<div class="d-flex justify-content-end flex-wrap">
    <a href="{{ route('admin.categories.edit', $category) }}" 
       class="btn btn-icon btn-light btn-hover-primary btn-sm mr-1" 
       data-toggle="tooltip" 
       title="Edit">
        <i class="flaticon2-edit"></i>
    </a>
    <button type="button" 
            class="btn btn-icon btn-light btn-hover-danger btn-sm" 
            data-toggle="tooltip" 
            title="Delete"
            onclick="deleteCategory({{ $category->id }}, '{{ addslashes($category->name) }}', {{ $category->products_count ?? 0 }}, {{ $category->children_count ?? 0 }})">
        <i class="flaticon2-trash"></i>
    </button>
</div>

