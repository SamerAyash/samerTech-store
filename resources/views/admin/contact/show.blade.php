@extends('admin.layout.app')

@push('style')
    <style>
        /* Responsive styles for contact show page */
        @media (max-width: 767.98px) {
            .contact-show-subheader-title {
                font-size: 1rem;
                margin-right: 0.5rem !important;
            }
            .contact-show-breadcrumb {
                font-size: 0.75rem;
            }
        }
        .contact-message-box {
            background-color: #f3f6f9;
            border-left: 4px solid #3699FF;
            padding: 1.5rem;
            border-radius: 0.5rem;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
    </style>
@endpush

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-2">
                <h5 class="text-dark font-weight-bold my-1 mr-5 contact-show-subheader-title">Contact Message</h5>
                <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm contact-show-breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.home') }}" class="text-muted">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.contact.index') }}" class="text-muted">Contact Messages</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="text-muted">{{ \Str::limit($contact->subject, 30) }}</span>
                    </li>
                </ul>
            </div>
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.contact.index') }}" class="btn btn-light font-weight-bold">
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
                <div class="col-xl-8">
                    <!--begin::Contact Details Card-->
                    <div class="card card-custom">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="card-label">Message Details</h3>
                            </div>
                            <div class="card-toolbar">
                                @if($contact->readed)
                                    <span class="badge badge-success badge-lg">
                                        <i class="flaticon2-check-mark"></i> Read
                                    </span>
                                @else
                                    <span class="badge badge-warning badge-lg">
                                        <i class="flaticon2-mail"></i> Unread
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-10">
                                <label class="font-weight-bold text-dark mb-3">Subject:</label>
                                <div class="text-dark font-size-h5">{{ $contact->subject }}</div>
                            </div>

                            <div class="mb-10">
                                <label class="font-weight-bold text-dark mb-3">Message:</label>
                                <div class="contact-message-box text-dark">
                                    {{ $contact->message }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Contact Details Card-->
                </div>

                <div class="col-xl-4">
                    <!--begin::Contact Info Card-->
                    <div class="card card-custom card-stretch">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="card-label">Contact Information</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-7">
                                <label class="font-weight-bold text-dark mb-2">Name:</label>
                                <p class="text-muted font-size-lg">{{ $contact->name }}</p>
                            </div>

                            <div class="mb-7">
                                <label class="font-weight-bold text-dark mb-2">Email:</label>
                                <p class="text-muted font-size-lg">
                                    <a href="mailto:{{ $contact->email }}" class="text-primary">
                                        {{ $contact->email }}
                                    </a>
                                </p>
                            </div>

                            <div class="mb-7">
                                <label class="font-weight-bold text-dark mb-2">Phone:</label>
                                <p class="text-muted font-size-lg">
                                    <a href="tel:{{ $contact->phone }}" class="text-primary">
                                        {{ $contact->phone }}
                                    </a>
                                </p>
                            </div>

                            <div class="mb-7">
                                <label class="font-weight-bold text-dark mb-2">Status:</label>
                                <div class="mt-2">
                                    @if($contact->readed)
                                        <span class="badge badge-success badge-lg">
                                            <i class="flaticon2-check-mark"></i> Read
                                        </span>
                                    @else
                                        <span class="badge badge-warning badge-lg">
                                            <i class="flaticon2-mail"></i> Unread
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-7">
                                <label class="font-weight-bold text-dark mb-2">Received At:</label>
                                <p class="text-muted font-size-sm">
                                    {{ $contact->created_at->format('M d, Y') }}<br>
                                    <span class="text-muted">{{ $contact->created_at->format('h:i A') }}</span>
                                </p>
                            </div>

                            <!--begin::Actions-->
                            <div class="d-flex flex-column mt-5">
                                <button type="button" 
                                        class="btn btn-{{ $contact->readed ? 'warning' : 'success' }} mb-2" 
                                        onclick="toggleReadStatus({{ $contact->id }}, {{ $contact->readed ? 'true' : 'false' }})">
                                    <i class="flaticon2-{{ $contact->readed ? 'mail' : 'check-mark' }}"></i> 
                                    {{ $contact->readed ? 'Mark as Unread' : 'Mark as Read' }}
                                </button>
                                <button type="button" 
                                        class="btn btn-danger" 
                                        onclick="deleteContact({{ $contact->id }}, '{{ addslashes($contact->name) }}')">
                                    <i class="flaticon2-trash"></i> Delete Message
                                </button>
                            </div>
                            <!--end::Actions-->
                        </div>
                    </div>
                    <!--end::Contact Info Card-->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset("cp_assets/js/sweetalert2@11.js") }}"></script>
    <script>
        // Toggle read status
        function toggleReadStatus(id, currentStatus) {
            const newStatus = !currentStatus;
            const statusText = newStatus ? 'Read' : 'Unread';

            Swal.fire({
                title: 'Update Status?',
                html: `<p>Mark this message as <strong>${statusText}</strong>?</p>`,
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
                        url: "{{ route('admin.contact.updateStatus', ':id') }}"
                            .replace(':id', id),
                        type: 'PUT',
                        data: {
                            _token: '{{ csrf_token() }}'
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

        // Delete confirmation with SweetAlert2
        function deleteContact(id, name) {
            Swal.fire({
                title: 'Delete Contact?',
                html: `<p class="mb-2">Are you sure you want to delete contact from <strong>${name}</strong>?</p>
                       <p class="text-muted font-size-sm mt-2">This action cannot be undone.</p>`,
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
                        url: "{{ route('admin.contact.destroy', ':id') }}"
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
                                    window.location.href = "{{ route('admin.contact.index') }}";
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
