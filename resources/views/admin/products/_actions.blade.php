<div class="d-flex justify-content-end flex-wrap">
    <a href="{{ route('admin.products.show', $product->ref_code) }}" 
       class="btn btn-icon btn-light btn-hover-primary btn-sm mr-1" 
       data-toggle="tooltip" 
       title="View Product">
        <i class="flaticon-eye"></i>
    </a>
    <a href="{{ route('admin.products.edit', $product->ref_code) }}" 
       class="btn btn-icon btn-light btn-hover-warning btn-sm mr-1" 
       data-toggle="tooltip" 
       title="Edit Product">
        <i class="flaticon2-edit"></i>
    </a>
</div>

