@extends('layouts.client')

@section('content')
    <div class="container client-login mt-5">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="login-wrapper">
                    <div class="left">
                        <div class="logo">
                            <h1 class="text-white">Chat App</h1>
                            <h4>Welcome to Support Chat</h4>
                        </div>
                    </div>
                    <div class="right">
                        <h3>Login</h3>
                        <form method="POST" action="{{ route('welcomeLogin') }}">
                            @csrf

                            <div class="form-group">
                                @if (session()->has('email_not_found'))
                                    <span class="invalid-feedback d-block">
                                        <strong>{{ session()->get('email_not_found') }}</strong>
                                    </span>
                                @endif
                                <input placeholder="Enter your Email" id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <input placeholder="Enter your Full Name" id="full_name" type="text" class="form-control @error('full_name') is-invalid @enderror" name="full_name" value="{{ old('full_name') }}" required autocomplete="full_name">
                                @error('full_name')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <input placeholder="Enter your Company" id="company" type="text" class="form-control @error('company') is-invalid @enderror" name="company" value="{{ old('company') }}" required autocomplete="company">
                                @error('company')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group mb-2">
                                <input placeholder="Enter your Phone number" id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" autocomplete="phone">
                                <small class="text-muted"><i>Optional</i></small>
                                @error('phone')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>


                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>
                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>

                            <input type="hidden" name="recaptcha" id="recaptcha">
                        </form>
                        <div class="login-icons pt-3">
                            <p class="d-inline">Follow Us: </p>
                            <ul class="pl-0 d-inline">
                                <li class="d-inline pl-0 pr-2"><a href="#" target="_blank" class="text-decoration-none"><i class="icon-social-facebook"></i></a></li>
                                <li class="d-inline pl-0 pr-2"><a href="#" target="_blank" class="text-decoration-none"><i class="icon-social-twitter"></i></a></li>
                                <li class="d-inline pl-0 pr-2"><a href="#" class="text-decoration-none" target="_blank"><i class="icon-social-linkedin"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('recaptcha.site_key') }}"></script>
    <script type="text/javascript">
        grecaptcha.ready(function() {
            grecaptcha.execute('{{ config('recaptcha.site_key') }}', {action: 'chat'}).then(function(token) {
                if (token) {
                    document.getElementById('recaptcha').value = token;
                }
            });
        });
    </script>
@endsection