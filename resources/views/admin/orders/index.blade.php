@extends('admin.layout.app')

@push('style')
    <link href="{{ asset('cp_assets/plugins/custom/datatables/datatables.bundle.css') }}"
        rel="stylesheet" type="text/css" />
    <style>
        /* Responsive styles for orders pages */
        @media (max-width: 767.98px) {
            .orders-subheader-title {
                font-size: 1rem;
                margin-right: 0.5rem !important;
            }
            .orders-breadcrumb {
                font-size: 0.75rem;
            }
            .orders-table-wrapper {
                overflow-x: auto;
            }
        }
    </style>
@endpush

@section('content')
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-2">
                <h5 class="text-dark font-weight-bold my-1 mr-5 orders-subheader-title">Orders</h5>
                <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm orders-breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.home') }}" class="text-muted">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="text-muted">Orders</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!--end::Subheader-->

    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            @includeIf('admin.component.alert')

            <div class="card card-custom">
                <div class="card-header border-0 pt-6">
                    <div class="card-title w-100">
                        Orders Table
                    </div>
                    <div class="card-toolbar">
                        <a href="{{ route('admin.orders.create') }}" class="btn btn-primary font-weight-bold">
                            <i class="flaticon2-plus"></i> Create Guest Order
                        </a>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="orders-table-wrapper">
                        <table id="kt_datatable" class="table table-striped table-row-bordered gy-5">
                            <thead>
                                <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="min-w-50px">ID</th>
                                    <th class="min-w-150px">Order Number</th>
                                    <th class="min-w-150px">Customer</th>
                                    <th class="min-w-100px">Total Amount</th>
                                    <th class="min-w-100px">Payment Status</th>
                                    <th class="min-w-100px">Status</th>
                                    <th class="min-w-100px">Items</th>
                                    <th class="min-w-150px">Created At</th>
                                    <th class="text-end min-w-100px">Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->
@endsection

@push('scripts')
    <script src="{{ asset('cp_assets/plugins/custom/datatables/datatables.bundle.js') }}">
    </script>
    <script src="{{ asset("cp_assets/js/sweetalert2@11.js") }}"></script>
    <script>
        "use strict";
        var KTDatatablesServerSide = function () {
            var table;

            var initDatatable = function () {
                table = $('#kt_datatable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('admin.orders.data') }}",
                        type: 'GET',
                        error: function (xhr, error, code) {
                            console.log('DataTable Error:', error, code);
                            console.log('Response:', xhr.responseText);
                        }
                    },
                    columns: [{
                            data: 'id',
                            name: 'id',
                            className: 'text-center'
                        },
                        {
                            data: 'order_number',
                            name: 'order_number'
                        },
                        {
                            data: 'user_name',
                            name: 'user_name',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'total_amount',
                            name: 'total_amount'
                        },
                        {
                            data: 'payment_status',
                            name: 'payment_status',
                            className: 'text-center'
                        },
                        {
                            data: 'status',
                            name: 'status',
                            className: 'text-center'
                        },
                        {
                            data: 'items_count',
                            name: 'items_count',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
                        },
                        {
                            data: 'actions',
                            name: 'actions',
                            orderable: false,
                            searchable: false,
                            className: 'text-end'
                        }
                    ],
                    responsive: true,
                    order: [
                        [0, 'desc']
                    ],
                    pageLength: 10,
                    language: {
                        processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
                    },
                    drawCallback: function () {
                        if (typeof KTMenu !== 'undefined' && typeof KTMenu.createInstances === 'function') {
                            KTMenu.createInstances();
                        }
                    }
                });

                return table;
            };

            return {
                init: function () {
                    initDatatable();
                }
            };
        }();

        jQuery(document).ready(function () {
            if (typeof $.fn.DataTable !== 'undefined') {
                KTDatatablesServerSide.init();
            } else {
                console.error('DataTables is not loaded');
            }
        });

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
                                    $('#kt_datatable').DataTable().ajax.reload();
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
                                    $('#kt_datatable').DataTable().ajax.reload();
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

