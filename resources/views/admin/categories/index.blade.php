@extends('admin.layout.app')

@push('style')
    <link href="{{ asset('cp_assets/plugins/custom/datatables/datatables.bundle.css') }}"
        rel="stylesheet" type="text/css" />
    <style>
        /* Responsive styles for category pages */
        @media (max-width: 991.98px) {
            .category-search-input {
                width: 100% !important;
                margin-bottom: 1rem;
            }
            .category-card-header {
                flex-direction: column;
                align-items: stretch !important;
            }
            .category-card-toolbar {
                margin-top: 1rem;
            }
        }
        @media (max-width: 767.98px) {
            .category-subheader-title {
                font-size: 1rem;
                margin-right: 0.5rem !important;
            }
            .category-breadcrumb {
                font-size: 0.75rem;
            }
            .category-table-wrapper {
                overflow-x: auto;
            }
        }
        .category-search-wrapper {
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
                <h5 class="text-dark font-weight-bold my-1 mr-5 category-subheader-title">Categories</h5>
                <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm category-breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.home') }}" class="text-muted">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="text-muted">Categories</span>
                    </li>
                </ul>
            </div>
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.categories.sort') }}"
                    class="btn btn-primary font-weight-bold mr-2">
                    <i class="flaticon2-sort"></i>
                    <span>Sort categories</span>
                </a>
                <a href="{{ route('admin.categories.create') }}"
                    class="btn btn-primary font-weight-bold">
                    <i class="flaticon2-plus"></i>
                    <span class="d-none d-md-inline">Add Category</span>
                    <span class="d-inline d-md-none">Add</span>
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
                <div class="card-header border-0 pt-6 category-card-header">
                    <div class="card-title w-100">
                        Categories Table
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="category-table-wrapper">
                        <table id="kt_datatable" class="table table-striped table-row-bordered gy-5">
                            <thead>
                                <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="min-w-100px">Name</th>
                                    <th class="min-w-100px">Slug</th>
                                    <th class="min-w-100px">Name (AR)</th>
                                    <th class="min-w-100px">Slug (AR)</th>
                                    <th class="min-w-100px">Parent</th>
                                    <th class="min-w-100px">Status</th>
                                    <th class="min-w-100px">Children</th>
                                    <th class="min-w-120px">Created At</th>
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
                        url: "{{ route('admin.categories.data') }}",
                        type: 'GET',
                        error: function (xhr, error, code) {
                            console.log('DataTable Error:', error, code);
                            console.log('Response:', xhr.responseText);
                        }
                    },
                    columns: [
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'slug',
                            name: 'slug',
                            orderable: false,
                            searchable: false,
                        },
                        {
                            data: 'name_ar',
                            name: 'name_ar',
                            orderable: false,
                            searchable: false,
                            className: 'text-right'
                        },
                        {
                            data: 'slug_ar',
                            name: 'slug_ar',
                            orderable: false,
                            searchable: false,
                            className: 'text-right'
                        },
                        {
                            data: 'parent',
                            name: 'parent_id',
                            orderable: true,
                            searchable: false,
                            className: ''
                        },
                        {
                            data: 'status',
                            name: 'status',
                            className: 'text-center'
                        },
                        {
                            data: 'children_count',
                            name: 'children_count',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at',
                            className: ''
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

                // Name filter
                var nameSearch = $('#kt_datatable_search_name');
                nameSearch.on('keyup', function () {
                    table.column(1).search(this.value).draw();
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
            // Wait for DataTables to be available
            if (typeof $.fn.DataTable !== 'undefined') {
                KTDatatablesServerSide.init();
            } else {
                console.error('DataTables is not loaded');
            }
        });

        // Delete confirmation with SweetAlert2
        function deleteCategory(id, name, productsCount, childrenCount) {
            let warningHtml = `<p class="mb-2">Are you sure you want to delete <strong>${name}</strong>?</p>`;

            if (productsCount > 0) {
                warningHtml +=
                    `<p class="text-danger mb-1">Confirming this deletion will detach ${productsCount} product(s) from this category.</p>`;
            }

            if (childrenCount > 0) {
                warningHtml +=
                    `<p class="text-warning mb-1">This category has ${childrenCount} child category(ies) that will be moved to root level.</p>`;
            }

            warningHtml += `<p class="text-muted font-size-sm mt-2">This action cannot be undone.</p>`;

            Swal.fire({
                title: 'Delete Category?',
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
                        url: "{{ route('admin.categories.destroy', ':id') }}"
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

    </script>
@endpush
