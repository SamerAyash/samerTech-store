@extends('admin.layout.app')

@push('style')
    <link href="{{ asset('cp_assets/plugins/custom/datatables/datatables.bundle.css') }}"
        rel="stylesheet" type="text/css" />
    <style>
        @media (max-width: 991.98px) {
            .post-search-input {
                width: 100% !important;
                margin-bottom: 1rem;
            }
            .post-card-header {
                flex-direction: column;
                align-items: stretch !important;
            }
            .post-card-toolbar {
                margin-top: 1rem;
            }
        }
        @media (max-width: 767.98px) {
            .post-subheader-title {
                font-size: 1rem;
                margin-right: 0.5rem !important;
            }
            .post-breadcrumb {
                font-size: 0.75rem;
            }
            .post-table-wrapper {
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
                <h5 class="text-dark font-weight-bold my-1 mr-5 post-subheader-title">Posts</h5>
                <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm post-breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.home') }}" class="text-muted">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="text-muted">Posts</span>
                    </li>
                </ul>
            </div>
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.posts.create') }}"
                    class="btn btn-primary font-weight-bold">
                    <i class="flaticon2-plus"></i>
                    <span class="d-none d-md-inline">Add Post</span>
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
                <div class="card-header border-0 pt-6 post-card-header">
                    <div class="card-title">
                        <h3 class="card-label">Posts</h3>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="post-table-wrapper">
                        <table id="kt_datatable" class="table table-striped table-row-bordered gy-5">
                            <thead>
                                <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="min-w-150px">Title</th>
                                    <th class="min-w-100px">Slug</th>
                                    <th class="min-w-150px">Title (AR)</th>
                                    <th class="min-w-100px">Slug (AR)</th>
                                    <th class="min-w-100px">Products</th>
                                    <th class="min-w-100px">Status</th>
                                    <th class="min-w-120px">Published At</th>
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
        jQuery(document).ready(function () {
            var table = $('#kt_datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.posts.data') }}",
                    type: 'GET'
                },
                columns: [
                    { data: 'title', name: 'title' },
                    { data: 'slug', name: 'slug', orderable: false, searchable: false },
                    { data: 'title_ar', name: 'title_ar', orderable: false, searchable: false, className: 'text-right' },
                    { data: 'slug_ar', name: 'slug_ar', orderable: false, searchable: false, className: 'text-right' },
                    { data: 'products_count', name: 'products_count', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'status', name: 'status', className: 'text-center' },
                    { data: 'published_at', name: 'published_at' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' }
                ],
                responsive: true,
                order: [[7, 'desc']],
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
        });

        // Delete confirmation
        function deletePost(id, title, productsCount) {
            var warningMsg = 'Are you sure you want to delete "' + title + '"?';
            if (productsCount > 0) {
                warningMsg += '\n\nThis post is linked to ' + productsCount + ' product(s). The links will be removed.';
            }
            warningMsg += '\n\nThis action cannot be undone.';

            Swal.fire({
                title: 'Delete Post?',
                text: warningMsg,
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
                        url: "{{ route('admin.posts.destroy', ':id') }}".replace(':id', id),
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
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
                            var errorMessage = xhr.responseJSON?.message || 'Something went wrong.';
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
