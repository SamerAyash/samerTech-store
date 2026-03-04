@extends('admin.layout.app')

@push('style')
    <style>
        /* Responsive styles for user show page */
        @media (max-width: 767.98px) {
            .user-show-subheader-title {
                font-size: 1rem;
                margin-right: 0.5rem !important;
            }
            .user-show-breadcrumb {
                font-size: 0.75rem;
            }
            .user-avatar-wrapper {
                text-align: center;
                margin-bottom: 1rem;
            }
        }
        .user-avatar-large {
            width: 120px;
            height: 120px;
            border-radius: 50%;
        }
    </style>
@endpush

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-2">
                <h5 class="text-dark font-weight-bold my-1 mr-5 user-show-subheader-title">User Profile</h5>
                <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm user-show-breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.home') }}" class="text-muted">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.users.index') }}" class="text-muted">Users</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="text-muted">{{ $user->name }}</span>
                    </li>
                </ul>
            </div>
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.users.index') }}" class="btn btn-light font-weight-bold">
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

            <!--begin::Profile Overview-->
            <div class="row">
                <div class="col-xl-4">
                    <!--begin::Profile Card-->
                    <div class="card card-custom card-stretch">
                        <div class="card-body pt-4">
                            <div class="user-avatar-wrapper text-center mb-7">
                                @if($user->avatar)
                                    <div class="symbol symbol-120 symbol-lg-120">
                                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="user-avatar-large">
                                    </div>
                                @else
                                    <div class="symbol symbol-120 symbol-lg-120">
                                        <div class="symbol-label font-size-h1 font-weight-bold bg-light-primary text-primary">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    </div>
                                @endif
                                <div class="mt-4">
                                    <h3 class="font-weight-bold text-dark">{{ $user->name }}</h3>
                                    <span class="text-muted font-size-sm">{{ $user->email }}</span>
                                </div>
                            </div>

                            <!--begin::User Info-->
                            <div class="mb-7">
                                <div class="mb-4">
                                    <span class="font-weight-bold text-dark">Status:</span>
                                    @if($user->status == 'active')
                                        <span class="badge badge-success ml-2">Active</span>
                                    @else
                                        <span class="badge badge-danger ml-2">Blocked</span>
                                    @endif
                                </div>
                                <div class="mb-4">
                                    <span class="font-weight-bold text-dark">Email Verified:</span>
                                    @if($user->email_verified_at)
                                        <span class="badge badge-success ml-2">
                                            <i class="flaticon2-check-mark"></i> Verified
                                        </span>
                                    @else
                                        <span class="badge badge-light ml-2">
                                            <i class="flaticon2-cross"></i> Not Verified
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <!--end::User Info-->

                            <!--begin::Actions-->
                            <div class="d-flex flex-column">
                                <button type="button" class="btn btn-primary mb-2" onclick="updateUserStatus({{ $user->id }}, '{{ $user->status }}')">
                                    <i class="flaticon2-protection"></i> Change Status
                                </button>
                                @if(!$user->email_verified_at)
                                    <button type="button" class="btn btn-success mb-2" onclick="verifyEmail({{ $user->id }})">
                                        <i class="flaticon2-check-mark"></i> Verify Email
                                    </button>
                                @endif
                            </div>
                            <!--end::Actions-->
                        </div>
                    </div>
                    <!--end::Profile Card-->
                </div>

                <div class="col-xl-8">
                    <!--begin::Tabs-->
                    <div class="card card-custom">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="card-label">User Information</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-tabs nav-tabs-line" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#personal_info">
                                        <span class="nav-icon">
                                            <i class="flaticon2-user"></i>
                                        </span>
                                        <span class="nav-text">Personal Info</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#account_settings">
                                        <span class="nav-icon">
                                            <i class="flaticon2-settings"></i>
                                        </span>
                                        <span class="nav-text">Account Settings</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#store_activity">
                                        <span class="nav-icon">
                                            <i class="flaticon2-shopping-cart"></i>
                                        </span>
                                        <span class="nav-text">Store Activity</span>
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content mt-5">
                                <!--begin::Personal Info Tab-->
                                <div class="tab-pane fade show active" id="personal_info" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6 mb-5">
                                            <label class="font-weight-bold text-dark">Full Name:</label>
                                            <p class="text-muted">{{ $user->name }}</p>
                                        </div>
                                        <div class="col-md-6 mb-5">
                                            <label class="font-weight-bold text-dark">Email:</label>
                                            <p class="text-muted">{{ $user->email }}</p>
                                        </div>
                                        <div class="col-md-6 mb-5">
                                            <label class="font-weight-bold text-dark">Phone:</label>
                                            <p class="text-muted">{{ $user->phone ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-5">
                                            <label class="font-weight-bold text-dark">Gender:</label>
                                            <p class="text-muted">{{ ucfirst($user->gender ?? '-') }}</p>
                                        </div>
                                        <div class="col-md-6 mb-5">
                                            <label class="font-weight-bold text-dark">Birth Date:</label>
                                            <p class="text-muted">{{ $user->birth_date ? $user->birth_date->format('M d, Y') : '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-5">
                                            <label class="font-weight-bold text-dark">Country:</label>
                                            <p class="text-muted">{{ $user->country ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-5">
                                            <label class="font-weight-bold text-dark">City:</label>
                                            <p class="text-muted">{{ $user->city ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-5">
                                            <label class="font-weight-bold text-dark">Main Address:</label>
                                            <p class="text-muted">{{ $user->main_address ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-5">
                                            <label class="font-weight-bold text-dark">Registration Date:</label>
                                            <p class="text-muted">{{ $user->created_at->format('M d, Y h:i A') }}</p>
                                        </div>
                                        <div class="col-md-6 mb-5">
                                            <label class="font-weight-bold text-dark">Last Login:</label>
                                            <p class="text-muted">{{ $user->last_login_at ? $user->last_login_at->format('M d, Y h:i A') : 'Never' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Personal Info Tab-->

                                <!--begin::Account Settings Tab-->
                                <div class="tab-pane fade" id="account_settings" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6 mb-5">
                                            <label class="font-weight-bold text-dark">Account Status:</label>
                                            <div class="mt-2">
                                                @if($user->status == 'active')
                                                    <span class="badge badge-success badge-lg">Active</span>
                                                @else
                                                    <span class="badge badge-danger badge-lg">Blocked</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-5">
                                            <label class="font-weight-bold text-dark">Email Verified:</label>
                                            <div class="mt-2">
                                                @if($user->email_verified_at)
                                                    <span class="badge badge-success badge-lg">
                                                        <i class="flaticon2-check-mark"></i> Verified
                                                        <br><small class="text-muted">{{ $user->email_verified_at->format('M d, Y') }}</small>
                                                    </span>
                                                @else
                                                    <span class="badge badge-light badge-lg">
                                                        <i class="flaticon2-cross"></i> Not Verified
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-5">
                                            <label class="font-weight-bold text-dark">Phone Verified:</label>
                                            <div class="mt-2">
                                                @if($user->phone_verified_at)
                                                    <span class="badge badge-success badge-lg">
                                                        <i class="flaticon2-check-mark"></i> Verified
                                                        <br><small class="text-muted">{{ $user->phone_verified_at->format('M d, Y') }}</small>
                                                    </span>
                                                @else
                                                    <span class="badge badge-light badge-lg">
                                                        <i class="flaticon2-cross"></i> Not Verified
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Account Settings Tab-->

                                <!--begin::Store Activity Tab-->
                                <div class="tab-pane fade" id="store_activity" role="tabpanel">
                                    <div class="row mb-10">
                                        <div class="col-md-3">
                                            <div class="card card-custom bg-light-primary">
                                                <div class="card-body text-center">
                                                    <span class="text-dark font-weight-bold font-size-h2 d-block">{{ $user->orders_count ?? 0 }}</span>
                                                    <span class="text-muted font-size-sm">Total Orders</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card card-custom bg-light-success">
                                                <div class="card-body text-center">
                                                    <span class="text-dark font-weight-bold font-size-h2 d-block">${{ number_format($totalSpent, 2) }}</span>
                                                    <span class="text-muted font-size-sm">Total Spent</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card card-custom bg-light-info">
                                                <div class="card-body text-center">
                                                    <span class="text-dark font-weight-bold font-size-h2 d-block">${{ number_format($averageOrderValue, 2) }}</span>
                                                    <span class="text-muted font-size-sm">Avg Order Value</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card card-custom bg-light-warning">
                                                <div class="card-body text-center">
                                                    <span class="text-dark font-weight-bold font-size-h2 d-block">
                                                        {{ $lastPurchaseDate ? $lastPurchaseDate->format('M d') : 'Never' }}
                                                    </span>
                                                    <span class="text-muted font-size-sm">Last Purchase</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <h4 class="font-weight-bold mb-5">Latest Orders</h4>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Order ID</th>
                                                    <th>Status</th>
                                                    <th>Total</th>
                                                    <th>Created At</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($latestOrders as $order)
                                                    <tr>
                                                        <td>#{{ $order->id }}</td>
                                                        <td>
                                                            @php
                                                                $statusColors = [
                                                                    'pending' => 'warning',
                                                                    'processing' => 'info',
                                                                    'shipped' => 'primary',
                                                                    'delivered' => 'success',
                                                                    'cancelled' => 'danger'
                                                                ];
                                                                $color = $statusColors[$order->status] ?? 'secondary';
                                                            @endphp
                                                            <span class="badge badge-{{ $color }}">{{ ucfirst($order->status) }}</span>
                                                        </td>
                                                        <td>${{ number_format($order->total_amount, 2) }}</td>
                                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                                        <td>
                                                            <a href="#" class="btn btn-sm btn-light-primary">
                                                                <i class="flaticon2-eye"></i> View
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center text-muted">No orders found</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!--end::Store Activity Tab-->
                            </div>
                        </div>
                    </div>
                    <!--end::Tabs-->
                </div>
            </div>
            <!--end::Profile Overview-->
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset("cp_assets/js/sweetalert2@11.js") }}"></script>
    <script>
        // Update user status
        function updateUserStatus(id, currentStatus) {
            const statusMap = {
                'active': 'blocked',
                'blocked': 'active'
            };
            const newStatus = statusMap[currentStatus] || 'active';
            const statusLabels = {
                'active': 'Active',
                'blocked': 'Blocked'
            };

            Swal.fire({
                title: 'Update Status?',
                html: `<p>Change user status to <strong>${statusLabels[newStatus]}</strong>?</p>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, update',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-primary font-weight-bold',
                    cancelButton: 'btn btn-light font-weight-bold'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.users.updateStatus', ':id') }}"
                            .replace(':id', id),
                        type: 'PUT',
                        data: {
                            _token: '{{ csrf_token() }}',
                            status: newStatus
                        },
                        success: function (response) {
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
                                    location.reload();
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

        // Verify email
        function verifyEmail(id) {
            Swal.fire({
                title: 'Verify Email?',
                text: 'Are you sure you want to verify this user\'s email?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, verify',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-success font-weight-bold',
                    cancelButton: 'btn btn-light font-weight-bold'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.users.verifyEmail', ':id') }}"
                            .replace(':id', id),
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'Verified!',
                                    text: response.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        confirmButton: 'btn btn-primary font-weight-bold'
                                    },
                                    buttonsStyling: false
                                }).then(() => {
                                    location.reload();
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

        // Verify phone
        function verifyPhone(id) {
            Swal.fire({
                title: 'Verify Phone?',
                text: 'Are you sure you want to verify this user\'s phone?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, verify',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-info font-weight-bold',
                    cancelButton: 'btn btn-light font-weight-bold'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.users.verifyPhone', ':id') }}"
                            .replace(':id', id),
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'Verified!',
                                    text: response.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        confirmButton: 'btn btn-primary font-weight-bold'
                                    },
                                    buttonsStyling: false
                                }).then(() => {
                                    location.reload();
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
    </script>
@endpush

