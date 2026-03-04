@extends('admin.layout.app')

@push('style')
    <style>
        .product-image-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 1rem;
        }
        .product-image-item {
            position: relative;
            border: 2px solid #e4e6ef;
            border-radius: 0.5rem;
            overflow: hidden;
        }
        .product-image-item.main {
            border-color: #3699FF;
        }
        .product-image-item.secondary {
            border-color: #17a2b8;
        }
        .product-image-item.main.secondary {
            border-color: #3699FF;
            border-left-color: #17a2b8;
            border-right-color: #17a2b8;
        }
        .product-image-item img {
            width: 100%;
            height: auto;
            max-height: 200px;
            object-fit: contain;
            display: block;
            background: #f3f6f9;
        }
        .variation-table {
            font-size: 0.9rem;
        }
    </style>
@endpush

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-2">
                <h5 class="text-dark font-weight-bold my-1 mr-5">Product Details</h5>
                <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.home') }}" class="text-muted">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.products.index') }}" class="text-muted">Products</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="text-muted">{{ $product->ref_code }}</span>
                    </li>
                </ul>
            </div>
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.products.index') }}" class="btn btn-light font-weight-bold mr-2">
                    <i class="flaticon2-left-arrow-1"></i> Back
                </a>
                <a href="{{ route('admin.products.edit', $product->ref_code) }}" class="btn btn-primary font-weight-bold">
                    <i class="flaticon2-edit"></i> Edit
                </a>
                <a target="_blank" href="{{ config('app.frontend_url').'/en/products/'.$product->ref_code.'/'.arabic_slug($product->translate('en')->name) }}" class="btn btn-warning font-weight-bold mr-2">
                    Product page in website
                </a>
            </div>
        </div>
    </div>
    <!--end::Subheader-->

    <div class="d-flex flex-column-fluid">
        <div class="container">
            @includeIf('admin.component.alert')

            <div class="card card-custom">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">{{ $product->translate('en')->name }}</h3>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-line" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#main_info">
                                <span class="nav-icon">
                                    <i class="flaticon2-info"></i>
                                </span>
                                <span class="nav-text">Main Product Info</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#product_details">
                                <span class="nav-icon">
                                    <i class="flaticon2-file"></i>
                                </span>
                                <span class="nav-text">Product Details</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#product_variations">
                                <span class="nav-icon">
                                    <i class="flaticon2-list"></i>
                                </span>
                                <span class="nav-text">Product Variations</span>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content mt-5">
                        <!--begin::Main Info Tab-->
                        <div class="tab-pane fade show active" id="main_info" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6 mb-5">
                                    <label class="font-weight-bold text-dark">Ref Code:</label>
                                    <p class="text-muted">{{ $product->ref_code }}</p>
                                </div>
                                <div class="col-md-6 mb-5">
                                    <label class="font-weight-bold text-dark">Name (EN):</label>
                                    <p class="text-muted">{{ optional($product->translate('en'))->name }}</p>
                                </div>
                                <div class="col-md-6 mb-5">
                                    <label class="font-weight-bold text-dark">Name (AR):</label>
                                    <p class="text-muted">{{ optional($product->translate('ar'))->name }}</p>
                                </div>
                                <div class="col-md-6 mb-5">
                                    <label class="font-weight-bold text-dark">Brand (EN):</label>
                                    <p class="text-muted">{{ $product->translate('en')->brand ?? '-' }}</p>
                                </div>    
                                <div class="col-md-6 mb-5">
                                    <label class="font-weight-bold text-dark">Brand (AR):</label>
                                    <p class="text-muted">{{ optional($product->translate('ar'))->brand ?? '-' }}</p>
                                </div>
                                <div class="col-md-6 mb-5">
                                    <label class="font-weight-bold text-dark">Last Synced:</label>
                                    <p class="text-muted">
                                        {{ $product->last_synced_at ? $product->last_synced_at->diffForHumans() : 'Never' }}
                                    </p>
                                </div>
                                <div class="col-md-6 mb-5">
                                    <label class="font-weight-bold text-dark">Status:</label>
                                    <p class="text-muted">
                                        {!! $product->status ? 
                                        '<span class="badge badge-success">Active</span>' : 
                                        '<span class="badge badge-danger">Inactive</span>' !!}
                                    </p>
                                </div>
                                <div class="col-md-12 mb-5">
                                    <label class="font-weight-bold text-dark">Categories:</label>
                                    <div class="mt-2">
                                        @if($product->categories->count() > 0)
                                            @foreach($product->categories as $category)
                                                <span class="badge badge-primary mr-2 mb-2">
                                                    {{ optional($category->translate('en'))->name ?? $category->name }}
                                                    @if($category->translate('ar'))
                                                        / {{ optional($category->translate('ar'))->name }}
                                                    @endif
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">No categories assigned</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="mb-10">
                                <label class="font-weight-bold text-dark mb-3 d-block">Main Image:</label>
                                @php
                                    $mainImage = $product->images()->where('is_main', true)->first();
                                @endphp
                                @if($mainImage)
                                    <div class="symbol symbol-200">
                                        <img src="{{ asset('storage/' . $mainImage->medium) }}" alt="Main Image" class="img-fluid">
                                    </div>
                                @else
                                    <p class="text-muted">No main image set</p>
                                @endif
                            </div>

                            <div class="mb-10">
                                <label class="font-weight-bold text-dark mb-3">Product Images Gallery:</label>
                                @if($product->images->count() > 0)
                                    <div class="product-image-gallery">
                                        @foreach($product->images as $image)
                                            <div class="product-image-item {{ $image->is_main ? 'main' : '' }} {{ $image->is_secondary ? 'secondary' : '' }}">
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
                                                    <div class="position-absolute bottom-0 start-0 end-0 bg-dark text-white p-1 text-center" style="font-size: 0.7rem;">
                                                        @if($image->color) Color: {{ $image->color }} @endif
                                                        @if($image->size) Size: {{ $image->size }} @endif
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted">No images uploaded yet</p>
                                @endif
                            </div>
                        </div>
                        <!--end::Main Info Tab-->

                        <!--begin::Product Details Tab-->
                        <div class="tab-pane fade" id="product_details" role="tabpanel">
                            <div class="mb-5">
                                <label class="font-weight-bold text-dark">Description (EN):</label>
                                <div class="text-muted">
                                    {!! $product->translate('en')->description ?? 'No description' !!}
                                </div>
                            </div>
                            <div class="mb-5">
                                <label class="font-weight-bold text-dark">Description (AR):</label>
                                <div class="text-muted">
                                    {!! $product->translate('ar')->description ?? 'No description' !!}
                                </div>
                            </div>
                            <div class="mb-5">
                                <label class="font-weight-bold text-dark">Short Description (EN):</label>
                                <div class="text-muted">
                                    {{ $product->translate('en')->short_description ?? '-' }}
                                </div>
                            </div>
                            <div class="mb-5">
                                <label class="font-weight-bold text-dark">Short Description (AR):</label>
                                <div class="text-muted">
                                    {{ $product->translate('ar')->short_description ?? '-' }}
                                </div>
                            </div>
                            <div class="mb-5">
                                <label class="font-weight-bold text-dark">Details (EN):</label>
                                <div class="text-muted">
                                    {!! $product->translate('en')->details ?? 'No details' !!}
                                </div>
                            </div>
                            <div class="mb-5">
                                <label class="font-weight-bold text-dark">Details (AR):</label>
                                <div class="text-muted">
                                    {!! $product->translate('ar')->details ?? 'No details' !!}
                                </div>
                            </div>
                        </div>
                        <!--end::Product Details Tab-->

                        <!--begin::Product Variations Tab-->
                        <div class="tab-pane fade" id="product_variations" role="tabpanel">
                            @if(count($variations) > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover variation-table">
                                        <thead>
                                            <tr>
                                                <th>Color</th>
                                                <th>Size</th>
                                                <th>Price</th>
                                                <th>Qty</th>
                                                <th>Offer Price</th>
                                                <th>Offer Type</th>
                                                <th>Image</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($variations as $variation)
                                                @php
                                                    $variationImage = $product->images()
                                                        ->where('color', $variation['color_code'])
                                                        ->where('size', $variation['size_code'])
                                                        ->first();
                                                @endphp
                                                <tr>
                                                    <td>{{ $variation['color_desc'] ?? '-' }}</td>
                                                    <td>{{ $variation['size_code'] ?? '-' }}</td>
                                                    <td>${{ number_format($variation['price'] ?? 0, 2) }}</td>
                                                    <td>{{ $variation['qty'] ?? 0 }}</td>
                                                    <td>
                                                        @if($variation['offer_price'])
                                                            ${{ number_format($variation['offer_price'], 2) }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>{{ $variation['offer_type'] ?? '-' }}</td>
                                                    <td>
                                                        @if($variationImage)
                                                            <img src="{{ asset('storage/' . $variationImage->medium) }}" 
                                                                 alt="Variation Image" 
                                                                 style="width: 60px; height: 60px; object-fit: cover;">
                                                        @else
                                                            <span class="text-muted">No image</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">No variations found for this product.</p>
                            @endif
                        </div>
                        <!--end::Product Variations Tab-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@endpush

