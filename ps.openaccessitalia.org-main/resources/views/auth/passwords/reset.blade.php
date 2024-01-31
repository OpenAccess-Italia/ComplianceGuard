@extends('layouts.app')

@section('content')
<!--begin::Main-->
<div class="d-flex flex-column flex-root">
    <!--begin::Login-->
    <div class="login login-3 wizard d-flex flex-column flex-lg-row flex-column-fluid">
        <!--begin::Aside-->
        <div class="login-aside d-flex flex-column flex-row-auto">
            <!--begin::Aside Top-->
            <div class="d-flex h-100 flex-column py-10" style="background-image: url(/img/city.png)">
                <!--begin::Aside header-->
                <a href="#" class="login-logo text-center pt-lg-10 pb-10">
                    <img src="/img/brand_w.png" alt="logo" style="max-height:60px">
                </a>
                <!--end::Aside header-->
                <!--begin::Aside Title-->
                <p class="font-size-h1 text-center text-white">{{env("APP_NAME")}}</p>
                <!--end::Aside Title-->
            </div>
            <!--end::Aside Top-->
        </div>
        <!--begin::Aside-->
        <!--begin::Content-->
        <div class="login-content flex-column-fluid d-flex flex-column p-10">
            <!--begin::Wrapper-->
            <div class="d-flex flex-row-fluid flex-center">
                <!--begin::Forgot-->
                <div class="login-form">
                    <!--begin::Form-->
                    <form method="POST" action="{{ route('password.update') }}" class="form" id="reset-form">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <!--begin::Title-->
                        <div class="pb-5 pb-lg-15">
                            <h3 class="font-weight-bolder text-dark font-size-h2 font-size-h1-lg">Reset Password</h3>
                        </div>
                        <!--end::Title-->
                        <!--begin::Form group-->
                        <div class="form-group">
                            <label class="font-size-h6 font-weight-bolder text-dark">Indirizzo email</label>
                            <input id="email" type="email" class="form-control h-auto py-6 border-0 rounded-lg font-size-h6 @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required placeholder="Email" autocomplete="email" autofocus>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--end::Form group-->
                        <!--begin::Form group-->
                        <div class="form-group">
                            <div class="d-flex justify-content-between mt-n5">
                                <label class="font-size-h6 font-weight-bolder text-dark pt-5">Password</label>
                            </div>
                            <input id="password" name="password" type="password" class="form-control h-auto py-6 rounded-lg border-0 @error('password') is-invalid @enderror" required autocomplete="new-password">

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--end::Form group-->
                        <!--begin::Form group-->
                        <div class="form-group">
                            <div class="d-flex justify-content-between mt-n5">
                                <label class="font-size-h6 font-weight-bolder text-dark pt-5">Ripeti Password</label>
                            </div>
                            <input id="password-confirm" type="password" class="form-control h-auto py-6 rounded-lg border-0" name="password_confirmation" required autocomplete="new-password">
                        </div>
                        <!--end::Form group-->

                        <!--begin::Form group-->
                        <div class="form-group d-flex flex-wrap">
                            <button onClick="formhash();" type="submit" id="kt_login_forgot_form_submit_button" class="btn btn-dark font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-4">Reset password</button>
                        </div>
                        <!--end::Form group-->
                    </form>
                    <!--end::Form-->
                </div>
                <!--end::Forgot-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Login-->
</div>
<!--end::Main-->

<script>
    function formhash(){
        var psw = document.getElementById("password").value;
        var psw512 = hex_sha512(psw);
        document.getElementById("password").value = psw512;
        document.getElementById("password-confirm").value = psw512;
        document.getElementById("reset-form").submit();
    }
</script>
@endsection
