@extends('admin.layout.app')

@push('style')
    <style>
        /* Responsive styles for order show page */
        @media (max-width: 767.98px) {
            .order-show-subheader-title {
                font-size: 1rem;
                margin-right: 0.5rem !important;
            }
            .order-show-breadcrumb {
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
                <h5 class="text-dark font-weight-bold my-1 mr-5 order-show-subheader-title">Order Details</h5>
                <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm order-show-breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.home') }}" class="text-muted">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.orders.index') }}" class="text-muted">Orders</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="text-muted">{{ $order->order_number }}</span>
                    </li>
                </ul>
            </div>
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.orders.index') }}" class="btn btn-light font-weight-bold">
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
                <!--begin::Order Info-->
                <div class="col-xl-8">
                    <div class="card card-custom mb-5">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="card-label">Order Information</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-5">
                                <div class="col-md-6">
                                    <label class="font-weight-bold text-dark">Order Number:</label>
                                    <p class="text-muted">{{ $order->order_number }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="font-weight-bold text-dark">Order ID:</label>
                                    <p class="text-muted">#{{ $order->id }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="font-weight-bold text-dark">Order Status:</label>
                                    <div class="mt-2">
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
                                        <span class="badge badge-{{ $color }} badge-lg">{{ ucfirst($order->status) }}</span>
                                        <button type="button" class="btn btn-sm btn-light ml-2" onclick="updateOrderStatus({{ $order->id }}, '{{ $order->status }}')">
                                            <i class="flaticon2-settings"></i> Change
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="font-weight-bold text-dark">Payment Status:</label>
                                    <div class="mt-2">
                                        @php
                                            $paymentColors = [
                                                'pending' => 'warning',
                                                'paid' => 'success',
                                                'failed' => 'danger',
                                                'refunded' => 'info'
                                            ];
                                            $paymentColor = $paymentColors[$order->payment_status] ?? 'secondary';
                                        @endphp
                                        <span class="badge badge-{{ $paymentColor }} badge-lg">{{ ucfirst($order->payment_status) }}</span>
                                        <button type="button" class="btn btn-sm btn-light ml-2" onclick="updatePaymentStatus({{ $order->id }}, '{{ $order->payment_status }}')">
                                            <i class="flaticon2-check-mark"></i> Change
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="font-weight-bold text-dark">Created At:</label>
                                    <p class="text-muted">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="font-weight-bold text-dark">Updated At:</label>
                                    <p class="text-muted">{{ $order->updated_at->format('M d, Y h:i A') }}</p>
                                </div>
                            </div>

                            <h4 class="font-weight-bold mb-5">Payment Information</h4>
                            <div class="row">
                                <div class="col-md-6 mb-5">
                                    <label class="font-weight-bold text-dark">Payment Method:</label>
                                    <p class="text-muted">{{ $order->payment_method ?? '-' }}</p>
                                </div>
                                <div class="col-md-6 mb-5">
                                    <label class="font-weight-bold text-dark">Order Currency (Recorded):</label>
                                    <p class="text-muted">{{ $order->currency ?? 'QAR' }}</p>
                                </div>
                                <div class="col-md-6 mb-5">
                                    <label class="font-weight-bold text-dark">Currency Rate (at order time):</label>
                                    <p class="text-muted">{{ $order->currency_rate !== null ? number_format((float)$order->currency_rate, 6) : '-' }}</p>
                                </div>
                                <div class="col-md-6 mb-5">
                                    <label class="font-weight-bold text-dark">Transaction ID:</label>
                                    <p class="text-muted">{{ $order->payment_transaction_id ?? '-' }}</p>
                                </div>
                                @if($order->myfatoorah_invoice_id)
                                <div class="col-md-6 mb-5">
                                    <label class="font-weight-bold text-dark">MyFatoorah Invoice ID:</label>
                                    <p class="text-muted">{{ $order->myfatoorah_invoice_id }}</p>
                                </div>
                                @endif
                                @if($order->payment_url)
                                <div class="col-md-12 mb-5">
                                    <label class="font-weight-bold text-dark">Payment URL:</label>
                                    <p class="text-muted"><a href="{{ $order->payment_url }}" target="_blank" rel="noopener" class="text-primary">Open payment link</a></p>
                                </div>
                                @endif
                                @if($order->total_in_base_currency !== null)
                                <div class="col-md-12 mb-5">
                                    <label class="font-weight-bold text-dark">Equivalent in QAR (currency difference):</label>
                                    <p class="text-muted">{{ number_format($order->total_in_base_currency, 2) }} QAR</p>
                                </div>
                                @endif
                            </div>

                            <h4 class="font-weight-bold mb-5">Shipping Information</h4>
                            <div class="row">
                                <div class="col-md-12 mb-5">
                                    <label class="font-weight-bold text-dark">Shipping Address:</label>
                                    @if($order->shippingAddress)
                                        <p class="text-muted">
                                            {{ $order->shippingAddress->first_name }} {{ $order->shippingAddress->last_name }}<br>
                                            @if($order->shippingAddress->company){{ $order->shippingAddress->company }}<br>@endif
                                            {{ $order->shippingAddress->address }}<br>
                                            @if($order->shippingAddress->apartment){{ $order->shippingAddress->apartment }},<br>@endif
                                            {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->country }}<br>
                                            @if($order->shippingAddress->postal_code){{ $order->shippingAddress->postal_code }}<br>@endif
                                            Phone: {{ $order->shippingAddress->phone }}
                                        </p>
                                    @else
                                        <p class="text-muted">-</p>
                                    @endif
                                </div>
                                <div class="col-md-6 mb-5">
                                    <label class="font-weight-bold text-dark">Shipping Method:</label>
                                    <p class="text-muted">{{ $order->shipping_method }}</p>
                                </div>
                                <div class="col-md-6 mb-5">
                                    <label class="font-weight-bold text-dark">Shipping Cost:</label>
                                    <p class="text-muted">{{ number_format((float)$order->shipping_cost, 2) }} {{ $order->currency ?? 'QAR' }}</p>
                                </div>
                            </div>

                            @if($order->notes)
                            <h4 class="font-weight-bold mb-5 mt-5">Order Notes</h4>
                            <div class="row">
                                <div class="col-md-12">
                                    <p class="text-muted">{{ $order->notes }}</p>
                                </div>
                            </div>
                            @endif

                            @if($order->billingAddress)
                            <h4 class="font-weight-bold mb-5 mt-5">Billing Address</h4>
                            <div class="row">
                                <div class="col-md-12 mb-5">
                                    <p class="text-muted">
                                        {{ $order->billingAddress->first_name }} {{ $order->billingAddress->last_name }}<br>
                                        @if($order->billingAddress->company){{ $order->billingAddress->company }}<br>@endif
                                        {{ $order->billingAddress->address }}<br>
                                        @if($order->billingAddress->apartment){{ $order->billingAddress->apartment }},<br>@endif
                                        {{ $order->billingAddress->city }}, {{ $order->billingAddress->country }}<br>
                                        @if($order->billingAddress->postal_code){{ $order->billingAddress->postal_code }}<br>@endif
                                        Phone: {{ $order->billingAddress->phone }}
                                    </p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!--begin::Order Items-->
                    <div class="card card-custom">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="card-label">Order Items ({{ $order->orderItems->count() }})</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                @php $orderCurrency = $order->currency ?? 'QAR'; @endphp
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Product SKU</th>
                                            <th>Color</th>
                                            <th>Size</th>
                                            <th class="text-center">Qty</th>
                                            <th>Price</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($order->orderItems as $item)
                                            <tr>
                                                <td>
                                                    @if($item->product)
                                                        <a href="{{ route('admin.products.show', $item->product->ref_code) }}" class="text-primary font-weight-bold">
                                                            {{ $item->product->name }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">{{ $item->product_name }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $item->product_sku }}</td>
                                                <td>{{ $item->color ?? '-' }}</td>
                                                <td>{{ $item->size ?? '-' }}</td>
                                                <td class="text-center">{{ $item->quantity }}</td>
                                                <td>{{ number_format((float)$item->price, 2) }} {{ $orderCurrency }}</td>
                                                <td class="font-weight-bold">{{ number_format((float)$item->subtotal, 2) }} {{ $orderCurrency }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted">No items found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="7" class="text-right font-weight-bold">Subtotal:</td>
                                            <td class="font-weight-bold">{{ number_format((float)$order->subtotal, 2) }} {{ $orderCurrency }}</td>
                                        </tr>
                                        @if((float)($order->discount_amount ?? 0) > 0)
                                        <tr>
                                            <td colspan="7" class="text-right font-weight-bold">Discount:</td>
                                            <td class="font-weight-bold">-{{ number_format((float)$order->discount_amount, 2) }} {{ $orderCurrency }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td colspan="7" class="text-right font-weight-bold">Shipping:</td>
                                            <td class="font-weight-bold">{{ number_format((float)$order->shipping_cost, 2) }} {{ $orderCurrency }}</td>
                                        </tr>
                                        <tr class="bg-light">
                                            <td colspan="7" class="text-right font-weight-bold">Total:</td>
                                            <td class="font-weight-bold font-size-h5">{{ number_format((float)$order->total, 2) }} {{ $orderCurrency }}</td>
                                        </tr>
                                        @if($order->total_in_base_currency !== null)
                                        <tr class="bg-light">
                                            <td colspan="7" class="text-right font-weight-bold">Equivalent (QAR):</td>
                                            <td class="font-weight-bold font-size-h5">{{ number_format($order->total_in_base_currency, 2) }} QAR</td>
                                        </tr>
                                        @endif
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!--end::Order Items-->
                </div>
                <!--end::Order Info-->

                <!--begin::Customer Info-->
                <div class="col-xl-4">
                    <div class="card card-custom">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="card-label">Customer Information</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($order->user)
                                <div class="mb-5">
                                    <label class="font-weight-bold text-dark">Name:</label>
                                    <p class="text-muted">
                                        <a href="{{ route('admin.users.show', $order->user) }}" class="text-primary">
                                            {{ $order->user->name }}
                                        </a>
                                    </p>
                                </div>
                                <div class="mb-5">
                                    <label class="font-weight-bold text-dark">Email:</label>
                                    <p class="text-muted">{{ $order->user->email }}</p>
                                </div>
                                <div class="mb-5">
                                    <label class="font-weight-bold text-dark">Phone:</label>
                                    <p class="text-muted">{{ $order->user->phone ?? '-' }}</p>
                                </div>
                                <div class="mb-5">
                                    <label class="font-weight-bold text-dark">Total Orders:</label>
                                    <p class="text-muted">{{ $order->user->orders()->count() }}</p>
                                </div>
                            @else
                                <div class="mb-5">
                                    <label class="font-weight-bold text-dark">Customer Type:</label>
                                    <p class="text-muted">
                                        <span class="badge badge-secondary">Guest</span>
                                    </p>
                                </div>
                                <div class="mb-5">
                                    <label class="font-weight-bold text-dark">Name:</label>
                                    <p class="text-muted">{{ $order->guest_name ?? '-' }}</p>
                                </div>
                                <div class="mb-5">
                                    <label class="font-weight-bold text-dark">Email:</label>
                                    <p class="text-muted">{{ $order->guest_email ?? '-' }}</p>
                                </div>
                                <div class="mb-5">
                                    <label class="font-weight-bold text-dark">Phone:</label>
                                    <p class="text-muted">{{ $order->guest_phone ?? ($order->shippingAddress->phone ?? '-') }}</p>
                                </div>
                                @if($order->shippingAddress)
                                <div class="mb-5">
                                    <label class="font-weight-bold text-dark">Shipping Address:</label>
                                    <p class="text-muted">
                                        {{ $order->shippingAddress->first_name }} {{ $order->shippingAddress->last_name }}<br>
                                        {{ $order->shippingAddress->address }}<br>
                                        {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->country }}
                                    </p>
                                </div>
                                @endif
                            @endif
                        </div>
                    </div>

                    <!--begin::Order Summary-->
                    <div class="card card-custom mt-5">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="card-label">Order Summary</h3>
                            </div>
                        </div>
                        @php $orderCurrency = $order->currency ?? 'QAR'; @endphp
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Subtotal:</span>
                                <span class="font-weight-bold">{{ number_format((float)$order->subtotal, 2) }} {{ $orderCurrency }}</span>
                            </div>
                            @if((float)($order->discount_amount ?? 0) > 0)
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Discount @if($order->discountCode)({{ $order->discountCode->code }})@endif:</span>
                                <span class="font-weight-bold">-{{ number_format((float)$order->discount_amount, 2) }} {{ $orderCurrency }}</span>
                            </div>
                            @endif
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Shipping:</span>
                                <span class="font-weight-bold">{{ number_format((float)$order->shipping_cost, 2) }} {{ $orderCurrency }}</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span class="font-weight-bold font-size-h5">Total:</span>
                                <span class="font-weight-bold font-size-h5 text-primary">{{ number_format((float)$order->total, 2) }} {{ $orderCurrency }}</span>
                            </div>
                            @if($order->total_in_base_currency !== null)
                            <div class="d-flex justify-content-between mt-2">
                                <span class="text-muted">Equivalent (QAR):</span>
                                <span class="font-weight-bold">{{ number_format($order->total_in_base_currency, 2) }} QAR</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    <!--end::Order Summary-->
                </div>
                <!--end::Customer Info-->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
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

