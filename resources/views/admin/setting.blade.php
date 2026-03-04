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
                            Settings
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
                                <a href="{{route('admin.setting')}}" class="text-muted">
                                    Settings
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
            <div class=" container row">
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
                <form method="post" class="col-lg-6" action="{{route('admin.setting_password')}}">
                    @csrf
                    <div class="card card-custom mb-3">
                        <div class="card-header">
                            <h3 class="card-title">
                                Change Password
                            </h3>
                        </div>
                        <!--begin::Section 1 Form-->
                        <div class="form">
                            <div class="card-body">
                                <form class="form" id="kt_form">
                                    <div class="tab-content">
                                        <!--begin::Tab-->
                                        <div class="tab-pane show px-7 active" id="kt_user_edit_tab_1" role="tabpanel">
                                            <!--begin::Row-->
                                            <div class="row">
                                                <div class="col-xl-12 my-2">
                                                    <!--begin::Group-->
                                                    <div class="form-group row">
                                                        <label class="col-form-label col-3 text-lg-right text-left">Current Password</label>
                                                        <div class="col-9">
                                                            <input class="form-control form-control-lg form-control-solid" type="password" name="password">
                                                            @error('password')
                                                            <p class="text-danger">{{$message}}</p>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-form-label col-3 text-lg-right text-left">New Password</label>
                                                        <div class="col-9">
                                                            <input class="form-control form-control-lg form-control-solid" type="password" name="new_password">
                                                            @error('new_password')
                                                            <p class="text-danger">{{$message}}</p>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-form-label col-3 text-lg-right text-left">Confirm New Password</label>
                                                        <div class="col-9">
                                                            <input class="form-control form-control-lg form-control-solid" type="password" name="password_confirmation">
                                                            @error('password_confirmation')
                                                            <p class="text-danger">{{$message}}</p>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            <!--end::Row-->
                                        </div>
                                        <!--end::Tab-->
                                    </div>
                                </form>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary mr-2">Update</button>
                                <button type="reset" class="btn btn-secondary">Cancel</button>

                            </div>
                        </div>
                        <!--end::Section 1 Form-->
                    </div>
                </form>
                <form method="post" class="col-lg-6" action="{{route('admin.setting_email')}}">
                    @csrf
                    <div class="card card-custom mb-3">
                        <div class="card-header">
                            <h3 class="card-title">
                                تغيير البريد الالكتروني
                            </h3>
                        </div>
                        <!--begin::Section 1 Form-->
                        <div class="form">
                            <div class="card-body">
                                <form class="form" id="kt_form">
                                    <div class="tab-content">
                                        <!--begin::Tab-->
                                        <div class="tab-pane show px-7 active" id="kt_user_edit_tab_1" role="tabpanel">
                                            <!--begin::Row-->
                                            <div class="row">
                                                <div class="col-xl-12 my-2">
                                                    <!--begin::Group-->
                                                    <div class="form-group row">
                                                        <label class="col-form-label col-3 text-lg-right text-left">البريد الالكتروني القديم</label>
                                                        <div class="col-9">
                                                            <input class="form-control form-control-lg form-control-solid" type="email" name="email">
                                                            @error('email')
                                                            <p class="text-danger">{{$message}}</p>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-form-label col-3 text-lg-right text-left">البريد الالكتروني الجديد</label>
                                                        <div class="col-9">
                                                            <input class="form-control form-control-lg form-control-solid" type="email" name="new_email">
                                                            @error('new_email')
                                                            <p class="text-danger">{{$message}}</p>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-form-label col-3 text-lg-right text-left">تأكيد البريد الالكتروني الجديد</label>
                                                        <div class="col-9">
                                                            <input class="form-control form-control-lg form-control-solid" type="email" name="email_confirmation">
                                                            @error('email_confirmation')
                                                            <p class="text-danger">{{$message}}</p>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            <!--end::Row-->
                                        </div>
                                        <!--end::Tab-->
                                    </div>
                                </form>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary mr-2">تحديث</button>
                                <button type="reset" class="btn btn-secondary">إلغاء</button>

                            </div>
                        </div>
                        <!--end::Section 1 Form-->
                    </div>
                </form>
            </div>
            <!--end::Container-->
        </div>
        <!--end::Entry-->
    </div>
@endsection
