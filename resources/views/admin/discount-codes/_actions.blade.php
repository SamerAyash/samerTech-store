<div class="d-flex justify-content-end flex-wrap">
    <a href="{{ route('admin.discount-codes.show', $code) }}" 
       class="btn btn-icon btn-light btn-hover-primary btn-sm mr-1" 
       data-toggle="tooltip" 
       title="View Details">
        <i class="flaticon-eye"></i>
    </a>
    <a href="{{ route('admin.discount-codes.edit', $code) }}" 
       class="btn btn-icon btn-light btn-hover-info btn-sm mr-1" 
       data-toggle="tooltip" 
       title="Edit">
        <i class="flaticon2-edit"></i>
    </a>
    <button type="button" 
            class="btn btn-icon btn-light btn-hover-warning btn-sm mr-1" 
            data-toggle="tooltip" 
            title="Toggle Status"
            onclick="toggleStatus({{ $code->id }})">
        <i class="flaticon2-protection"></i>
    </button>
    <button type="button" 
            class="btn btn-icon btn-light btn-hover-danger btn-sm" 
            data-toggle="tooltip" 
            title="Delete"
            onclick="deleteDiscountCode({{ $code->id }})">
        <i class="flaticon2-trash"></i>
    </button>
</div>

