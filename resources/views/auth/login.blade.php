@extends('layouts.admin-login')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="admin-login">
                <div class="admin-login__left">
                    <div class="logo-container">
                        <h1 class="text-white">Chat App</h1>
                    </div>
                </div>
                <div class="admin-login__right">
                    <h2>welcome</h2>
                    <p>please login to admin dashboard</p>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group">
                            <input id="username"
                                   type="text" class="form-control @error('username') is-invalid @enderror"
                                   name="username" value="{{ old('username') }}"
                                   required
                                   autocomplete="username"
                                   placeholder="Username"
                                   autofocus>

                            @error('username')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <input id="password"
                                   type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   name="password" required autocomplete="current-password"
                                   placeholder="Password"
                            >

                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Login') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
