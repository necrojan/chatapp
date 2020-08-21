@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="search-text-container">
            <h4 class="mb-3">Archived Messages</h4>
            <div class="search-wrapper">
                <form action="/search-logs" method="post" role="search">
                    {{ csrf_field() }}
                    <div class="form-group mb-3 has-search">
                        <span class="fa fa-search form-control-feedback"></span>
                        <input type="text" class="form-control" placeholder="Search by Message">
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>
    <div class="row">
        <div class="col-md-12">
            <div class="search-user-results">
                @if ($users)

                    @forelse($users as $user)
                        @if (count($user->messages) > 0)

                            <div class="user-result-wrapper">
                                @if ($user->photo)
                                    <img src="{{ asset('storage/images/'.$user->photo) }}" class="align-self-center mr-3">
                                @else
                                    <img src="{{ asset('storage/images/no-image.png') }}" class="align-self-center mr-3">
                                @endif
                                <div class="user-details">
                                    <h4 class="mb-0">{{ $user->name }}</h4>
                                    <p class="mb-0 user-email">{{ $user->email }}</p>
                                    <div class="user-messages mt-2">
                                        @foreach($user->messages as $message)
                                            <div class="user-message mb-2">
                                                <p class="mb-0">{{ $message->created_at->format('m/d/Y') }}</p>
                                                <p class="mb-0">{{ $message->message }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div>No Users</div>
                    @endforelse
                @endif
            </div>
        </div>
    </div>
@endsection