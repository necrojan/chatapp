@extends('layouts.admin')

@section('content')
    <h1>Edit Profile</h1>
    <hr>
    <form action="{{ route('profile.update', ['user' => $user]) }}" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        {{ method_field('PUT') }}
        <div class="card">
            <div class="card-body">
                @if($errors->has('photo'))
                    @error('photo')
                        <span class="invalid-feedback d-block mb-3">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                @endif
                <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                    <label for="name">Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" value="{{ $user->name }}">
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                    <label for="email">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" id="email" value="{{ $user->email }}">
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                    <label for="password">Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="password"
                           value="{{ $user->password }}">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group">
                    @if ($user->photo)
                        <p>{{ $user->photo }} </p>
                    @endif
                    <input type="file" name="photo" class="form-control-file">
                </div>

            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        Update
                    </button>
                </div>
            </div>
        </div>
    </form>
    <join></join>
@endsection