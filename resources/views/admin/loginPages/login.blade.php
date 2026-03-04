@extends("admin.loginPages.layout")
@section("content")
    <!--begin::Signin-->
    <div class="login-form">
        <!--begin::Form-->
        <form class="form" id="kt_login_singin_form" action="{{ route("admin.dologin") }}" method="post">
            @csrf
            <!--begin::Title-->
            <div class="pb-5 pb-lg-15">
                <h3 class="font-weight-bolder text-dark font-size-h2 font-size-h1-lg">Sign in to Your Dashboard</h3>
            </div>
            <!--begin::Title-->

            <!--begin::Form group-->
            <div class="form-group">
                <label class="font-size-h6 font-weight-bolder text-dark">Your Email</label>
                <input class="form-control h-auto py-7 px-6 rounded-lg border-0" type="email" name="email" placeholder="Email"/>
            </div>
            <!--end::Form group-->

            <!--begin::Form group-->
            <div class="form-group">
                <div class="d-flex justify-content-between mt-n5">
                    <label class="font-size-h6 font-weight-bolder text-dark pt-5">Your Password</label>

                    <a href="{{route("admin.forgotPassword")}}" class="text-dark font-size-h6 font-weight-bolder pt-5">
                        Forgot Password ?
                    </a>
                </div>
                <input class="form-control h-auto py-7 px-6 rounded-lg border-0" type="password" name="password" placeholder="Password"/>
            </div>
            <!--end::Form group-->
            @if (session()->has('error'))
                <div class="text-danger font-weight-bold font-size-h6 mb-5">
                    <div>{{session()->get('error')}}</div>
                </div>
            @endif
            <!--begin::Action-->
            <div class="pb-lg-0 pb-5">
                <button type="submit" id="kt_login_singin_form_submit_button" class="custom-button btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-3">Sign In</button>
            </div>
            <!--end::Action-->
        </form>
        <!--end::Form-->
    </div>
    <!--end::Signin-->
@endsection
