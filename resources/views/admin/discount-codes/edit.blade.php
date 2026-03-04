@extends('admin.layout.app')

@push('style')
    <style>
        /* Responsive styles for discount code form pages */
        @media (max-width: 767.98px) {
            .discount-code-form-subheader-title {
                font-size: 1rem;
                margin-right: 0.5rem !important;
            }
            .discount-code-form-breadcrumb {
                font-size: 0.75rem;
            }
            .discount-code-form-card-header {
                flex-direction: column;
                align-items: flex-start !important;
            }
            .discount-code-form-card-title {
                margin-bottom: 0.5rem;
            }
            .discount-code-form-footer {
                flex-direction: column;
                gap: 0.5rem;
            }
            .discount-code-form-footer .btn {
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
                    <h5 class="text-dark font-weight-bold my-1 mr-5 discount-code-form-subheader-title">Edit Discount Code</h5>
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm discount-code-form-breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.home') }}" class="text-muted">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.discount-codes.index') }}" class="text-muted">Discount Codes</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="text-muted">{{ $discountCode->code }}</span>
                        </li>
                    </ul>
                </div>
                <div class="d-flex align-items-center">
                    <a href="{{ route('admin.discount-codes.index') }}" class="btn btn-light font-weight-bold">
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
                    <div class="card-header discount-code-form-card-header">
                        <div class="card-title discount-code-form-card-title">
                            <span class="card-icon">
                                <i class="flaticon2-writing text-primary"></i>
                            </span>
                            <h3 class="card-label">Update Discount Code</h3>
                        </div>
                    </div>

                    <form action="{{ route('admin.discount-codes.update', $discountCode) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Code <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control form-control-solid @error('code') is-invalid @enderror" 
                                               name="code" 
                                               value="{{ old('code', $discountCode->code) }}" 
                                               placeholder="Enter discount code"
                                               required>
                                        @error('code')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Name</label>
                                        <input type="text" 
                                               class="form-control form-control-solid @error('name') is-invalid @enderror" 
                                               name="name" 
                                               value="{{ old('name', $discountCode->name) }}" 
                                               placeholder="Enter discount code name">
                                        @error('name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Description</label>
                                <textarea name="description" 
                                          class="form-control form-control-solid @error('description') is-invalid @enderror" 
                                          rows="3"
                                          placeholder="Enter description">{{ old('description', $discountCode->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Discount Type <span class="text-danger">*</span></label>
                                        <select name="discount_type" 
                                                id="discount_type"
                                                class="form-control form-control-solid @error('discount_type') is-invalid @enderror" 
                                                required>
                                            <option value="percentage" {{ old('discount_type', $discountCode->discount_type) == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                            <option value="fixed" {{ old('discount_type', $discountCode->discount_type) == 'fixed' ? 'selected' : '' }}>Fixed Amount ($)</option>
                                        </select>
                                        @error('discount_type')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Discount Value <span class="text-danger">*</span></label>
                                        <input type="number" 
                                               step="0.01"
                                               min="0"
                                               class="form-control form-control-solid @error('discount_value') is-invalid @enderror" 
                                               name="discount_value" 
                                               value="{{ old('discount_value', $discountCode->discount_value) }}" 
                                               placeholder="Enter discount value"
                                               required>
                                        <span class="form-text text-muted" id="discount_value_hint">Enter percentage (e.g., 10 for 10%)</span>
                                        @error('discount_value')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Minimum Amount</label>
                                        <input type="number" 
                                               step="0.01"
                                               min="0"
                                               class="form-control form-control-solid @error('min_amount') is-invalid @enderror" 
                                               name="min_amount" 
                                               value="{{ old('min_amount', $discountCode->min_amount) }}" 
                                               placeholder="Enter minimum order amount">
                                        <span class="form-text text-muted">Minimum order amount to apply discount</span>
                                        @error('min_amount')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6" id="max_discount_wrapper">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Maximum Discount</label>
                                        <input type="number" 
                                               step="0.01"
                                               min="0"
                                               class="form-control form-control-solid @error('max_discount') is-invalid @enderror" 
                                               name="max_discount" 
                                               value="{{ old('max_discount', $discountCode->max_discount) }}" 
                                               placeholder="Enter maximum discount">
                                        <span class="form-text text-muted">Maximum discount amount (for percentage only)</span>
                                        @error('max_discount')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Usage Limit</label>
                                        <input type="number" 
                                               min="1"
                                               class="form-control form-control-solid @error('usage_limit') is-invalid @enderror" 
                                               name="usage_limit" 
                                               value="{{ old('usage_limit', $discountCode->usage_limit) }}" 
                                               placeholder="Enter usage limit">
                                        <span class="form-text text-muted">Leave empty for unlimited usage</span>
                                        @error('usage_limit')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Status</label>
                                        <div class="checkbox-inline">
                                            <label class="checkbox checkbox-lg">
                                                <input type="checkbox" name="status" value="1" {{ old('status', $discountCode->status) ? 'checked' : '' }}>
                                                <span></span>
                                                Active
                                            </label>
                                        </div>
                                        <span class="form-text text-muted">Enable or disable this discount code</span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Start Date</label>
                                        <input type="datetime-local" 
                                               class="form-control form-control-solid @error('start_date') is-invalid @enderror" 
                                               name="start_date" 
                                               value="{{ old('start_date', $discountCode->start_date ? $discountCode->start_date->format('Y-m-d\TH:i') : '') }}">
                                        <span class="form-text text-muted">Leave empty to start immediately</span>
                                        @error('start_date')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">End Date</label>
                                        <input type="datetime-local" 
                                               class="form-control form-control-solid @error('end_date') is-invalid @enderror" 
                                               name="end_date" 
                                               value="{{ old('end_date', $discountCode->end_date ? $discountCode->end_date->format('Y-m-d\TH:i') : '') }}">
                                        <span class="form-text text-muted">Leave empty for no expiration</span>
                                        @error('end_date')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer discount-code-form-footer d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary font-weight-bold mr-2">
                                <i class="flaticon2-paperplane"></i> Update
                            </button>
                            <a href="{{ route('admin.discount-codes.index') }}" class="btn btn-light-primary font-weight-bold">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#discount_type').on('change', function() {
                var type = $(this).val();
                var hint = $('#discount_value_hint');
                var maxDiscountWrapper = $('#max_discount_wrapper');
                
                if (type === 'percentage') {
                    hint.text('Enter percentage (e.g., 10 for 10%)');
                    maxDiscountWrapper.show();
                } else {
                    hint.text('Enter fixed amount (e.g., 50 for $50)');
                    maxDiscountWrapper.hide();
                    $('input[name="max_discount"]').val('');
                }
            });
            
            // Trigger on page load
            $('#discount_type').trigger('change');
        });
    </script>
@endpush

