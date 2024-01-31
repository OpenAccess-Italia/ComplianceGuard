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
        <div class="login-content flex-row-fluid d-flex flex-column p-10">
            <!--begin::Wrapper-->
            <div class="d-flex flex-row-fluid flex-center">
                <!--begin::Signin-->
                <div class="login-form">
                    <!--begin::Form-->
                    <form method="POST" action="{{ route('login') }}" class="form" id="kt_login_singin_form">
                        @csrf
                        <!--begin::Title-->
                        <div class="pb-5 pb-lg-15">
                            <h3 class="font-weight-bolder text-dark font-size-h2 font-size-h1-lg">Login</h3>
                        </div>
                        <!--begin::Title-->
                        <!--begin::Form group-->
                        <div class="form-group">
                            <label class="font-size-h6 font-weight-bolder text-dark">Email o Username</label>
                            <input id="email" name="email" type="text" class="form-control h-auto py-6 rounded-lg border-0 @error('email') is-invalid @enderror" value="{{ old('email') }}" required autocomplete="email" autofocus>
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
                            <input id="password" name="password" type="password" class="form-control h-auto py-6 rounded-lg border-0 @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--end::Form group-->
                        <!--begin::Action-->
                        <div class="pb-lg-0 pb-5">
                            <label class="checkbox checkbox-outline" for="remember">
                                <input class="checkbox" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <span></span>&nbsp;
                                {{ __('Ricordami') }}
                            </label>
                            <button onClick="formhash();" type="submit" id="kt_login_singin_form_submit_button" class="btn btn-dark font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-3">Login</button>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-dark font-size-h6 font-weight-bolder text-hover-primary pt-5">{{ __('Password dimenticata?') }}</a>
                            @endif
                        </div>
                        <!--end::Action-->
                    </form>
                    <!--end::Form-->
                </div>
                <!--end::Signin-->
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
        document.getElementById("kt_login_singin_form").submit();
    }
</script>
@endsection
