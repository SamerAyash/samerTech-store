@extends('admin.layout.app')

@push('style')
    <link href="{{ asset('cp_assets/plugins/custom/datatables/datatables.bundle.css') }}"
        rel="stylesheet" type="text/css" />
    <style>
        /* Responsive styles for discount codes pages */
        @media (max-width: 767.98px) {
            .discount-codes-subheader-title {
                font-size: 1rem;
                margin-right: 0.5rem !important;
            }
            .discount-codes-breadcrumb {
                font-size: 0.75rem;
            }
            .discount-codes-table-wrapper {
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
                <h5 class="text-dark font-weight-bold my-1 mr-5 discount-codes-subheader-title">Discount Codes</h5>
                <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm discount-codes-breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.home') }}" class="text-muted">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="text-muted">Discount Codes</span>
                    </li>
                </ul>
            </div>
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.discount-codes.create') }}" class="btn btn-primary font-weight-bold">
                    <i class="flaticon2-plus"></i> 
                    <span class="d-none d-sm-inline">Add New</span>
                </a>
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
                        Discount Codes Table
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="discount-codes-table-wrapper">
                        <table id="kt_datatable" class="table table-striped table-row-bordered gy-5">
                            <thead>
                                <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="min-w-50px">ID</th>
                                    <th class="min-w-150px">Code</th>
                                    <th class="min-w-150px">Name</th>
                                    <th class="min-w-100px">Type</th>
                                    <th class="min-w-100px">Value</th>
                                    <th class="min-w-100px">Usage</th>
                                    <th class="min-w-100px">Status</th>
                                    <th class="min-w-100px">Start Date</th>
                                    <th class="min-w-100px">End Date</th>
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
                        url: "{{ route('admin.discount-codes.data') }}",
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
                            data: 'code',
                            name: 'code'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'discount_type',
                            name: 'discount_type',
                            className: 'text-center'
                        },
                        {
                            data: 'discount_value',
                            name: 'discount_value',
                            className: 'text-center'
                        },
                        {
                            data: 'usage_limit',
                            name: 'usage_limit',
                            className: 'text-center'
                        },
                        {
                            data: 'status',
                            name: 'status',
                            className: 'text-center'
                        },
                        {
                            data: 'start_date',
                            name: 'start_date'
                        },
                        {
                            data: 'end_date',
                            name: 'end_date'
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
                        $('#kt_datatable').DataTable().ajax.reload();
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

