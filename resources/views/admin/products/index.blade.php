@extends('admin.layout.app')

@push('style')
    <link href="{{ asset('cp_assets/plugins/custom/datatables/datatables.bundle.css') }}"
        rel="stylesheet" type="text/css" />
    <style>
        .dataTables_filter input {
            width: 210px !important; /* كبر على مزاجك */
        }
        /* Responsive styles for products pages */
        @media (max-width: 767.98px) {
            .products-subheader-title {
                font-size: 1rem;
                margin-right: 0.5rem !important;
            }
            .products-breadcrumb {
                font-size: 0.75rem;
            }
            .products-table-wrapper {
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
                <h5 class="text-dark font-weight-bold my-1 mr-5 products-subheader-title">Products</h5>
                <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm products-breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.home') }}" class="text-muted">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="text-muted">Products</span>
                    </li>
                </ul>
            </div>
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.products.create') }}"
                    class="btn btn-primary font-weight-bold">
                    <i class="flaticon2-plus"></i>
                    <span class="d-inline">Create Product</span>
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
                        Products Table
                    </div>
                </div>
                <div class="card-body pt-0">
                    <!--begin::Filters-->
                    <div class="mb-7">
                        <div class="row align-items-center">
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                                <label class="font-weight-bold text-muted font-size-sm mb-1">Status</label>
                                <select id="filter_status" class="form-control form-control-sm form-control-solid">
                                    <option value="" selected>All</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                                <label class="font-weight-bold text-muted font-size-sm mb-1">Images</label>
                                <select id="filter_has_images" class="form-control form-control-sm form-control-solid">
                                    <option value="" selected>All</option>
                                    <option value="1">With Images</option>
                                    <option value="0">Without Images</option>
                                </select>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6 mt-4 d-flex align-items-end">
                                <button type="button" id="btn_reset_filters" class="btn btn-sm btn-light-primary font-weight-bold">
                                    <i class="flaticon2-refresh-arrow"></i> Reset Filters
                                </button>
                            </div>
                        </div>
                    </div>
                    <!--end::Filters-->
                    <div class="products-table-wrapper">
                        <table id="kt_datatable" class="table table-striped table-row-bordered gy-5">
                            <thead>
                                <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="min-w-100px">SKU</th>
                                    <th class="min-w-100px">Name</th>
                                    <th class="min-w-100px">Status</th>
                                    <th class="min-w-80px">Images</th>
                                    <th class="min-w-100px">Last Synced</th>
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
                        url: "{{ route('admin.products.data') }}",
                        type: 'GET',
                        data: function (d) {
                            d.filter_status = $('#filter_status').val();
                            d.filter_has_images = $('#filter_has_images').val();
                        },
                        error: function (xhr, error, code) {
                            console.log('DataTable Error:', error, code);
                            console.log('Response:', xhr.responseText);
                        }
                    },
                    columns: [{
                            data: 'ref_code',
                            name: 'ref_code'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'status',
                            name: 'status',
                            className: 'text-center'
                        },
                        {
                            data: 'images_count',
                            name: 'images_count',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        },
                        {
                            data: 'last_synced',
                            name: 'last_synced',
                            searchable: false
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

            var initFilters = function () {
                // Reload table when any filter changes
                $('#filter_status, #filter_has_images').on('change', function () {
                    table.ajax.reload();
                });

                // Reset all filters
                $('#btn_reset_filters').on('click', function () {
                    $('#filter_status').val('');
                    $('#filter_has_images').val('');
                    table.ajax.reload();
                });
            };

            return {
                init: function () {
                    initDatatable();
                    initFilters();
                    $('.dataTables_filter input').attr('placeholder', 'Search by SKU or Name');
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

    </script>
@endpush

