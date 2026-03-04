<div class="category-item-wrapper" data-category-id="{{ $category->id }}">
    <div class="category-item">
        <div class="category-header">
            <div class="d-flex align-items-center" style="flex: 1;">
                <span class="drag-handle" style="pointer-events: auto;">
                    <i class="flaticon2-menu-vertical"></i>
                </span>
                <div class="category-info">
                    <div class="category-name">{{ $category->name }}</div>
                    <div class="category-meta">
                        <span>Slug: {{ $category->slug }}</span>
                        @if($category->parent)
                            <span class="mx-2">|</span>
                            <span>Parent: {{ $category->parent->name }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="category-badges">
                @if($category->status)
                    <span class="badge badge-success">Active</span>
                @else
                    <span class="badge badge-danger">Inactive</span>
                @endif
                @if($category->children_count > 0)
                    <span class="badge badge-light-primary">{{ $category->children_count }} children</span>
                @endif
            </div>
            <div class="category-actions">
                <a href="{{ route('admin.categories.edit', $category) }}" 
                   class="btn btn-icon btn-light btn-hover-primary btn-sm" 
                   data-toggle="tooltip" 
                   title="Edit">
                    <i class="flaticon2-edit"></i>
                </a>
                <button type="button" 
                        class="btn btn-icon btn-light btn-hover-danger btn-sm" 
                        data-toggle="tooltip" 
                        title="Delete"
                        onclick="deleteCategory({{ $category->id }}, '{{ addslashes($category->name) }}', {{ $category->category_products()->count() }}, {{ $category->children_count }})">
                    <i class="flaticon2-trash"></i>
                </button>
            </div>
        </div>
    </div>
    
    <div class="category-children sortable-container" data-parent-id="{{ $category->id }}">
        @foreach($category->children as $child)
            @include('admin.categories._category_item', ['category' => $child, 'level' => $level + 1])
        @endforeach
    </div>
</div>

