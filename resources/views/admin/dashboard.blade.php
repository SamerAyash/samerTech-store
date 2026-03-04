@extends('admin.layout.app')

@push('style')
    <link href="{{ asset('cp_assets/plugins/custom/amcharts/amcharts.bundle.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .stat-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
        }
        .stat-card.primary { border-left-color: #5865F2; }
        .stat-card.success { border-left-color: #0BB783; }
        .stat-card.info { border-left-color: #1BC5BD; }
        .stat-card.warning { border-left-color: #FFA800; }
        .stat-card.danger { border-left-color: #F64E60; }
        
        .stat-icon {
            width: 70px;
            height: 70px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }
        
        .revenue-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
        }
        
        .revenue-card.success {
            background: linear-gradient(135deg, #0BB783 0%, #0a9d6e 100%);
        }
        
        .revenue-card.info {
            background: linear-gradient(135deg, #1BC5BD 0%, #159a94 100%);
        }
        
        .revenue-card.warning {
            background: linear-gradient(135deg, #FFA800 0%, #e69400 100%);
        }
        
        .chart-container {
            min-height: 350px;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1.2;
        }
        
        .stat-label {
            font-size: 0.95rem;
            font-weight: 500;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .recent-orders-table {
            font-size: 0.9rem;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.85rem;
        }
    </style>
@endpush

@section('content')
    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Subheader-->
        <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
            <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <!--begin::Info-->
                <div class="d-flex align-items-center flex-wrap mr-2">
                    <!--begin::Page Title-->
                    <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">
                        Dashboard - Statistics & Analytics
                    </h5>
                    <!--end::Page Title-->
                </div>
                <!--end::Info-->
            </div>
        </div>
        <!--end::Subheader-->

        <!--begin::Entry-->
        <div class="d-flex flex-column-fluid">
            <!--begin::Container-->
            <div class="container">
                <!--begin::Dashboard-->
                <!--begin::Row - Main Stats-->
                <div class="row mb-8">
                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                        <!--begin::Stats Widget-->
                        <div class="card card-custom stat-card primary">
                            <div class="card-body p-6">
                                <div class="d-flex flex-column">
                                    <div class="stat-icon bg-light-primary">
                                        <span class="svg-icon svg-icon-primary svg-icon-3x">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" fill="currentColor"/>
                                                <path opacity="0.3" d="M12.0002 14.5C7.99016 14.5 4.75016 17.15 4.75016 20.5C4.75016 20.83 4.92016 21.14 5.20016 21.33C5.48016 21.52 5.83016 21.58 6.18016 21.5H17.8202C18.1702 21.58 18.5202 21.52 18.8002 21.33C19.0802 21.14 19.2502 20.83 19.2502 20.5C19.2502 17.15 16.0102 14.5 12.0002 14.5Z" fill="currentColor"/>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="mt-4">
                                        <span class="text-dark font-weight-bolder font-size-h3 d-block">{{ number_format($totalUsers) }}</span>
                                        <span class="text-muted font-weight-bold font-size-lg mt-1">Total Users</span>
                                        <div class="d-flex align-items-center mt-3">
                                            <span class="text-success font-weight-bolder font-size-sm mr-2">{{ $activeUsers }}</span>
                                            <span class="text-muted font-size-sm">Active</span>
                                            <span class="text-danger font-weight-bolder font-size-sm mr-2 ml-4">{{ $blockedUsers }}</span>
                                            <span class="text-muted font-size-sm">Blocked</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Stats Widget-->
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                        <!--begin::Stats Widget-->
                        <div class="card card-custom stat-card success">
                            <div class="card-body p-6">
                                <div class="d-flex flex-column">
                                    <div class="stat-icon bg-light-success">
                                        <span class="svg-icon svg-icon-success svg-icon-3x">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path d="M3 13C3 9.22876 3 7.34315 4.17157 6.17157C5.34315 5.34315 7.22876 5.34315 11 5.34315H13C16.7712 5.34315 18.6569 5.34315 19.8284 6.17157C21 7.34315 21 9.22876 21 13V15C21 18.7712 21 20.6569 19.8284 21.8284C18.6569 23 16.7712 23 13 23H11C7.22876 23 5.34315 23 4.17157 21.8284C3 20.6569 3 18.7712 3 15V13Z" fill="currentColor"/>
                                                <path opacity="0.3" d="M1 10C1 9.44772 1.44772 9 2 9H22C22.5523 9 23 9.44772 23 10C23 10.5523 22.5523 11 22 11H2C1.44772 11 1 10.5523 1 10Z" fill="currentColor"/>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="mt-4">
                                        <span class="text-dark font-weight-bolder font-size-h3 d-block">{{ number_format($totalOrders) }}</span>
                                        <span class="text-muted font-weight-bold font-size-lg mt-1">Total Orders</span>
                                        <div class="d-flex align-items-center mt-3">
                                            <span class="text-info font-weight-bolder font-size-sm mr-2">{{ $paidOrders }}</span>
                                            <span class="text-muted font-size-sm">Paid</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Stats Widget-->
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                        <!--begin::Stats Widget-->
                        <div class="card card-custom stat-card info">
                            <div class="card-body p-6">
                                <div class="d-flex flex-column">
                                    <div class="stat-icon bg-light-info">
                                        <span class="svg-icon svg-icon-info svg-icon-3x">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path d="M6 2C4.34315 2 3 3.34315 3 5V19C3 20.6569 4.34315 22 6 22H18C19.6569 22 21 20.6569 21 19V5C21 3.34315 19.6569 2 18 2H6Z" fill="currentColor"/>
                                                <path opacity="0.3" d="M7 7H17C17.5523 7 18 7.44772 18 8C18 8.55228 17.5523 9 17 9H7C6.44772 9 6 8.55228 6 8C6 7.44772 6.44772 7 7 7Z" fill="currentColor"/>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="mt-4">
                                        <span class="text-dark font-weight-bolder font-size-h3 d-block">{{ number_format($totalProducts) }}</span>
                                        <span class="text-muted font-weight-bold font-size-lg mt-1">Total Products</span>
                                        <div class="d-flex align-items-center mt-3">
                                            <span class="text-success font-weight-bolder font-size-sm mr-2">{{ $activeProducts }}</span>
                                            <span class="text-muted font-size-sm">Active</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Stats Widget-->
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                        <!--begin::Stats Widget-->
                        <div class="card card-custom stat-card warning">
                            <div class="card-body p-6">
                                <div class="d-flex flex-column">
                                    <div class="stat-icon bg-light-warning">
                                        <span class="svg-icon svg-icon-warning svg-icon-3x">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path d="M3 13C3 9.22876 3 7.34315 4.17157 6.17157C5.34315 5.34315 7.22876 5.34315 11 5.34315H13C16.7712 5.34315 18.6569 5.34315 19.8284 6.17157C21 7.34315 21 9.22876 21 13V15C21 18.7712 21 20.6569 19.8284 21.8284C18.6569 23 16.7712 23 13 23H11C7.22876 23 5.34315 23 4.17157 21.8284C3 20.6569 3 18.7712 3 15V13Z" fill="currentColor"/>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="mt-4">
                                        <span class="text-dark font-weight-bolder font-size-h3 d-block">{{ number_format($totalCategories) }}</span>
                                        <span class="text-muted font-weight-bold font-size-lg mt-1">Total Categories</span>
                                        <div class="d-flex align-items-center mt-3">
                                            <span class="text-success font-weight-bolder font-size-sm mr-2">{{ $activeCategories }}</span>
                                            <span class="text-muted font-size-sm">Active</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Stats Widget-->
                    </div>
                </div>
                <!--end::Row-->

                <!--begin::Row - Additional Stats-->
                <div class="row mb-8">
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <!--begin::Stats Widget-->
                        <div class="card card-custom stat-card danger">
                            <div class="card-body p-6">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-light-danger mr-6">
                                        <span class="svg-icon svg-icon-danger svg-icon-3x">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path d="M3 13C3 9.22876 3 7.34315 4.17157 6.17157C5.34315 5.34315 7.22876 5.34315 11 5.34315H13C16.7712 5.34315 18.6569 5.34315 19.8284 6.17157C21 7.34315 21 9.22876 21 13V15C21 18.7712 21 20.6569 19.8284 21.8284C18.6569 23 16.7712 23 13 23H11C7.22876 23 5.34315 23 4.17157 21.8284C3 20.6569 3 18.7712 3 15V13Z" fill="currentColor"/>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-dark font-weight-bolder font-size-h3 mb-1">{{ number_format($totalPosts) }}</span>
                                        <span class="text-muted font-weight-bold font-size-lg">Total Posts</span>
                                        <div class="d-flex align-items-center mt-2">
                                            <span class="text-success font-weight-bolder font-size-sm mr-2">{{ $publishedPosts }}</span>
                                            <span class="text-muted font-size-sm">Published</span>
                                            <span class="text-muted font-weight-bolder font-size-sm mr-2 ml-4">{{ $draftPosts }}</span>
                                            <span class="text-muted font-size-sm">Draft</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Stats Widget-->
                    </div>
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <!--begin::Stats Widget-->
                        <div class="card card-custom stat-card primary">
                            <div class="card-body p-6">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-light-primary mr-6">
                                        <span class="svg-icon svg-icon-primary svg-icon-3x">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path d="M3 13C3 9.22876 3 7.34315 4.17157 6.17157C5.34315 5.34315 7.22876 5.34315 11 5.34315H13C16.7712 5.34315 18.6569 5.34315 19.8284 6.17157C21 7.34315 21 9.22876 21 13V15C21 18.7712 21 20.6569 19.8284 21.8284C18.6569 23 16.7712 23 13 23H11C7.22876 23 5.34315 23 4.17157 21.8284C3 20.6569 3 18.7712 3 15V13Z" fill="currentColor"/>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-dark font-weight-bolder font-size-h3 mb-1">{{ number_format($totalMessages) }}</span>
                                        <span class="text-muted font-weight-bold font-size-lg">Total Messages</span>
                                        <div class="d-flex align-items-center mt-2">
                                            <span class="text-warning font-weight-bolder font-size-sm mr-2">{{ $unreadMessages }}</span>
                                            <span class="text-muted font-size-sm">Unread</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Stats Widget-->
                    </div>
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <!--begin::Stats Widget-->
                        <div class="card card-custom stat-card success">
                            <div class="card-body p-6">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-light-success mr-6">
                                        <span class="svg-icon svg-icon-success svg-icon-3x">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM13 17H11V15H13V17ZM13 13H11V7H13V13Z" fill="currentColor"/>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-dark font-weight-bolder font-size-h3 mb-1">{{ number_format($totalDiscountCodes) }}</span>
                                        <span class="text-muted font-weight-bold font-size-lg">Discount Codes</span>
                                        <div class="d-flex align-items-center mt-2">
                                            <span class="text-success font-weight-bolder font-size-sm mr-2">{{ $activeDiscountCodes }}</span>
                                            <span class="text-muted font-size-sm">Active</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Stats Widget-->
                    </div>
                </div>
                <!--end::Row-->
                <!--begin::Row - Recent Orders-->
                <div class="row mb-8">
                    <div class="col-xl-12">
                        <!--begin::Card-->
                        <div class="card card-custom">
                            <div class="card-header border-0 pt-6">
                                <div class="card-title w-100">
                                    <h3 class="card-label font-weight-bolder">Recent Orders</h3>
                                </div>
                                <div class="card-toolbar">
                                    <a href="{{ route('admin.orders.index') }}" class="btn btn-primary font-weight-bolder">
                                        View All
                                    </a>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="table-responsive">
                                    <table class="table table-head-custom table-vertical-center table-head-bg table-borderless">
                                        <thead>
                                            <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                                <th class="min-w-50px">ID</th>
                                                <th class="min-w-150px">Order Number</th>
                                                <th class="min-w-150px">Customer</th>
                                                <th class="min-w-100px">Total Amount</th>
                                                <th class="min-w-100px">Payment Status</th>
                                                <th class="min-w-100px">Status</th>
                                                <th class="min-w-150px">Created At</th>
                                                <th class="text-end min-w-100px">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentOrders as $order)
                                                @php
                                                    $orderTotalQAR = app(\App\Services\CurrencyService::class)->convertToQAR($order->total, $order->currency);
                                                    $statusColors = [
                                                        'pending' => 'warning',
                                                        'processing' => 'info',
                                                        'shipped' => 'primary',
                                                        'delivered' => 'success',
                                                        'cancelled' => 'danger'
                                                    ];
                                                    $paymentColors = [
                                                        'pending' => 'warning',
                                                        'paid' => 'success',
                                                        'failed' => 'danger',
                                                        'refunded' => 'info'
                                                    ];
                                                    $statusColor = $statusColors[$order->status] ?? 'secondary';
                                                    $paymentColor = $paymentColors[$order->payment_status] ?? 'secondary';
                                                @endphp
                                                <tr>
                                                    <td class="text-center">
                                                        <span class="text-dark font-weight-bold">#{{ $order->id }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="text-dark font-weight-bold">{{ $order->order_number }}</span>
                                                    </td>
                                                    <td>
                                                        @if($order->user)
                                                            <div class="d-flex flex-column">
                                                                <span class="text-dark font-weight-bold">{{ $order->user->name }}</span>
                                                                <span class="text-muted font-size-sm">{{ $order->user->email }}</span>
                                                            </div>
                                                        @else
                                                            <span class="text-muted">Guest</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="text-dark font-weight-bold">{{ number_format($orderTotalQAR, 2) }} QAR</span>
                                                        @if($order->currency !== 'QAR')
                                                            <div class="text-muted font-size-sm">({{ number_format($order->total, 2) }} {{ $order->currency }})</div>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge badge-{{ $paymentColor }}">{{ ucfirst($order->payment_status) }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge badge-{{ $statusColor }}">{{ ucfirst($order->status) }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="text-dark font-weight-bold">{{ $order->created_at->format('M d, Y') }}</span>
                                                        <div class="text-muted font-size-sm">{{ $order->created_at->format('H:i') }}</div>
                                                    </td>
                                                    <td class="text-end">
                                                        <div class="d-flex justify-content-end flex-wrap">
                                                            <a href="{{ route('admin.orders.show', $order->id) }}" 
                                                               class="btn btn-icon btn-light btn-hover-primary btn-sm mr-1" 
                                                               data-toggle="tooltip" 
                                                               title="View Order">
                                                                <i class="flaticon-eye"></i>
                                                            </a>
                                                            <button type="button" 
                                                                    class="btn btn-icon btn-light btn-hover-info btn-sm mr-1" 
                                                                    data-toggle="tooltip" 
                                                                    title="Update Status"
                                                                    onclick="updateOrderStatus({{ $order->id }}, '{{ $order->status }}')">
                                                                <i class="flaticon2-settings"></i>
                                                            </button>
                                                            <button type="button" 
                                                                    class="btn btn-icon btn-light btn-hover-success btn-sm" 
                                                                    data-toggle="tooltip" 
                                                                    title="Update Payment Status"
                                                                    onclick="updatePaymentStatus({{ $order->id }}, '{{ $order->payment_status }}')">
                                                                <i class="flaticon2-check-mark"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center text-muted py-10">No orders found</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!--end::Card-->
                    </div>
                </div>
                <!--end::Row-->

                <!--begin::Row - Order Status Breakdown-->
                <div class="row mb-8">
                    <div class="col-xl-6">
                        <!--begin::Card-->
                        <div class="card card-custom">
                            <div class="card-header border-0 pt-6">
                                <div class="card-title">
                                    <h3 class="card-label font-weight-bolder">Order Status Breakdown</h3>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-center justify-content-between mb-6">
                                        <span class="text-muted font-weight-bold">Pending</span>
                                        <span class="text-dark-75 font-weight-bolder font-size-lg">{{ $pendingOrders }}</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-6">
                                        <span class="text-muted font-weight-bold">Processing</span>
                                        <span class="text-dark-75 font-weight-bolder font-size-lg">{{ $processingOrders }}</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-6">
                                        <span class="text-muted font-weight-bold">Shipped</span>
                                        <span class="text-dark-75 font-weight-bolder font-size-lg">{{ $shippedOrders }}</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-6">
                                        <span class="text-muted font-weight-bold">Delivered</span>
                                        <span class="text-dark-75 font-weight-bolder font-size-lg">{{ $deliveredOrders }}</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="text-muted font-weight-bold">Cancelled</span>
                                        <span class="text-dark-75 font-weight-bolder font-size-lg">{{ $cancelledOrders }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Card-->
                    </div>
                    <div class="col-xl-6">
                        <!--begin::Card-->
                        <div class="card card-custom">
                            <div class="card-header border-0 pt-6">
                                <div class="card-title">
                                    <h3 class="card-label font-weight-bolder">Payment Status Breakdown</h3>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-center justify-content-between mb-6">
                                        <span class="text-muted font-weight-bold">Paid</span>
                                        <span class="text-dark-75 font-weight-bolder font-size-lg">{{ $paidOrders }}</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-6">
                                        <span class="text-muted font-weight-bold">Pending</span>
                                        <span class="text-dark-75 font-weight-bolder font-size-lg">{{ $pendingPaymentOrders }}</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-6">
                                        <span class="text-muted font-weight-bold">Failed</span>
                                        <span class="text-dark-75 font-weight-bolder font-size-lg">{{ $failedPaymentOrders }}</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="text-muted font-weight-bold">Refunded</span>
                                        <span class="text-dark-75 font-weight-bolder font-size-lg">{{ $refundedPaymentOrders }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Card-->
                    </div>
                </div>
                <!--end::Row-->
<!--begin::Row - Charts and Tables-->
<div class="row mb-8">
                    <div class="col-xl-8">
                        <!--begin::Card-->
                        <div class="card card-custom">
                            <div class="card-header border-0 pt-6">
                                <div class="card-title">
                                    <h3 class="card-label font-weight-bolder">Orders & Revenue Statistics (Last 6 Months)</h3>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div id="kt_chart_orders_revenue" class="chart-container"></div>
                            </div>
                        </div>
                        <!--end::Card-->
                    </div>
                    <div class="col-xl-4">
                        <!--begin::Card-->
                        <div class="card card-custom">
                            <div class="card-header border-0 pt-6">
                                <div class="card-title">
                                    <h3 class="card-label font-weight-bolder">Order Status</h3>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div id="kt_chart_order_status" class="chart-container"></div>
                            </div>
                        </div>
                        <!--end::Card-->
                    </div>
                </div>
                <!--end::Row-->
                <!--end::Dashboard-->
            </div>
            <!--end::Container-->
        </div>
        <!--end::Entry-->
    </div>
    <!--end::Content-->
@endsection

@push('scripts')
    <script src="{{ asset('cp_assets/plugins/custom/amcharts/amcharts.bundle.js') }}"></script>
    <script>
        am4core.ready(function() {
            // Orders and Revenue Chart
            var chart = am4core.create("kt_chart_orders_revenue", am4charts.XYChart);
            chart.data = @json($ordersByMonth);
            
            var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
            categoryAxis.dataFields.category = "month";
            categoryAxis.renderer.grid.template.location = 0;
            categoryAxis.renderer.minGridDistance = 30;
            categoryAxis.renderer.labels.template.fontSize = 12;
            
            var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
            valueAxis.title.text = "Count / Revenue";
            valueAxis.title.fontSize = 14;
            valueAxis.renderer.labels.template.fontSize = 12;
            
            // Orders series
            var series1 = chart.series.push(new am4charts.ColumnSeries());
            series1.dataFields.valueY = "count";
            series1.dataFields.categoryX = "month";
            series1.name = "Orders";
            series1.columns.template.tooltipText = "{categoryX}: {valueY} orders";
            series1.columns.template.fill = am4core.color("#1BC5BD");
            series1.columns.template.stroke = am4core.color("#1BC5BD");
            series1.columns.template.column.cornerRadiusTopLeft = 8;
            series1.columns.template.column.cornerRadiusTopRight = 8;
            
            // Revenue series
            var series2 = chart.series.push(new am4charts.LineSeries());
            series2.dataFields.valueY = "revenue";
            series2.dataFields.categoryX = "month";
            series2.name = "Revenue (QAR)";
            series2.stroke = am4core.color("#F3A3B7");
            series2.strokeWidth = 3;
            series2.tooltipText = "{categoryX}: {valueY} QAR";
            series2.tensionX = 0.7;
            
            var bullet = series2.bullets.push(new am4charts.CircleBullet());
            bullet.circle.radius = 5;
            bullet.circle.fill = am4core.color("#F3A3B7");
            bullet.circle.stroke = am4core.color("#fff");
            bullet.circle.strokeWidth = 2;
            
            chart.legend = new am4charts.Legend();
            chart.legend.position = "top";
            chart.legend.paddingBottom = 20;
            chart.legend.labels.template.maxWidth = 95;
            chart.legend.fontSize = 12;
            
            // Order Status Chart
            var chart2 = am4core.create("kt_chart_order_status", am4charts.PieChart);
            chart2.data = [
                { status: "Pending", count: {{ $pendingOrders }} },
                { status: "Processing", count: {{ $processingOrders }} },
                { status: "Shipped", count: {{ $shippedOrders }} },
                { status: "Delivered", count: {{ $deliveredOrders }} },
                { status: "Cancelled", count: {{ $cancelledOrders }} }
            ];
            
            var pieSeries = chart2.series.push(new am4charts.PieSeries());
            pieSeries.dataFields.value = "count";
            pieSeries.dataFields.category = "status";
            pieSeries.slices.template.stroke = am4core.color("#fff");
            pieSeries.slices.template.strokeWidth = 2;
            pieSeries.slices.template.strokeOpacity = 1;
            pieSeries.slices.template.cornerRadius = 5;
            pieSeries.slices.template.innerCornerRadius = 3;
            
            pieSeries.colors.list = [
                am4core.color("#FFA800"),
                am4core.color("#1BC5BD"),
                am4core.color("#8950FC"),
                am4core.color("#0BB783"),
                am4core.color("#F64E60")
            ];
            
            pieSeries.labels.template.fontSize = 11;
            pieSeries.ticks.template.disabled = true;
            
            chart2.legend = new am4charts.Legend();
            chart2.legend.position = "bottom";
            chart2.legend.fontSize = 11;
            chart2.legend.labels.template.maxWidth = 120;
        });
    </script>
    <script src="{{ asset("cp_assets/js/sweetalert2@11.js") }}"></script>
    <script>
        // Update order status
        function updateOrderStatus(id, currentStatus) {
            const statusOptions = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
            const statusLabels = {
                'pending': 'Pending',
                'processing': 'Processing',
                'shipped': 'Shipped',
                'delivered': 'Delivered',
                'cancelled': 'Cancelled'
            };

            let optionsHtml = statusOptions.map(status => {
                const selected = status === currentStatus ? 'selected' : '';
                return `<option value="${status}" ${selected}>${statusLabels[status]}</option>`;
            }).join('');

            Swal.fire({
                title: 'Update Order Status',
                html: `<select id="orderStatus" class="swal2-input form-control">${optionsHtml}</select>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Update',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-primary font-weight-bold',
                    cancelButton: 'btn btn-light font-weight-bold'
                },
                buttonsStyling: false,
                didOpen: () => {
                    const select = document.getElementById('orderStatus');
                    select.style.width = '100%';
                    select.style.padding = '0.5rem';
                },
                preConfirm: () => {
                    return document.getElementById('orderStatus').value;
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    $.ajax({
                        url: "{{ route('admin.orders.updateStatus', ':id') }}".replace(':id', id),
                        type: 'PUT',
                        data: {
                            _token: '{{ csrf_token() }}',
                            status: result.value
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

        // Update payment status
        function updatePaymentStatus(id, currentStatus) {
            const statusOptions = ['pending', 'paid', 'failed', 'refunded'];
            const statusLabels = {
                'pending': 'Pending',
                'paid': 'Paid',
                'failed': 'Failed',
                'refunded': 'Refunded'
            };

            let optionsHtml = statusOptions.map(status => {
                const selected = status === currentStatus ? 'selected' : '';
                return `<option value="${status}" ${selected}>${statusLabels[status]}</option>`;
            }).join('');

            Swal.fire({
                title: 'Update Payment Status',
                html: `<select id="paymentStatus" class="swal2-input form-control">${optionsHtml}</select>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Update',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-primary font-weight-bold',
                    cancelButton: 'btn btn-light font-weight-bold'
                },
                buttonsStyling: false,
                didOpen: () => {
                    const select = document.getElementById('paymentStatus');
                    select.style.width = '100%';
                    select.style.padding = '0.5rem';
                },
                preConfirm: () => {
                    return document.getElementById('paymentStatus').value;
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    $.ajax({
                        url: "{{ route('admin.orders.updatePaymentStatus', ':id') }}".replace(':id', id),
                        type: 'PUT',
                        data: {
                            _token: '{{ csrf_token() }}',
                            payment_status: result.value
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
    </script>
@endpush
