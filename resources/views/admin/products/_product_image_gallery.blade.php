@if($product->images->count() > 0)
    <div class="product-image-gallery">
        @foreach($product->images as $image)
            <div
            id="product-image-item-{{ $image->id }}"
            class="product-image-item {{ $image->is_main ? 'main' : '' }} {{ $image->is_secondary ? 'secondary' : '' }}">
            <img src="{{ asset('storage/' . $image->medium) }}" alt="Product Image">
            <div class="position-absolute top-0 right-0 p-2">
                @if($image->is_main)
                    <span class="badge badge-success">Main</span>
                @endif
                @if($image->is_secondary)
                    <span class="badge badge-info">Secondary</span>
                @endif
            </div>
            @if($image->color || $image->size)
                <div class="position-absolute bottom-0 start-0 end-0 bg-dark text-white p-1 text-center"
                    style="font-size: 0.7rem; background: rgba(0,0,0,0.7) !important;">
                    @if($image->color) 
                        <span class="badge badge-info mr-1">{{ $image->color }}</span>
                    @endif
                    @if($image->size) 
                        <span class="badge badge-warning">{{ $image->size }}</span>
                    @endif
                </div>
            @endif
            <div class="position-absolute top-0 start-0 p-2 d-flex flex-column" style="gap: 0.25rem;">
                <button type="button" class="btn btn-sm btn-icon btn-light btn-hover-primary"
                    onclick="editImageAttributes({{ $image->id }}, '{{ asset('storage/' . $image->medium) }}', '{{ $image->color ?? '' }}', '{{ $image->size ?? '' }}', {{ $image->is_main ? 'true' : 'false' }}, {{ $image->is_secondary ? 'true' : 'false' }})"
                    title="Edit Attributes">
                    <i class="flaticon2-edit"></i>
                </button>
                @if(!$image->is_main)
                    <button type="button" class="btn btn-sm btn-icon btn-light btn-hover-success"
                        onclick="setMainImage({{ $image->id }})"
                        title="Set as Main">
                        <i class="flaticon2-check-mark"></i>
                    </button>
                @endif
                @if(!$image->is_secondary)
                    <button type="button" class="btn btn-sm btn-icon btn-light btn-hover-info"
                        onclick="setSecondaryImage({{ $image->id }})"
                        title="Set as Secondary">
                        <i class="flaticon2-star"></i>
                    </button>
                @endif
                <button type="button" class="btn btn-sm btn-icon btn-light btn-hover-danger"
                    onclick="deleteImage({{ $image->id }})"
                    title="Delete">
                    <i class="flaticon2-trash"></i>
                </button>
            </div>
        </div>
        @endforeach
    </div>
@else
    <p class="text-muted">No images uploaded yet</p>
@endif
