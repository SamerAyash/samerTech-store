@extends('admin.layout.app')

@push('style')
    <style>
        /* Responsive styles for discount code show page */
        @media (max-width: 767.98px) {
            .discount-code-show-subheader-title {
                font-size: 1rem;
                margin-right: 0.5rem !important;
            }
            .discount-code-show-breadcrumb {
                font-size: 0.75rem;
            }
        }
    </style>
@endpush

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-2">
                <h5 class="text-dark font-weight-bold my-1 mr-5 discount-code-show-subheader-title">Discount Code Details</h5>
                <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm discount-code-show-breadcrumb">
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
    <!--end::Subheader-->

    <div class="d-flex flex-column-fluid">
        <div class="container">
            @includeIf('admin.component.alert')

            <div class="row">
                <!--begin::Discount Code Info-->
                <div class="col-xl-8">
                    <div class="card card-custom mb-5">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="card-label">Discount Code Information</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-5">
                                <div class="col-md-6">
                                    <label class="font-weight-bold text-dark">Code:</label>
                                    <p class="text-muted font-weight-bold">{{ $discountCode->code }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="font-weight-bold text-dark">ID:</label>
                                    <p class="text-muted">#{{ $discountCode->id }}</p>
                                </div>
                                @if($discountCode->name)
                                <div class="col-md-6">
                                    <label class="font-weight-bold text-dark">Name:</label>
                                    <p class="text-muted">{{ $discountCode->name }}</p>
                                </div>
                                @endif
                                <div class="col-md-6">
                                    <label class="font-weight-bold text-dark">Status:</label>
                                    <div class="mt-2">
                                        @php
                                            $isValid = $discountCode->isValid();
                                        @endphp
                                        <span class="badge badge-{{ $discountCode->status ? 'success' : 'danger' }} badge-lg">
                                            {{ $discountCode->status ? 'Active' : 'Inactive' }}
                                        </span>
                                        @if($isValid)
                                            <span class="badge badge-info badge-lg ml-2">Valid</span>
                                        @else
                                            <span class="badge badge-warning badge-lg ml-2">Invalid</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="font-weight-bold text-dark">Created At:</label>
                                    <p class="text-muted">{{ $discountCode->created_at->format('M d, Y h:i A') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="font-weight-bold text-dark">Updated At:</label>
                                    <p class="text-muted">{{ $discountCode->updated_at->format('M d, Y h:i A') }}</p>
                                </div>
                            </div>

                            @if($discountCode->description)
                            <div class="mb-5">
                                <label class="font-weight-bold text-dark">Description:</label>
                                <p class="text-muted">{{ $discountCode->description }}</p>
                            </div>
                            @endif

                            <h4 class="font-weight-bold mb-5">Discount Details</h4>
                            <div class="row">
                                <div class="col-md-6 mb-5">
                                    <label class="font-weight-bold text-dark">Discount Type:</label>
                                    <p class="text-muted">
                                        @if($discountCode->discount_type === 'percentage')
                                            <span class="badge badge-info">Percentage</span>
                                        @else
                                            <span class="badge badge-primary">Fixed Amount</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-5">
                                    <label class="font-weight-bold text-dark">Discount Value:</label>
                                    <p class="text-muted font-weight-bold">
                                        @if($discountCode->discount_type === 'percentage')
                                            {{ number_format($discountCode->discount_value, 2) }}%
                                        @else
                                            ${{ number_format($discountCode->discount_value, 2) }}
                                        @endif
                                    </p>
                                </div>
                                @if($discountCode->min_amount)
                                <div class="col-md-6 mb-5">
                                    <label class="font-weight-bold text-dark">Minimum Amount:</label>
                                    <p class="text-muted">${{ number_format($discountCode->min_amount, 2) }}</p>
                                </div>
                                @endif
                                @if($discountCode->max_discount && $discountCode->discount_type === 'percentage')
                                <div class="col-md-6 mb-5">
                                    <label class="font-weight-bold text-dark">Maximum Discount:</label>
                                    <p class="text-muted">${{ number_format($discountCode->max_discount, 2) }}</p>
                                </div>
                                @endif
                            </div>

                            <h4 class="font-weight-bold mb-5">Usage Information</h4>
                            <div class="row">
                                <div class="col-md-6 mb-5">
                                    <label class="font-weight-bold text-dark">Usage Limit:</label>
                                    <p class="text-muted">
                                        @if($discountCode->usage_limit)
                                            {{ $discountCode->usage_limit }} times
                                        @else
                                            Unlimited
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-5">
                                    <label class="font-weight-bold text-dark">Used Count:</label>
                                    <p class="text-muted font-weight-bold">{{ $discountCode->used_count }} times</p>
                                </div>
                                @if($discountCode->start_date)
                                <div class="col-md-6 mb-5">
                                    <label class="font-weight-bold text-dark">Start Date:</label>
                                    <p class="text-muted">{{ $discountCode->start_date->format('M d, Y h:i A') }}</p>
                                </div>
                                @endif
                                @if($discountCode->end_date)
                                <div class="col-md-6 mb-5">
                                    <label class="font-weight-bold text-dark">End Date:</label>
                                    <p class="text-muted">{{ $discountCode->end_date->format('M d, Y h:i A') }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Discount Code Info-->

                <!--begin::Actions & Orders-->
                <div class="col-xl-4">
                    <div class="card card-custom">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="card-label">Actions</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-column">
                                <a href="{{ route('admin.discount-codes.edit', $discountCode) }}" class="btn btn-primary font-weight-bold mb-3">
                                    <i class="flaticon2-edit"></i> Edit Discount Code
                                </a>
                                <button type="button" class="btn btn-warning font-weight-bold mb-3" onclick="toggleStatus({{ $discountCode->id }})">
                                    <i class="flaticon2-protection"></i> Toggle Status
                                </button>
                                <button type="button" class="btn btn-danger font-weight-bold" onclick="deleteDiscountCode({{ $discountCode->id }})">
                                    <i class="flaticon2-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>

                    <!--begin::Orders Using This Code-->
                    <div class="card card-custom mt-5">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="card-label">Orders Using This Code ({{ $discountCode->orders_count }})</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($discountCode->orders->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Order #</th>
                                                <th>Customer</th>
                                                <th>Amount</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($discountCode->orders->take(10) as $order)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('admin.orders.show', $order) }}" class="text-primary font-weight-bold">
                                                            {{ $order->order_number }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        @if($order->user)
                                                            {{ $order->user->name }}
                                                        @else
                                                            <span class="text-muted">Guest</span>
                                                        @endif
                                                    </td>
                                                    <td>${{ number_format($order->total_amount, 2) }}</td>
                                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if($discountCode->orders->count() > 10)
                                    <p class="text-muted text-center mt-3">Showing first 10 orders</p>
                                @endif
                            @else
                                <p class="text-muted text-center">No orders have used this discount code yet.</p>
                            @endif
                        </div>
                    </div>
                    <!--end::Orders Using This Code-->
                </div>
                <!--end::Actions & Orders-->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset("cp_assets/js/sweetalert2@11.js") }}"></script>
    <script>
        // Delete discount code
        function deleteDiscountCode(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-danger font-weight-bold',
                    cancelButton: 'btn btn-light font-weight-bold'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.discount-codes.destroy', ':id') }}".replace(':id', id),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: response.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        confirmButton: 'btn btn-primary font-weight-bold'
                                    },
                                    buttonsStyling: false
                                }).then(() => {
                                    window.location.href = "{{ route('admin.discount-codes.index') }}";
                                });
                            }
                        },
                        error: function (xhr) {
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

        // Toggle status
        function toggleStatus(id) {
            $.ajax({
                url: "{{ route('admin.discount-codes.toggleStatus', ':id') }}".replace(':id', id),
                type: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response.success) {
                        location.reload();
                    }
                },
                error: function (xhr) {
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

