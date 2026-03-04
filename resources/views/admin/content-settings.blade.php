@extends('admin.layout.app')
@section('content')
    <!--begin::Content-->
    <div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Subheader-->
        <div class="subheader py-2 py-lg-6  subheader-solid " id="kt_subheader">
            <div
                class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <!--begin::Info-->
                <div class="d-flex align-items-center flex-wrap mr-1">
                    <!--begin::Page Heading-->
                    <div class="d-flex align-items-baseline flex-wrap mr-5">
                        <!--begin::Page name-->
                        <h5 class="text-dark font-weight-bold my-1 mr-5">
                            Site Settings
                        </h5>
                        <!--end::Page name-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                            <li class="breadcrumb-item">
                                <a href="{{route('admin.home')}}" class="text-muted">
                                    Home
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{route('admin.content-settings.index')}}" class="text-muted">
                                    Site Settings
                                </a>
                            </li>
                        </ul>
                        <!--end::Breadcrumb-->
                    </div>
                    <!--end::Page Heading-->
                </div>
                <!--end::Info-->

                <!--begin::Toolbar-->
                <div class="d-flex align-items-center"></div>
                <!--end::Toolbar-->
            </div>
        </div>
        <!--end::Subheader-->
        <div class="d-flex flex-column-fluid">
            <!--begin::Container-->
            <div class=" container">
                @if(session()->has('success'))
                    <div class="alert alert-success col-12" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session()->has('error'))
                    <div class="alert alert-danger col-12" role="alert">
                        {{ session('error') }}
                    </div>
                @endif
                
                <!-- Site Settings Form -->
                <form method="post" action="{{route('admin.content-settings.update')}}" id="siteSettingsForm">
                    @csrf
                    @method('PUT')
                    
                    <!-- Shipping Information -->
                    <div class="card card-custom mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Shipping Information</h3>
                        </div>
                        <div class="card-body">
                            <!-- English -->
                            <div class="form-group">
                                <label class="font-weight-bold">Shipping Information (English)</label>
                                <div id="shipping_information_en_editor" style="height: 300px;">
                                    {!! old('shipping_information_en', optional($shipping_information_en)->value) !!}
                                </div>
                                <textarea name="shipping_information_en" id="shipping_information_en" style="display: none;" class="@error('shipping_information_en') is-invalid @enderror">{{ old('shipping_information_en', optional($shipping_information_en)->value) }}</textarea>
                                @error('shipping_information_en')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Arabic -->
                            <div class="form-group">
                                <label class="font-weight-bold">Shipping Information (العربية)</label>
                                <div id="shipping_information_ar_editor" style="height: 300px;">
                                    {!! old('shipping_information_ar', optional($shipping_information_ar)->value) !!}
                                </div>
                                <textarea name="shipping_information_ar" id="shipping_information_ar" style="display: none;" class="@error('shipping_information_ar') is-invalid @enderror">{{ old('shipping_information_ar', optional($shipping_information_ar)->value) }}</textarea>
                                @error('shipping_information_ar')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Returns & Exchanges -->
                    <div class="card card-custom mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Returns & Exchanges</h3>
                        </div>
                        <div class="card-body">
                            <!-- English -->
                            <div class="form-group">
                                <label class="font-weight-bold">Returns & Exchanges (English)</label>
                                <div id="returns_exchanges_en_editor" style="height: 300px;">
                                    {!! old('returns_exchanges_en', optional($returns_exchanges_en)->value) !!}
                                </div>
                                <textarea name="returns_exchanges_en" id="returns_exchanges_en" style="display: none;" class="@error('returns_exchanges_en') is-invalid @enderror">{{ old('returns_exchanges_en', optional($returns_exchanges_en)->value) }}</textarea>
                                @error('returns_exchanges_en')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Arabic -->
                            <div class="form-group">
                                <label class="font-weight-bold">Returns & Exchanges (العربية)</label>
                                <div id="returns_exchanges_ar_editor" style="height: 300px;">
                                    {!! old('returns_exchanges_ar', optional($returns_exchanges_ar)->value) !!}
                                </div>
                                <textarea name="returns_exchanges_ar" id="returns_exchanges_ar" style="display: none;" class="@error('returns_exchanges_ar') is-invalid @enderror">{{ old('returns_exchanges_ar', optional($returns_exchanges_ar)->value) }}</textarea>
                                @error('returns_exchanges_ar')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Payment Options & Security -->
                    <div class="card card-custom mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Payment Options & Security</h3>
                        </div>
                        <div class="card-body">
                            <!-- English -->
                            <div class="form-group">
                                <label class="font-weight-bold">Payment Options & Security (English)</label>
                                <div id="payment_options_security_en_editor" style="height: 300px;">
                                    {!! old('payment_options_security_en', optional($payment_options_security_en)->value) !!}
                                </div>
                                <textarea name="payment_options_security_en" id="payment_options_security_en" style="display: none;" class="@error('payment_options_security_en') is-invalid @enderror">{{ old('payment_options_security_en', optional($payment_options_security_en)->value) }}</textarea>
                                @error('payment_options_security_en')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Arabic -->
                            <div class="form-group">
                                <label class="font-weight-bold">Payment Options & Security (العربية)</label>
                                <div id="payment_options_security_ar_editor" style="height: 300px;">
                                    {!! old('payment_options_security_ar', optional($payment_options_security_ar)->value) !!}
                                </div>
                                <textarea name="payment_options_security_ar" id="payment_options_security_ar" style="display: none;" class="@error('payment_options_security_ar') is-invalid @enderror">{{ old('payment_options_security_ar', optional($payment_options_security_ar)->value) }}</textarea>
                                @error('payment_options_security_ar')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary mr-2">Update Site Settings</button>
                            <button type="reset" class="btn btn-secondary">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
            <!--end::Container-->
        </div>
        <!--end::Entry-->
    </div>
@endsection

@push('style')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        jQuery(document).ready(function() {
            // Initialize Quill editors for Shipping Information
            var shippingEnQuill = new Quill('#shipping_information_en_editor', {
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'align': [] }],
                        ['link', 'image'],
                        ['clean']
                    ]
                },
                theme: 'snow'
            });

            var shippingArQuill = new Quill('#shipping_information_ar_editor', {
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'align': [] }],
                        ['link', 'image'],
                        ['clean']
                    ]
                },
                theme: 'snow'
            });

            // Initialize Quill editors for Returns & Exchanges
            var returnsEnQuill = new Quill('#returns_exchanges_en_editor', {
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'align': [] }],
                        ['link', 'image'],
                        ['clean']
                    ]
                },
                theme: 'snow'
            });

            var returnsArQuill = new Quill('#returns_exchanges_ar_editor', {
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'align': [] }],
                        ['link', 'image'],
                        ['clean']
                    ]
                },
                theme: 'snow'
            });

            // Initialize Quill editors for Payment Options & Security
            var paymentEnQuill = new Quill('#payment_options_security_en_editor', {
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'align': [] }],
                        ['link', 'image'],
                        ['clean']
                    ]
                },
                theme: 'snow'
            });

            var paymentArQuill = new Quill('#payment_options_security_ar_editor', {
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'align': [] }],
                        ['link', 'image'],
                        ['clean']
                    ]
                },
                theme: 'snow'
            });

            // Update hidden textareas before form submission
            $('#siteSettingsForm').on('submit', function() {
                $('#shipping_information_en').val(shippingEnQuill.root.innerHTML);
                $('#shipping_information_ar').val(shippingArQuill.root.innerHTML);
                $('#returns_exchanges_en').val(returnsEnQuill.root.innerHTML);
                $('#returns_exchanges_ar').val(returnsArQuill.root.innerHTML);
                $('#payment_options_security_en').val(paymentEnQuill.root.innerHTML);
                $('#payment_options_security_ar').val(paymentArQuill.root.innerHTML);
            });
        });
    </script>
@endpush

