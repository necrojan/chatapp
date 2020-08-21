@extends('layouts.admin')

@section('content')
    <div class="profile">
        <h4 class="mb-3">Profile</h4>
        <hr>
        <div class="media">
            @if ($user->photo)
                <img src="{{ asset('storage/images/'.$user->photo) }}" class="align-self-center mr-3">
            @endif
            <div class="media-body">
                <h5 class="mt-0">{{ $user->name }}</h5>
                <p class="mb-2">{{ $user->username }}</p>
                <p class="mb-2">{{ $user->email }}</p>
                <a href="{{ route('profile.edit') }}" class="btn btn-primary">Edit</a>
            </div>
        </div>


        <join></join>
    </div>
@endsection