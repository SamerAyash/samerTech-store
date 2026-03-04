@extends('admin.layout.app')

@push('style')
    <style>
        .product-image-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 1.5rem;
        }
        .product-image-item {
            position: relative;
            border: 2px solid #e4e6ef;
            border-radius: 0.5rem;
            overflow: hidden;
            background: #f3f6f9;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .product-image-item:hover {
            border-color: #3699FF;
            box-shadow: 0 0.5rem 1rem rgba(54, 153, 255, 0.15);
            transform: translateY(-2px);
        }
        .product-image-item.main {
            border-color: #3699FF;
            border-width: 3px;
            box-shadow: 0 0 0 3px rgba(54, 153, 255, 0.1);
        }
        .product-image-item.secondary {
            border-color: #17a2b8;
            border-width: 3px;
            box-shadow: 0 0 0 3px rgba(23, 162, 184, 0.1);
        }
        .product-image-item.main.secondary {
            border-color: #3699FF;
            border-left-color: #17a2b8;
            border-right-color: #17a2b8;
        }
        .product-image-item img {
            width: 100%;
            height: auto;
            max-height: 250px;
            object-fit: contain;
            display: block;
            background: #f3f6f9;
        }
        .product-image-item .position-absolute {
            z-index: 10;
        }
        #selectedFilesPreview ul {
            list-style: none;
            padding-left: 0;
        }
        #selectedFilesPreview ul li {
            padding: 0.25rem 0;
        }
        #modalImagePreview {
            border: 2px solid #e4e6ef;
            padding: 0.5rem;
            background: #f3f6f9;
        }
    </style>
@endpush
@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-2">
                <h5 class="text-dark font-weight-bold my-1 mr-5">Edit Product</h5>
                <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.home') }}" class="text-muted">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.products.index') }}" class="text-muted">Products</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.products.show', $product->ref_code) }}" class="text-muted">{{ $product->ref_code }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="text-muted">Edit</span>
                    </li>
                </ul>
            </div>
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.products.show', $product->ref_code) }}" class="btn btn-light font-weight-bold">
                    <i class="flaticon2-left-arrow-1"></i> Back
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

            <form action="{{ route('admin.products.update', $product->ref_code) }}" method="POST" id="productForm">
                @csrf
                @method('PUT')

                <div class="card card-custom">
                    <div class="card-header">
                        <div class="card-title">
                            <h3 class="card-label">Product Information</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Ref Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-solid" value="{{ $product->ref_code }}" disabled>
                                    <span class="form-text text-muted">Ref code cannot be changed.</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control form-control-solid @error('status') is-invalid @enderror" required>
                                        <option value="1" {{ old('status', $product->status) == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status', $product->status) == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                                @error('status')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="font-weight-bold">Categories</label>
                                    <select name="categories[]" id="product_categories" class="form-control form-control-solid @error('categories') is-invalid @enderror" multiple>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" 
                                                {{ in_array($category->id, old('categories', $product->categories->pluck('id')->toArray())) ? 'selected' : '' }}>
                                                {{ optional($category->translate('en'))->name ?? $category->name }}
                                                @if($category->translate('ar'))
                                                    / {{ $category->translate('ar')->name }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="form-text text-muted">Select one or more categories for this product. You can search and select multiple categories.</span>
                                </div>
                                @error('categories')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Name (EN)<span class="text-danger">*</span></label>
                                    <input type="text" name="en_name" 
                                           class="form-control form-control-solid @error('en_name') is-invalid @enderror" 
                                           value="{{ old('en_name', optional($product->translate('en'))->name) }}" 
                                           required>
                                    @error('en_name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Name (AR)<span class="text-danger">*</span></label>
                                    <input type="text" name="ar_name" 
                                           class="text-right form-control form-control-solid @error('ar_name') is-invalid @enderror" 
                                           value="{{ old('ar_name', optional($product->translate('ar'))->name) }}" 
                                           required>
                                    @error('ar_name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Brand (EN)</label>
                                    <input type="text" name="en_brand" 
                                           class="form-control form-control-solid @error('en_brand') is-invalid @enderror" 
                                           value="{{ old('en_brand', optional($product->translate('en'))->brand) }}">
                                    @error('en_brand')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Brand (AR)</label>
                                    <input type="text" name="ar_brand" 
                                           class="text-right form-control form-control-solid @error('ar_brand') is-invalid @enderror" 
                                           value="{{ old('ar_brand', optional($product->translate('ar'))->brand) }}">
                                    @error('ar_brand')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Short Description (EN)</label>
                                    <input type="text" name="en_short_description" 
                                           class="form-control form-control-solid @error('en_short_description') is-invalid @enderror" 
                                           value="{{ old('en_short_description', optional($product->translate('en'))->short_description) }}">
                                    @error('en_short_description')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Short Description (AR)</label>
                                    <input type="text" name="ar_short_description" 
                                           class="text-right form-control form-control-solid @error('ar_short_description') is-invalid @enderror" 
                                           value="{{ old('ar_short_description', optional($product->translate('ar'))->short_description) }}">
                                    @error('ar_short_description')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>

                <div class="card card-custom mt-5">
                    <div class="card-header">
                        <div class="card-title">
                            <h3 class="card-label">Product Details</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="font-weight-bold">Description (EN)</label>
                            <div id="en_description_editor" style="height: 300px;">
                                {!! old('en_description', optional($product->translate('en'))->description) !!}
                            </div>
                            <textarea name="en_description" id="en_description" style="display: none;" class="@error('en_description') is-invalid @enderror">{{ old('en_description', optional($product->translate('en'))->description) }}</textarea>
                            @error('en_description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Details (EN)</label>
                            <div id="en_details_editor" style="height: 300px;">
                                {!! old('en_details', optional($product->translate('en'))->details) !!}
                            </div>
                            <textarea name="en_details" id="en_details" style="display: none;" class="@error('en_details') is-invalid @enderror">{{ old('en_details', optional($product->translate('en'))->details) }}</textarea>
                            @error('en_details')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <hr>
                        <div class="form-group">
                            <label class="font-weight-bold">Description (AR)</label>
                            <div id="ar_description_editor" style="height: 300px;" class="text-right">
                                {!! old('ar_description', optional($product->translate('ar'))->description) !!}
                            </div>
                            <textarea name="ar_description" id="ar_description" style="display: none;" class="@error('ar_description') is-invalid @enderror">{{ old('ar_description', optional($product->translate('ar'))->description) }}</textarea>
                            @error('ar_description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Details (AR)</label>
                            <div id="ar_details_editor" style="height: 300px;" class="text-right">
                                {!! old('ar_details', optional($product->translate('ar'))->details) !!}
                            </div>
                            <textarea name="ar_details" id="ar_details" style="display: none;" class="@error('ar_details') is-invalid @enderror">{{ old('ar_details', optional($product->translate('ar'))->details) }}</textarea>
                            @error('ar_details')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                    <button type="submit" id="updateProductBtn" class="btn btn-primary font-weight-bold mr-2">
                        <i class="flaticon2-check-mark"></i> Update Product
                    </button>
                    <a href="{{ route('admin.products.show', $product->ref_code) }}" class="btn btn-light-primary font-weight-bold">
                        Cancel
                    </a>
                    </div>
                </div>
            </form>

            <div class="card card-custom mt-5">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">Product Images</h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-5">
                        <h5 class="font-weight-bold mb-3">Upload Multiple Images</h5>
                        <form id="uploadImageForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Images <span class="text-danger">*</span></label>
                                        <div class="custom-file">
                                            <input type="file" name="images[]" id="imagesInput" 
                                                    class="custom-file-input" 
                                                    accept="image/*" multiple required>
                                            <label class="custom-file-label" for="imagesInput">Choose multiple images...</label>
                                        </div>
                                        <span class="form-text text-muted">You can select multiple images at once. After upload, you can set main image and link colors/sizes.</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div id="selectedFilesPreview" class="mt-2"></div>
                                    </div>
                                </div>
                            </div>
                            <button id="uploadImageBtn" type="submit" class="btn btn-primary">
                                <i class="flaticon2-upload"></i> <span id="uploadBtnText">Upload Images</span>
                            </button>
                            <div id="uploadProgress" class="mt-3" style="display: none;">
                                <div class="progress">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%"></div>
                                </div>
                                <span class="form-text text-muted">Uploading images, please wait...</span>
                            </div>
                        </form>
                    </div>

                    <div class="mb-5">
                        <h5 class="font-weight-bold mb-3">Existing Images</h5>
                        <div id="product-images">
                            @includeIf('admin.products._product_image_gallery', ['product' => $product])
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal for setting image attributes -->
            <div class="modal fade" id="imageAttributesModal" tabindex="-1" role="dialog" aria-labelledby="imageAttributesModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="imageAttributesModalLabel">Configure Image Attributes</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row mb-5">
                                <div class="col-md-12 text-center">
                                    <img id="modalImagePreview" src="" alt="Preview" class="img-fluid rounded" style="max-height: 300px;">
                                </div>
                            </div>
                            <form id="imageAttributesForm">
                                @csrf
                                <input type="hidden" id="modalImageId" name="image_id">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Color (Optional)</label>
                                            <select name="color" id="modalColorSelect" class="form-control form-control-solid">
                                                <option value="">-- No Color --</option>
                                            </select>
                                            <span class="form-text text-muted">Link this image to a specific color</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Size (Optional)</label>
                                            <select name="size" id="modalSizeSelect" class="form-control form-control-solid">
                                                <option value="">-- No Size --</option>
                                            </select>
                                            <span class="form-text text-muted">Link this image to a specific size</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox-inline">
                                                <label class="checkbox checkbox-primary">
                                                    <input type="checkbox" name="is_main" id="modalIsMain" value="1">
                                                    <span></span>Set as Main Image
                                                </label>
                                            </div>
                                            <span class="form-text text-muted">The main image will be displayed as the primary product image</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox-inline">
                                                <label class="checkbox checkbox-info">
                                                    <input type="checkbox" name="is_secondary" id="modalIsSecondary" value="1">
                                                    <span></span>Set as Secondary Image
                                                </label>
                                            </div>
                                            <span class="form-text text-muted">The secondary image will be displayed as the secondary product image</span>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary font-weight-bold" id="saveImageAttributesBtn">
                                <i class="flaticon2-check-mark"></i> Save Attributes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset("cp_assets/js/sweetalert2@11.js") }}"></script>
    <script src="{{ asset('cp_assets/plugins/custom/select2/select2.bundle.js') }}"></script>
    <script>
        // Initialize Select2 for categories multiselect
        jQuery(document).ready(function() {
            $('#product_categories').select2({
                placeholder: 'Select categories...',
                allowClear: true,
                width: '100%'
            });

            // Initialize Quill editors
            var enDescriptionQuill = new Quill('#en_description_editor', {
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'align': [] }],
                        ['link', 'image'],
                        ['clean']
                    ]
                },
                theme: 'snow'
            });

            var enDetailsQuill = new Quill('#en_details_editor', {
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'align': [] }],
                        ['link', 'image'],
                        ['clean']
                    ]
                },
                theme: 'snow'
            });

            var arDescriptionQuill = new Quill('#ar_description_editor', {
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'align': [] }],
                        ['link', 'image'],
                        ['clean']
                    ]
                },
                theme: 'snow'
            });

            var arDetailsQuill = new Quill('#ar_details_editor', {
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'align': [] }],
                        ['link', 'image'],
                        ['clean']
                    ]
                },
                theme: 'snow'
            });

            // Update hidden textareas before form submission
            $('#productForm').on('submit', function() {
                $('#en_description').val(enDescriptionQuill.root.innerHTML);
                $('#en_details').val(enDetailsQuill.root.innerHTML);
                $('#ar_description').val(arDescriptionQuill.root.innerHTML);
                $('#ar_details').val(arDetailsQuill.root.innerHTML);
            });
        });
    </script>
    <script>

        // Update product data via AJAX
        /*$('#productForm').on('submit', function(e) {
            e.preventDefault();
            
            var formData = $(this).serialize();
            var submitBtn = $('#updateProductBtn');
            var originalText = submitBtn.html();
            
            // Disable button and show loading
            submitBtn.prop('disabled', true).html('<i class="flaticon2-loading"></i> Updating...');
            
            $.ajax({
                url: $(this).attr('action'),
                type: 'PUT',
                data: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Updated!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-primary font-weight-bold'
                            },
                            buttonsStyling: false
                        }).then(() => {
                            // Optionally reload or update UI
                            // location.reload();
                        });
                    }
                },
                error: function(xhr) {
                    var errorMessage = 'Something went wrong.';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON.errors) {
                            var errors = [];
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                errors.push(value[0]);
                            });
                            errorMessage = errors.join('<br>');
                        }
                    }
                    Swal.fire({
                        title: 'Error!',
                        html: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-primary font-weight-bold'
                        },
                        buttonsStyling: false
                    });
                },
                complete: function() {
                    // Re-enable button
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });**/

        // Show selected files preview
        $('#imagesInput').on('change', function() {
            var files = this.files;
            var preview = $('#selectedFilesPreview');
            preview.html('');
            
            if (files.length > 0) {
                var fileList = $('<div class="alert alert-info"><strong>Selected ' + files.length + ' file(s):</strong><ul class="mb-0 mt-2"></ul></div>');
                var ul = fileList.find('ul');
                
                for (var i = 0; i < files.length; i++) {
                    ul.append('<li>' + files[i].name + ' (' + (files[i].size / 1024).toFixed(2) + ' KB)</li>');
                }
                
                preview.html(fileList);
            }
        });

        // Update custom file input label
        $('#imagesInput').on('change', function() {
            var files = this.files;
            var label = $(this).next('.custom-file-label');
            if (files.length > 0) {
                if (files.length === 1) {
                    label.text(files[0].name);
                } else {
                    label.text(files.length + ' files selected');
                }
            } else {
                label.text('Choose multiple images...');
            }
        });

        // Load product attributes (colors and sizes)
        var productColors = {};
        var productSizes = {};
        
        function loadProductAttributes() {
            $.ajax({
                url: "{{ route('admin.products.getAttributes', $product->ref_code) }}",
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        productColors = response.colors;
                        productSizes = response.sizes;
                        
                        // Populate color select
                        var colorSelect = $('#modalColorSelect');
                        colorSelect.find('option:not(:first)').remove();
                        $.each(productColors, function(code, desc) {
                            colorSelect.append($('<option>', {
                                value: code,
                                text: desc
                            }));
                        });
                        
                        // Populate size select
                        var sizeSelect = $('#modalSizeSelect');
                        sizeSelect.find('option:not(:first)').remove();
                        $.each(productSizes, function(code, desc) {
                            sizeSelect.append($('<option>', {
                                value: code,
                                text: desc
                            }));
                        });
                    }
                }
            });
        }
        
        // Load attributes on page load
        loadProductAttributes();

        // Upload images
        $('#uploadImageForm').on('submit', function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            var submitBtn = $('#uploadImageBtn');
            var uploadBtnText = $('#uploadBtnText');
            var uploadProgress = $('#uploadProgress');
            var originalText = uploadBtnText.text();
            
            submitBtn.prop('disabled', true);
            uploadBtnText.html('<i class="flaticon2-loading spinner spinner-sm"></i> Uploading...');
            uploadProgress.show();
            
            $.ajax({
                url: "{{ route('admin.products.uploadImage', $product->ref_code) }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#uploadImageForm')[0].reset();
                        $('#selectedFilesPreview').html('');
                        $('#imagesInput').next('.custom-file-label').text('Choose multiple images...');
                        
                        // Update gallery
                        $('#product-images').html(response.gallery);
                        
                        // Show uploaded images and open modal for each
                        if (response.images && response.images.length > 0) {
                            var uploadedCount = response.images.length;
                            Swal.fire({
                                title: 'Uploaded!',
                                text: uploadedCount + ' image(s) uploaded successfully. Please configure attributes for each image.',
                                icon: 'success',
                                confirmButtonText: 'OK',
                                customClass: {
                                    confirmButton: 'btn btn-primary font-weight-bold'
                                },
                                buttonsStyling: false
                            }).then(() => {
                                // Clean any existing backdrop before opening modal
                                removeModalBackdrop();
                                
                                // Open modal for first image
                                if (response.images.length > 0) {
                                    setTimeout(function() {
                                        openImageAttributesModal(response.images[0].id, response.images[0].medium_url, response.images);
                                    }, 100);
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Uploaded!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'OK',
                                customClass: {
                                    confirmButton: 'btn btn-primary font-weight-bold'
                                },
                                buttonsStyling: false
                            });
                        }
                    }
                },
                error: function(xhr) {
                    var errorMessage = 'Something went wrong.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        var errors = [];
                        $.each(xhr.responseJSON.errors, function(key, value) {
                            errors.push(value[0]);
                        });
                        errorMessage = errors.join('<br>');
                    }
                    Swal.fire({
                        title: 'Error!',
                        html: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-primary font-weight-bold'
                        },
                        buttonsStyling: false
                    });
                },
                complete: function() {
                    submitBtn.prop('disabled', false);
                    uploadBtnText.text(originalText);
                    uploadProgress.hide();
                }
            });
        });

        // Function to remove modal backdrop
        function removeModalBackdrop() {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            $('body').css('padding-right', '');
        }

        // Clean backdrop on page unload
        $(window).on('beforeunload', function() {
            removeModalBackdrop();
        });

        // Clean backdrop when clicking on modal backdrop
        $(document).on('click', '.modal-backdrop', function() {
            removeModalBackdrop();
        });

        // Clean backdrop when modal is hidden (additional safety)
        $(document).on('hidden.bs.modal', '#imageAttributesModal', function() {
            removeModalBackdrop();
        });

        // Open image attributes modal
        var pendingImages = [];
        function openImageAttributesModal(imageId, imageUrl, allImages) {
            // Clean up any existing backdrop first
            removeModalBackdrop();
            
            $('#modalImageId').val(imageId);
            $('#modalImagePreview').attr('src', imageUrl);
            $('#modalColorSelect').val('');
            $('#modalSizeSelect').val('');
            $('#modalIsMain').prop('checked', false);
            
            // Store remaining images
            if (allImages) {
                pendingImages = allImages.filter(function(img) {
                    return img.id != imageId;
                });
            }
            
            // Use one() to ensure modal events fire only once, then show
            $('#imageAttributesModal').one('hidden.bs.modal', function() {
                removeModalBackdrop();
            }).modal('show');
        }

        // Save image attributes
        $('#saveImageAttributesBtn').on('click', function() {
            var imageId = $('#modalImageId').val();
            var formData = {
                _token: '{{ csrf_token() }}',
                color: $('#modalColorSelect').val() || null,
                size: $('#modalSizeSelect').val() || null,
                is_main: $('#modalIsMain').is(':checked') ? 1 : 0,
                is_secondary: $('#modalIsSecondary').is(':checked') ? 1 : 0
            };
            
            var btn = $(this);
            var originalText = btn.html();
            btn.prop('disabled', true).html('<i class="flaticon2-loading"></i> Saving...');
            
            $.ajax({
                url: "{{ route('admin.products.updateImageAttributes', [$product->ref_code, ':id']) }}".replace(':id', imageId),
                type: 'PUT',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('#product-images').html(response.gallery);
                        
                        // If there are more pending images, open modal for next one
                        if (pendingImages.length > 0) {
                            var nextImage = pendingImages.shift();
                            
                            // Properly close current modal and clean backdrop
                            $('#imageAttributesModal').modal('hide');
                            
                            // Wait for modal to fully close before opening next one
                            $('#imageAttributesModal').one('hidden.bs.modal', function() {
                                removeModalBackdrop();
                                setTimeout(function() {
                                    openImageAttributesModal(nextImage.id, nextImage.medium_url, pendingImages);
                                }, 100);
                            });
                        } else {
                            // Close modal and clean backdrop
                            $('#imageAttributesModal').modal('hide');
                            $('#imageAttributesModal').one('hidden.bs.modal', function() {
                                removeModalBackdrop();
                            });
                            
                            Swal.fire({
                                title: 'Saved!',
                                text: 'Image attributes updated successfully.',
                                icon: 'success',
                                confirmButtonText: 'OK',
                                customClass: {
                                    confirmButton: 'btn btn-primary font-weight-bold'
                                },
                                buttonsStyling: false
                            });
                        }
                    }
                },
                error: function(xhr) {
                    var errorMessage = 'Something went wrong.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    // Clean backdrop on error
                    removeModalBackdrop();
                    
                    Swal.fire({
                        title: 'Error!',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-primary font-weight-bold'
                        },
                        buttonsStyling: false
                    });
                },
                complete: function() {
                    btn.prop('disabled', false).html(originalText);
                }
            });
        });

        // Track if modal was closed by save button or cancel
        var modalClosedBySave = false;

        // Track when save button is clicked
        $('#saveImageAttributesBtn').on('click', function() {
            modalClosedBySave = true;
        });

        // Allow skipping image configuration
        $('#imageAttributesModal').on('hidden.bs.modal', function() {
            // Clean backdrop when modal is closed
            removeModalBackdrop();
            
            // Only show prompt if modal was closed by cancel/dismiss, not by save
            if (!modalClosedBySave && pendingImages.length > 0) {
                Swal.fire({
                    title: 'Configure Remaining Images?',
                    text: 'You have ' + pendingImages.length + ' more image(s) to configure. Would you like to configure them now?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, configure',
                    cancelButtonText: 'Skip',
                    customClass: {
                        confirmButton: 'btn btn-primary font-weight-bold',
                        cancelButton: 'btn btn-light font-weight-bold'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed && pendingImages.length > 0) {
                        var nextImage = pendingImages.shift();
                        modalClosedBySave = false;
                        setTimeout(function() {
                            openImageAttributesModal(nextImage.id, nextImage.medium_url, pendingImages);
                        }, 100);
                    } else {
                        pendingImages = [];
                    }
                });
            }
            modalClosedBySave = false;
        });

        // Delete image
        function deleteImage(imageId) {
            Swal.fire({
                title: 'Delete Image?',
                text: 'Are you sure you want to delete this image?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-danger font-weight-bold',
                    cancelButton: 'btn btn-light font-weight-bold'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.products.deleteImage', [$product->ref_code, ':id']) }}"
                            .replace(':id', imageId),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#product-image-item-'+imageId).remove();
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: response.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        confirmButton: 'btn btn-primary font-weight-bold'
                                    },
                                    buttonsStyling: false
                                });
                            }
                        },
                        error: function(xhr) {
                            var errorMessage = 'Something went wrong.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                title: 'Error!',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonText: 'OK',
                                customClass: {
                                    confirmButton: 'btn btn-primary font-weight-bold'
                                },
                                buttonsStyling: false
                            });
                        }
                    });
                }
            });
        }

        // Edit image attributes from gallery
        function editImageAttributes(imageId, imageUrl, currentColor, currentSize, isMain, isSecondary) {
            // Clean up any existing backdrop first
            removeModalBackdrop();
            
            $('#modalImageId').val(imageId);
            $('#modalImagePreview').attr('src', imageUrl);
            $('#modalColorSelect').val(currentColor || '');
            $('#modalSizeSelect').val(currentSize || '');
            $('#modalIsMain').prop('checked', isMain);
            $('#modalIsSecondary').prop('checked', isSecondary || false);
            pendingImages = []; // Clear pending images when editing existing
            
            // Use one() to ensure modal events fire only once, then show
            $('#imageAttributesModal').one('hidden.bs.modal', function() {
                removeModalBackdrop();
            }).modal('show');
        }

        // Set main image
        function setMainImage(imageId) {
            $.ajax({
                url: "{{ route('admin.products.setMainImage', [$product->ref_code, ':id']) }}"
                    .replace(':id', imageId),
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#product-images').html(response.image);
                        Swal.fire({
                            title: 'Updated!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-primary font-weight-bold'
                            },
                            buttonsStyling: false
                        });
                    }
                },
                error: function(xhr) {
                    var errorMessage = 'Something went wrong.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        title: 'Error!',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-primary font-weight-bold'
                        },
                        buttonsStyling: false
                    });
                }
            });
        }

        // Set secondary image
        function setSecondaryImage(imageId) {
            $.ajax({
                url: "{{ route('admin.products.setSecondaryImage', [$product->ref_code, ':id']) }}"
                    .replace(':id', imageId),
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#product-images').html(response.image);
                        Swal.fire({
                            title: 'Updated!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-primary font-weight-bold'
                            },
                            buttonsStyling: false
                        });
                    }
                },
                error: function(xhr) {
                    var errorMessage = 'Something went wrong.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        title: 'Error!',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-primary font-weight-bold'
                        },
                        buttonsStyling: false
                    });
                }
            });
        }
    </script>
@endpush

