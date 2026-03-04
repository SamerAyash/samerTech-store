@extends('admin.layout.app')

@push('style')
    <style>
        /* Responsive styles for category form pages */
        @media (max-width: 767.98px) {
            .category-form-subheader-title {
                font-size: 1rem;
                margin-right: 0.5rem !important;
            }
            .category-form-breadcrumb {
                font-size: 0.75rem;
            }
            .category-form-card-header {
                flex-direction: column;
                align-items: flex-start !important;
            }
            .category-form-card-title {
                margin-bottom: 0.5rem;
            }
            .category-form-footer {
                flex-direction: column;
                gap: 0.5rem;
            }
            .category-form-footer .btn {
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
                    <h5 class="text-dark font-weight-bold my-1 mr-5 category-form-subheader-title">Create Category</h5>
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm category-form-breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.home') }}" class="text-muted">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.categories.index') }}" class="text-muted">Categories</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="text-muted">Create</span>
                        </li>
                    </ul>
                </div>
                <div class="d-flex align-items-center">
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-light font-weight-bold">
                        <i class="flaticon2-left-arrow-1"></i> 
                        <span class="d-none d-sm-inline">Back</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="d-flex flex-column-fluid">
            <div class="container">
                @includeIf('admin.component.alert')

                <div class="card card-custom">
                    <div class="card-header category-form-card-header">
                        <div class="card-title category-form-card-title">
                            <span class="card-icon">
                                <i class="flaticon2-add-1 text-primary"></i>
                            </span>
                            <h3 class="card-label">New Category</h3>
                        </div>
                    </div>

                    <form action="{{ route('admin.categories.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label class="font-weight-bold">Name (EN) <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control form-control-solid @error('en_name') is-invalid @enderror" 
                                       name="en_name" 
                                       value="{{ old('en_name') }}" 
                                       placeholder="Enter category name (EN)"
                                       required>
                                @error('en_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Name (AR) <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control form-control-solid @error('ar_name') is-invalid @enderror" 
                                       name="ar_name" 
                                       value="{{ old('ar_name') }}" 
                                       placeholder="Enter category name (AR)"
                                       required>
                                @error('ar_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Parent Category</label>
                                <select name="parent_id" 
                                        class="form-control form-control-solid @error('parent_id') is-invalid @enderror">
                                    <option value="">Root (no parent)</option>
                                    @foreach ($parents as $parent)
                                        <option value="{{ $parent->id }}" 
                                            {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="form-text text-muted">Select a parent category if this is a subcategory.</span>
                                @error('parent_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Status <span class="text-danger">*</span></label>
                                <select name="status" 
                                        class="form-control form-control-solid @error('status') is-invalid @enderror" 
                                        required>
                                    <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <div class="checkbox-inline">
                                    <label class="checkbox checkbox-lg">
                                        <input type="checkbox" 
                                               name="active_navbar" 
                                               value="1"
                                               {{ old('active_navbar') == '1' ? 'checked' : '' }}>
                                        <span></span>
                                        Display in Navbar
                                    </label>
                                </div>
                                <span class="form-text text-muted">Enable this option to display this category in the navigation bar.</span>
                                @error('active_navbar')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="card-footer category-form-footer d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary font-weight-bold mr-2">
                                <i class="flaticon2-paperplane"></i> Create
                            </button>
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-light-primary font-weight-bold">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
