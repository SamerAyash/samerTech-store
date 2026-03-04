<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductTypeController extends Controller
{
    public function index()
    {
        $types = ProductType::with('templateAttributes')->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $types,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:100', 'unique:product_types,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'attributes' => ['nullable', 'array'],
            'attributes.*.code' => ['required_with:attributes', 'string', 'max:100'],
            'attributes.*.name' => ['required_with:attributes', 'string', 'max:255'],
            'attributes.*.input_type' => ['nullable', 'string', 'max:50'],
            'attributes.*.is_required' => ['nullable', 'boolean'],
            'attributes.*.is_variant_axis' => ['nullable', 'boolean'],
            'attributes.*.options' => ['nullable', 'array'],
            'attributes.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $type = DB::transaction(function () use ($validated) {
            $type = ProductType::create([
                'code' => $validated['code'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            foreach ($validated['attributes'] ?? [] as $attribute) {
                $type->templateAttributes()->create([
                    'code' => $attribute['code'],
                    'name' => $attribute['name'],
                    'input_type' => $attribute['input_type'] ?? 'text',
                    'is_required' => $attribute['is_required'] ?? false,
                    'is_variant_axis' => $attribute['is_variant_axis'] ?? false,
                    'options' => $attribute['options'] ?? null,
                    'sort_order' => $attribute['sort_order'] ?? 0,
                ]);
            }

            return $type->load('templateAttributes');
        });

        return response()->json([
            'success' => true,
            'data' => $type,
        ], 201);
    }

    public function update(Request $request, ProductType $productType)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:100', 'unique:product_types,code,' . $productType->id],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'attributes' => ['nullable', 'array'],
            'attributes.*.id' => ['nullable', 'integer'],
            'attributes.*.code' => ['required_with:attributes', 'string', 'max:100'],
            'attributes.*.name' => ['required_with:attributes', 'string', 'max:255'],
            'attributes.*.input_type' => ['nullable', 'string', 'max:50'],
            'attributes.*.is_required' => ['nullable', 'boolean'],
            'attributes.*.is_variant_axis' => ['nullable', 'boolean'],
            'attributes.*.options' => ['nullable', 'array'],
            'attributes.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $type = DB::transaction(function () use ($productType, $validated) {
            $productType->update([
                'code' => $validated['code'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            $attributeIds = collect($validated['attributes'] ?? [])
                ->pluck('id')
                ->filter()
                ->map(fn ($id) => (int) $id)
                ->values();

            $productType->templateAttributes()->whereNotIn('id', $attributeIds)->delete();

            foreach ($validated['attributes'] ?? [] as $attribute) {
                $productType->templateAttributes()->updateOrCreate(
                    ['id' => $attribute['id'] ?? null],
                    [
                        'code' => $attribute['code'],
                        'name' => $attribute['name'],
                        'input_type' => $attribute['input_type'] ?? 'text',
                        'is_required' => $attribute['is_required'] ?? false,
                        'is_variant_axis' => $attribute['is_variant_axis'] ?? false,
                        'options' => $attribute['options'] ?? null,
                        'sort_order' => $attribute['sort_order'] ?? 0,
                    ]
                );
            }

            return $productType->fresh('templateAttributes');
        });

        return response()->json([
            'success' => true,
            'data' => $type,
        ]);
    }

    public function destroy(ProductType $productType)
    {
        $productType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product type deleted successfully.',
        ]);
    }
}
