@extends('admin.layout.app')

@push('style')
    <link href="{{ asset('cp_assets/plugins/custom/datatables/datatables.bundle.css') }}"
        rel="stylesheet" type="text/css" />
    <link href="{{ asset('cp_assets/plugins/custom/select2/select2.bundle.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .dataTables_filter input {
            width: 210px !important;
        }
        .product-search-wrapper {
            margin-bottom: 20px;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #3699FF;
        }
        .select2-results__option {
            padding: 8px 12px;
        }
        .product-option {
            display: flex;
            align-items: center;
        }
        .product-option img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            margin-left: 10px;
            border-radius: 4px;
        }
        .product-option-info {
            flex: 1;
        }
        .product-option-info .ref-code {
            font-weight: bold;
            color: #212529;
        }
        .product-option-info .name {
            font-size: 0.875rem;
            color: #6c757d;
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
                <h5 class="text-dark font-weight-bold my-1 mr-5">{{ $title }}</h5>
                <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.home') }}" class="text-muted">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.featured-products.index', ['section' => 'A']) }}" class="text-muted">Home Featured</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="text-muted">Section {{ $section }}</span>
                    </li>
                </ul>
            </div>
            <div class="d-flex align-items-center">
                <div class="btn-group mr-2">
                    <a href="{{ route('admin.featured-products.index', ['section' => 'A']) }}" 
                       class="btn btn-sm {{ $section === 'A' ? 'btn-primary' : 'btn-light' }}">
                        Section A
                    </a>
                    <a href="{{ route('admin.featured-products.index', ['section' => 'B']) }}" 
                       class="btn btn-sm {{ $section === 'B' ? 'btn-primary' : 'btn-light' }}">
                        Section B
                    </a>
                </div>
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
                        <h3 class="card-label">{{ $title }} Products ({{ $currentCount }}/4)</h3>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <!--begin::Add Product Search-->
                    <div class="product-search-wrapper">
                        <label class="font-weight-bold mb-3">Add a new product:</label>
                        @if($currentCount < 4)
                            <select id="product_search" class="form-control form-control-solid" style="width: 100%;">
                                <option></option>
                            </select>
                            <span class="form-text text-muted">Search by SKU or name and select the product to add (Maximum 4 products)</span>
                        @else
                            <div class="alert alert-warning">
                                <i class="flaticon2-information"></i>
                                <strong>Note:</strong> Maximum limit reached (4 products). Please delete a product to add a new one.
                            </div>
                        @endif
                    </div>
                    <!--end::Add Product Search-->
                    
                    <table id="kt_datatable" class="table table-striped table-row-bordered gy-5">
                        <thead>
                            <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                <th class="min-w-100px">Product Image</th>
                                <th class="min-w-100px">SKU</th>
                                <th class="min-w-150px">Product Name</th>
                                <th class="min-w-100px">Added At</th>
                                <th class="text-end min-w-100px">Actions</th>
                            </tr>
                        </thead>
                    </table>
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
    <script src="{{ asset('cp_assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('cp_assets/plugins/custom/select2/select2.bundle.js') }}"></script>
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
                        url: "{{ route('admin.featured-products.data') }}",
                        type: 'GET',
                        data: {
                            section: '{{ $section }}'
                        },
                        error: function (xhr, error, code) {
                            console.log('DataTable Error:', error, code);
                            console.log('Response:', xhr.responseText);
                        }
                    },
                    columns: [{
                            data: 'product_image',
                            name: 'product_image',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'product_ref_code',
                            name: 'product_ref_code'
                        },
                        {
                            data: 'product_name',
                            name: 'product_name'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at',
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
                    order: [[3, 'desc']],
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
                    $('.dataTables_filter input').attr('placeholder', 'Search by SKU or Product Name');
                }
            };
        }();

        jQuery(document).ready(function () {
            if (typeof $.fn.DataTable !== 'undefined') {
                KTDatatablesServerSide.init();
            } else {
                console.error('DataTables is not loaded');
            }

            @if($currentCount < 4)
            // Initialize Select2 for product search
            $('#product_search').select2({
                placeholder: 'Search for a product (SKU, Name)...',
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return "No results found";
                    },
                    searching: function() {
                        return "Searching...";
                    }
                },
                ajax: {
                    url: "{{ route('admin.featured-products.search') }}",
                    type: 'GET',
                    dataType: 'json',
                    delay: 300,
                    data: function (params) {
                        return {
                            q: params.term,
                            section: '{{ $section }}'
                        };
                    },
                    processResults: function (data) {
                        if (data.error) {
                            return {
                                results: []
                            };
                        }
                        return {
                            results: data.results.map(function(item) {
                                return {
                                    id: item.id,
                                    text: item.text,
                                    ref_code: item.ref_code,
                                    name_en: item.name_en,
                                    name_ar: item.name_ar,
                                    image: item.image
                                };
                            })
                        };
                    },
                    cache: true
                },
                templateResult: formatProductOption,
                templateSelection: formatProductSelection,
                minimumInputLength: 2
            });

            // Handle product selection
            $('#product_search').on('select2:select', function (e) {
                var data = e.params.data;
                addProductToFeatured(data.id, '{{ $section }}');
            });
            @endif
        });

        function formatProductOption(product) {
            if (!product.id) {
                return product.text;
            }

            var $option = $(
                '<div class="product-option">' +
                    (product.image ? '<img src="' + product.image + '" alt="Product">' : '<div style="width:40px;height:40px;background:#f0f0f0;border-radius:4px;margin-left:10px;"></div>') +
                    '<div class="product-option-info">' +
                        '<div class="ref-code">' + product.ref_code + '</div>' +
                        '<div class="name">' + (product.name_en || product.text) + '</div>' +
                    '</div>' +
                '</div>'
            );
            return $option;
        }

        function formatProductSelection(product) {
            return product.text || product.ref_code;
        }

        function addProductToFeatured(productId, section) {
            Swal.fire({
                title: 'Adding...',
                text: 'Please wait...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: "{{ route('admin.featured-products.store') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: productId,
                    section: section
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Added!',
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
                },
                error: function(xhr) {
                    var errorMessage = 'Something went wrong.';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON.errors) {
                            var errors = [];
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                errors.push(value[0]);
                            });
                            errorMessage = errors.join('<br>');
                        }
                    }
                    Swal.fire({
                        title: 'Error!',
                        html: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-primary font-weight-bold'
                        },
                        buttonsStyling: false
                    });
                    $('#product_search').val(null).trigger('change');
                }
            });
        }

        // Delete featured product
        function deleteFeatured(featuredId) {
            Swal.fire({
                title: 'Remove Product?',
                text: 'Are you sure you want to remove this product from the featured section?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, remove',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-danger font-weight-bold',
                    cancelButton: 'btn btn-light font-weight-bold'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ route('admin.featured-products.destroy', ':id') }}".replace(':id', featuredId);
                    
                    var methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    form.appendChild(methodInput);
                    
                    var tokenInput = document.createElement('input');
                    tokenInput.type = 'hidden';
                    tokenInput.name = '_token';
                    tokenInput.value = '{{ csrf_token() }}';
                    form.appendChild(tokenInput);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
@endpush
