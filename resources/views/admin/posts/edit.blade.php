@extends('admin.layout.app')

@push('style')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link href="{{ asset('cp_assets/plugins/custom/select2/select2.bundle.css') }}" rel="stylesheet" type="text/css" />
    <style>
        @media (max-width: 767.98px) {
            .post-form-subheader-title {
                font-size: 1rem;
                margin-right: 0.5rem !important;
            }
            .post-form-breadcrumb {
                font-size: 0.75rem;
            }
            .post-form-card-header {
                flex-direction: column;
                align-items: flex-start !important;
            }
            .post-form-card-title {
                margin-bottom: 0.5rem;
            }
            .post-form-footer {
                flex-direction: column;
                gap: 0.5rem;
            }
            .post-form-footer .btn {
                width: 100%;
                margin: 0 !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
            <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <div class="d-flex align-items-center flex-wrap mr-2">
                    <h5 class="text-dark font-weight-bold my-1 mr-5 post-form-subheader-title">Edit Post</h5>
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm post-form-breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.home') }}" class="text-muted">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.posts.index') }}" class="text-muted">Posts</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="text-muted d-none d-sm-inline">{{ optional($post->translate('en'))->title }}</span>
                            <span class="text-muted d-inline d-sm-none">Edit</span>
                        </li>
                    </ul>
                </div>
                <div class="d-flex align-items-center">
                    <a href="{{ route('admin.posts.index') }}" class="btn btn-light font-weight-bold">
                        <i class="flaticon2-left-arrow-1"></i> 
                        <span class="d-none d-sm-inline">Back</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="d-flex flex-column-fluid">
            <div class="container">
                @includeIf('admin.component.alert')

                <form action="{{ route('admin.posts.update', $post) }}" method="POST" enctype="multipart/form-data" id="postForm">
                    @csrf
                    @method('PUT')
                    
                    <!-- Basic Information -->
                    <div class="card card-custom mb-5">
                        <div class="card-header post-form-card-header">
                            <div class="card-title post-form-card-title">
                                <span class="card-icon">
                                    <i class="flaticon2-writing text-primary"></i>
                                </span>
                                <h3 class="card-label">Basic Information</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Title (EN) <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control form-control-solid @error('en_title') is-invalid @enderror" 
                                               name="en_title" 
                                               value="{{ old('en_title', optional($post->translate('en'))->title) }}" 
                                               placeholder="Enter post title (EN)"
                                               required>
                                        @error('en_title')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Title (AR) <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control form-control-solid @error('ar_title') is-invalid @enderror" 
                                               name="ar_title" 
                                               value="{{ old('ar_title', optional($post->translate('ar'))->title) }}" 
                                               placeholder="Enter post title (AR)"
                                               required>
                                        @error('ar_title')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Slug (EN)</label>
                                        <input type="text" 
                                               class="form-control form-control-solid @error('en_slug') is-invalid @enderror" 
                                               name="en_slug" 
                                               value="{{ old('en_slug', optional($post->translate('en'))->slug) }}" 
                                               placeholder="Auto-generated from title">
                                        <span class="form-text text-muted">Leave empty to auto-generate from title</span>
                                        @error('en_slug')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Slug (AR)</label>
                                        <input type="text" 
                                               class="form-control form-control-solid @error('ar_slug') is-invalid @enderror" 
                                               name="ar_slug" 
                                               value="{{ old('ar_slug', optional($post->translate('ar'))->slug) }}" 
                                               placeholder="Auto-generated from title">
                                        <span class="form-text text-muted">Leave empty to auto-generate from title</span>
                                        @error('ar_slug')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Status <span class="text-danger">*</span></label>
                                        <select name="status" 
                                                class="form-control form-control-solid @error('status') is-invalid @enderror" 
                                                required>
                                            <option value="draft" {{ old('status', $post->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                            <option value="published" {{ old('status', $post->status) == 'published' ? 'selected' : '' }}>Published</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Published At</label>
                                        <input type="datetime-local" 
                                               class="form-control form-control-solid @error('published_at') is-invalid @enderror" 
                                               name="published_at" 
                                               value="{{ old('published_at', $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : '') }}">
                                        <span class="form-text text-muted">Leave empty to publish immediately</span>
                                        @error('published_at')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Featured Image</label>
                                        @if($post->featured_image)
                                            <div class="mb-3">
                                                <img src="{{ asset('storage/' . $post->featured_image) }}" 
                                                     alt="Featured Image" 
                                                     class="img-thumbnail"
                                                     style="max-width: 200px; max-height: 200px; object-fit: cover; border-radius: 4px;">
                                            </div>
                                        @endif
                                        <input type="file" 
                                               class="form-control form-control-solid @error('featured_image') is-invalid @enderror" 
                                               name="featured_image" 
                                               accept="image/*">
                                        <span class="form-text text-muted">Upload a new image to replace the current one</span>
                                        @error('featured_image')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="card card-custom mb-5">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="card-label">Content</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="font-weight-bold">Content (EN) <span class="text-danger">*</span></label>
                                <div id="en_content_editor" style="height: 400px;">
                                    {!! old('en_content', optional($post->translate('en'))->content) !!}
                                </div>
                                <textarea name="en_content" id="en_content" style="display: none;" class="@error('en_content') is-invalid @enderror">{{ old('en_content', optional($post->translate('en'))->content) }}</textarea>
                                @error('en_content')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Content (AR) <span class="text-danger">*</span></label>
                                <div id="ar_content_editor" style="height: 400px;" class="text-right">
                                    {!! old('ar_content', optional($post->translate('ar'))->content) !!}
                                </div>
                                <textarea name="ar_content" id="ar_content" style="display: none;" class="@error('ar_content') is-invalid @enderror">{{ old('ar_content', optional($post->translate('ar'))->content) }}</textarea>
                                @error('ar_content')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Excerpt (EN)</label>
                                <textarea name="en_excerpt" 
                                          class="form-control form-control-solid @error('en_excerpt') is-invalid @enderror" 
                                          rows="3"
                                          placeholder="Enter excerpt (EN)">{{ old('en_excerpt', optional($post->translate('en'))->excerpt) }}</textarea>
                                @error('en_excerpt')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Excerpt (AR)</label>
                                <textarea name="ar_excerpt" 
                                          class="form-control form-control-solid @error('ar_excerpt') is-invalid @enderror" 
                                          rows="3"
                                          placeholder="Enter excerpt (AR)">{{ old('ar_excerpt', optional($post->translate('ar'))->excerpt) }}</textarea>
                                @error('ar_excerpt')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- SEO -->
                    <div class="card card-custom mb-5">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="card-label">SEO Settings</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Meta Title (EN)</label>
                                        <input type="text" 
                                               class="form-control form-control-solid @error('en_meta_title') is-invalid @enderror" 
                                               name="en_meta_title" 
                                               value="{{ old('en_meta_title', optional($post->translate('en'))->meta_title) }}" 
                                               placeholder="Enter meta title (EN)">
                                        @error('en_meta_title')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Meta Title (AR)</label>
                                        <input type="text" 
                                               class="form-control form-control-solid @error('ar_meta_title') is-invalid @enderror" 
                                               name="ar_meta_title" 
                                               value="{{ old('ar_meta_title', optional($post->translate('ar'))->meta_title) }}" 
                                               placeholder="Enter meta title (AR)">
                                        @error('ar_meta_title')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Meta Description (EN)</label>
                                        <textarea name="en_meta_description" 
                                                  class="form-control form-control-solid @error('en_meta_description') is-invalid @enderror" 
                                                  rows="3"
                                                  placeholder="Enter meta description (EN)">{{ old('en_meta_description', optional($post->translate('en'))->meta_description) }}</textarea>
                                        @error('en_meta_description')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Meta Description (AR)</label>
                                        <textarea name="ar_meta_description" 
                                                  class="form-control form-control-solid @error('ar_meta_description') is-invalid @enderror" 
                                                  rows="3"
                                                  placeholder="Enter meta description (AR)">{{ old('ar_meta_description', optional($post->translate('ar'))->meta_description) }}</textarea>
                                        @error('ar_meta_description')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Related Products -->
                    <div class="card card-custom mb-5">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="card-label">Related Products</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="font-weight-bold">Products</label>
                                <select name="products[]" id="post_products" class="form-control form-control-solid @error('products') is-invalid @enderror" multiple>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" 
                                            {{ in_array($product->id, old('products', $post->products->pluck('id')->toArray())) ? 'selected' : '' }}>
                                            {{ optional($product->translate('en'))->name ?? $product->ref_code }}
                                            @if($product->translate('ar'))
                                                / {{ $product->translate('ar')->name }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <span class="form-text text-muted">Select one or more products to link with this post</span>
                                @error('products')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card-footer post-form-footer d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary font-weight-bold mr-2">
                            <i class="flaticon2-check-mark"></i> Update Post
                        </button>
                        <a href="{{ route('admin.posts.index') }}" class="btn btn-light-primary font-weight-bold">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="{{ asset('cp_assets/plugins/custom/select2/select2.bundle.js') }}"></script>
    <script src="{{ asset("cp_assets/js/sweetalert2@11.js") }}"></script>
    <script>
        jQuery(document).ready(function() {
            // Initialize Select2 for products multiselect
            $('#post_products').select2({
                placeholder: 'Select products...',
                allowClear: true,
                width: '100%'
            });

            // Quill editor configuration
            var quillConfig = {
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
            };

            // Initialize Quill editors
            var enContentQuill = new Quill('#en_content_editor', quillConfig);
            var arContentQuill = new Quill('#ar_content_editor', quillConfig);

            // Validate and submit form
            $('#postForm').on('submit', function(e) {
                var enContent = enContentQuill.root.innerHTML;
                var arContent = arContentQuill.root.innerHTML;
                
                // Check if content is empty (remove HTML tags and check)
                var enText = enContent.replace(/<[^>]*>/g, '').trim();
                var arText = arContent.replace(/<[^>]*>/g, '').trim();
                
                if (!enText || enText === '') {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Validation Error',
                        text: 'Please enter content in English (EN).',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }
                
                if (!arText || arText === '') {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Validation Error',
                        text: 'Please enter content in Arabic (AR).',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }
                
                // Update hidden textareas
                $('#en_content').val(enContent);
                $('#ar_content').val(arContent);
            });
        });
    </script>
@endpush
