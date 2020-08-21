@extends('layouts.client')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="unauthorized-error">
                    <div class="error-email">
                        <p><i class="icon-exclamation"></i></p>
                        <h2>Oops!</h2>
                        <p>Chatapp employees please click the button</p>
                        <a href="/login" class="btn-primary btn">Login</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection