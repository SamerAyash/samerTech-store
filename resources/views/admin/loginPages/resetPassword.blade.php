@extends('admin.loginPages.layout')
@section('content')
<div class="login-form">
    <form class="form fv-plugins-bootstrap fv-plugins-framework" id="kt_login_forgot_form" action="{{route('admin.updatePassword',['token'=>$data->token])}}" method="post" novalidate="novalidate">
        @csrf
        <!--begin::Title-->
        <div class="pb-5 pb-lg-15">
            <h3 class="font-weight-bolder text-dark font-size-h2 font-size-h1-lg">Enter your new password</h3>
            <p class="text-muted font-weight-bold font-size-h4">Enter your new password</p>
        </div>
        <!--end::Title-->

        <!--begin::Form group-->
        <div class="form-group fv-plugins-icon-container">
            <input class="form-control h-auto py-7 px-6 border-0 rounded-lg font-size-h6" type="password" placeholder="Password" name="password" autocomplete="off">
        <div class="fv-plugins-message-container"></div></div>
        <div class="form-group fv-plugins-icon-container">
            <input class="form-control h-auto py-7 px-6 border-0 rounded-lg font-size-h6" type="password" placeholder="Password Confirmation" name="password_confirmation" autocomplete="off">
        <div class="fv-plugins-message-container"></div></div>
        <!--end::Form group-->
        @if (session()->has('success'))
            <div class="text-success font-weight-bold font-size-h6 mb-5">
                <div>{{session()->get('success')}}</div>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="text-danger font-weight-bold font-size-h6 mb-5">
                <div>{{session()->get('error')}}</div>
            </div>
        @endif
        <!--begin::Form group-->
        <div class="form-group d-flex flex-wrap">
            <button type="submit" id="kt_login_forgot_form_submit_button" class="custom-button btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-4">Submit</button>
            <a href="{{route('admin.login')}}" id="kt_login_forgot_cancel" class="btn btn-dark font-weight-bolder font-size-h6 px-8 py-4 my-3">Go to Sign in</a>
        </div>
        <!--end::Form group-->
    <input type="hidden"><div></div></form>
</div>
@endsection