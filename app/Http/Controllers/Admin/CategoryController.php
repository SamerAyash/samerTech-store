<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {    
        return view('admin.categories.index');
    }

    /**
     * Get categories data for DataTables (server-side processing).
     */
    public function data(Request $request)
    {
        $query = Category::with('parent')
            ->withCount(['children'])
            ->orderByDesc("created_at");
        return DataTables::of($query)
            ->filter(function ($query) use ($request) {
                // Handle global search
                if ($request->has('search') && !empty($request->search['value'])) {
                    $keyword = $request->search['value'];
                    $query->where(function ($q) use ($keyword) {
                        $q->whereHas('translations', function ($t) use ($keyword) {
                            $t->where('name', 'like', "%$keyword%");
                        });
                    });

                }
                // Handle column-specific search for name
                if ($request->has('columns')) {
                    foreach ($request->columns as $column) {
                        if (isset($column['data']) && $column['data'] == 'name' && 
                            isset($column['search']['value']) && !empty($column['search']['value'])) {
                            $query->whereTranslationLike('name', '%$column[search][value]%');
                        }
                    }
                }
            })
            ->addColumn('name', function ($category) {
                return '<div class="d-flex flex-column">
                    <span class="text-dark font-weight-bold">' . e(optional($category->translate('en'))->name) . '</span>
                    <span class="text-muted font-size-sm">Created ' . $category->created_at->diffForHumans() . '</span>
                </div>';
            })
            ->addColumn('slug', function ($category) {
                return '<div class="d-flex flex-column">
                    <span class="text-dark font-weight-bold">' . e(optional($category->translate('en'))->slug) . '</span>
                </div>';
            })
            ->addColumn('name_ar', function ($category) {
                return '<div class="d-flex flex-column">
                    <span class="text-dark font-weight-bold">' . e(optional($category->translate('ar'))->name) . '</span>
                </div>';
            })
            ->addColumn('slug_ar', function ($category) {
                return '<div class="d-flex flex-column">
                    <span class="text-dark font-weight-bold">' . e(optional($category->translate('ar'))->slug) . '</span>
                </div>';
            })
            ->addColumn('parent', function ($category) {
                if ($category->parent) {
                    return '<span class="badge badge-light-info">' . e(optional($category->parent->translate('en'))->name) . '</span>';
                }
                return '<span class="text-muted">Root</span>';
            })
            ->addColumn('children_count', function ($category) {
                return '<span class="badge badge-light-primary">' . $category->children_count . '</span>';
            })
            ->editColumn('status', function ($category) {
                $badge = $category->status 
                    ? '<span class="badge badge-success">Active</span>'
                    : '<span class="badge badge-danger">Inactive</span>';
                return $badge;
            })
            ->editColumn('created_at', function ($category) {
                return $category->created_at->format('M d, Y');
            })
            ->addColumn('actions', function ($category) {
                return view('admin.categories._actions', compact('category'))->render();
            })
            ->rawColumns(['name','slug' , 'name_ar', 'slug_ar', 'parent', 'children_count', 'status', 'active_navbar', 'actions'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $parents = Category::
        orderByTranslation('name', 'desc','en')
        ->get();

        return view('admin.categories.create', compact('parents'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'en_name' => ['required', 'string', 'max:255','different:ar_name',
            Rule::unique('category_translations', 'name')->where('locale', 'en')],
            'ar_name' => ['required', 'string', 'max:255','different:en_name',
            Rule::unique('category_translations', 'name')->where('locale', 'ar')],
            'status' => ['required', 'boolean'],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
            'active_navbar' => ['nullable', 'boolean'],
        ]);

        // Calculate slugs first before creating to avoid conflicts
        $slug_en = Category::generateUniqueSlug('en', $request->en_name);
        $slug_ar = Category::generateUniqueSlug('ar', $request->ar_name);

        $sort_order = 0;
        if ($request->parent_id != null) {
            $max_sort_order = Category::where('parent_id', $request->parent_id)
            ->max('sort_order');
            $sort_order = $max_sort_order ? $max_sort_order + 1 : $request->parent_id . 0;
        }
        else{
            $sort_order = Category::whereNull('parent_id')->max('sort_order') + 1;
        }

        $category = Category::create([
            'status' => $request->status,
            'parent_id' => $request->parent_id ?? null,
            'active_navbar' => $request->boolean('active_navbar'),
            'en' => ['name' => $request->en_name, 'slug' => $slug_en],
            'ar' => ['name' => $request->ar_name, 'slug' => $slug_ar],
            'sort_order' => $sort_order,
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $excludedIds = $this->collectDescendantIds($category);
        $excludedIds[] = $category->id;

        $category->loadCount('children');
        
        $parents = Category::whereNotIn('categories.id', $excludedIds)
            ->orderByTranslation('name', 'desc','en')
            ->get();

        return view('admin.categories.edit', compact('category', 'parents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'en_name' => ['required', 'string', 'max:255','different:ar_name',
            Rule::unique('category_translations', 'name')->where('locale', 'en')
            ->ignore(optional($category->translate('en'))->id)],
            'ar_name' => ['required', 'string', 'max:255','different:en_name',
            Rule::unique('category_translations', 'name')->where('locale', 'ar')
            ->ignore(optional($category->translate('ar'))->id)],
            'status' => ['required', 'boolean'],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
            'active_navbar' => ['nullable', 'boolean'],
        ]);

        $parentId = $request->input('parent_id');
        if ($parentId) {
            $this->ensureValidParent($category, $parentId);
        } else {
            $parentId = null;
        }

        // Calculate slugs first before updating to avoid conflicts
        $slug_en = Category::generateUniqueSlug('en', $request->en_name, $category->id);
        $slug_ar = Category::generateUniqueSlug('ar', $request->ar_name, $category->id);
        $sort_order= 0;
        if ($parentId != $category->parent_id) {
            $max_sort_order = Category::where('parent_id', $parentId)
            ->max('sort_order');
            $sort_order = $max_sort_order ? $max_sort_order + 1 : $parentId . 0;
        } else {
            $sort_order = $category->sort_order;
        }

        // Update main model attributes first
        $category->update([
            'status' => $request->status,
            'parent_id' => $parentId,
            'active_navbar' => $request->boolean('active_navbar'),
            'en' => ['name' => $request->en_name, 'slug' => $slug_en],
            'ar' => ['name' => $request->ar_name, 'slug' => $slug_ar],
            'sort_order' => $sort_order,
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $productsCount = $category->category_products()->count();
        $childrenCount = $category->children()->count();
        $categoryName = $category->name;

        // If category has children, move them to root (set parent_id to null)
        if ($childrenCount > 0) {
            $category->children()->update(['parent_id' => null]);
        }

        $category->delete();

        $message = 'Category "' . $categoryName . '" deleted successfully.';
        if ($productsCount > 0) {
            $category->category_products()->delete();
            $message .= ' ' . $productsCount . ' product(s) are now unassigned.';
        }
        if ($childrenCount > 0) {
            $message .= ' ' . $childrenCount . ' child category(ies) moved to root.';
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Ensure parent is valid (not self or descendant).
     */
    private function ensureValidParent(Category $category, int $parentId): void
    {
        if ($category->id === $parentId) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'parent_id' => 'A category cannot be its own parent.',
            ]);
        }

        if (in_array($parentId, $this->collectDescendantIds($category), true)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'parent_id' => 'A category cannot be assigned to one of its descendants.',
            ]);
        }
    }

    /**
     * Collect all descendant IDs recursively.
     */
    private function collectDescendantIds(Category $category): array
    {
        $ids = [];
        $category->loadMissing('children');

        foreach ($category->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $this->collectDescendantIds($child));
        }

        return array_values(array_unique($ids));
    }
    /**
     * Display page for sorting parent categories only
     */
    public function sort()
    {
        // Get only root categories (parent_id is null)
        $categories = Category::whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        return view('admin.categories.sort', compact('categories'));
    }

    /**
     * Display page for selecting parent category to sort its children
     */
    public function sortChildren()
    {
        // Get all parent categories (root categories)
        $parents = Category::whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        return view('admin.categories.sort-children', compact('parents'));
    }

    /**
     * Display page for sorting children of a specific parent
     */
    public function sortChildrenOf($parentId)
    {
        $parent = Category::findOrFail($parentId);
        
        // Get children of this parent
        $children = Category::where('parent_id', $parentId)
            ->orderBy('sort_order')
            ->get();

        return view('admin.categories.sort-children-list', compact('parent', 'children'));
    }
    public function updateOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:categories,id',
            'items.*.parent_id' => 'nullable|exists:categories,id',
            'items.*.sort_order' => 'required|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            $items = $request->input('items');
            
            foreach ($items as $item) {
                $sort_order = ($item['parent_id'] ?? 0) . $item['sort_order'];
                Category::where('id', $item['id'])->update([
                    'parent_id' => $item['parent_id'],
                    'sort_order' => $sort_order,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Category order updated successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update category order: ' . $e->getMessage()
            ], 500);
        }
    }
}
