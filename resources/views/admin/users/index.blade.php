@extends('admin.layout.app')

@push('style')
    <link href="{{ asset('cp_assets/plugins/custom/datatables/datatables.bundle.css') }}"
        rel="stylesheet" type="text/css" />
    <style>
        /* Responsive styles for users pages */
        @media (max-width: 991.98px) {
            .users-search-input {
                width: 100% !important;
                margin-bottom: 1rem;
            }
            .users-card-header {
                flex-direction: column;
                align-items: stretch !important;
            }
        }
        @media (max-width: 767.98px) {
            .users-subheader-title {
                font-size: 1rem;
                margin-right: 0.5rem !important;
            }
            .users-breadcrumb {
                font-size: 0.75rem;
            }
            .users-table-wrapper {
                overflow-x: auto;
            }
        }
        .users-search-wrapper {
            position: relative;
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
                <h5 class="text-dark font-weight-bold my-1 mr-5 category-subheader-title">Users</h5>
                <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm category-breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.home') }}" class="text-muted">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="text-muted">Users</span>
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
                <div class="card-header border-0 pt-6 category-card-header">
                    <div class="card-title w-100">
                        Users Table
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="users-table-wrapper">
                        <table id="kt_datatable" class="table table-striped table-row-bordered gy-5 dataTable no-footer dtr-inline collapsed">
                            <thead>
                                <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="min-w-50px">ID</th>
                                    <th class="min-w-100px">Name</th>
                                    <th class="min-w-100px">Email</th>
                                    <th class="min-w-120px">Phone</th>
                                    <th class="min-w-100px">Status</th>
                                    <th class="min-w-120px">Email Verified</th>
                                    <th class="min-w-100px">Orders</th>
                                    <th class="min-w-120px">Total Spent</th>
                                    <th class="min-w-120px">Created At</th>
                                    <th class="min-w-120px">Last Login</th>
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
                        url: "{{ route('admin.users.data') }}",
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
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'phone',
                            name: 'phone',  
                        },
                        {
                            data: 'status',
                            name: 'status',
                            className: 'text-center'
                        },
                        {
                            data: 'email_verified',
                            name: 'email_verified',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        },
                        {
                            data: 'orders_count',
                            name: 'orders_count',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        },
                        {
                            data: 'total_spent',
                            name: 'total_spent',
                            orderable: false,
                            searchable: false,
                        },
                        {
                            data: 'created_at',
                            name: 'created_at',
                            
                        },
                        {
                            data: 'last_login',
                            name: 'last_login',
                            orderable: false,
                            searchable: false,
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

                // Global search filter
                var searchInput = $('#kt_datatable_search');
                searchInput.on('keyup', function () {
                    table.search(this.value).draw();
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

        // Delete confirmation with SweetAlert2
        function deleteUser(id, name, ordersCount) {
            let warningHtml = `<p class="mb-2">Are you sure you want to delete <strong>${name}</strong>?</p>`;

            if (ordersCount > 0) {
                warningHtml +=
                    `<p class="text-danger mb-1">This user has ${ordersCount} order(s) that will be deleted.</p>`;
            }

            warningHtml += `<p class="text-muted font-size-sm mt-2">This action cannot be undone.</p>`;

            Swal.fire({
                title: 'Delete User?',
                html: warningHtml,
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
                        url: "{{ route('admin.users.destroy', ':id') }}"
                            .replace(':id', id),
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

        // Update status function
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

