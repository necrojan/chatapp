@extends('layouts.admin')

@section('content')
    @php
        $response = $response ?? new \App\CannedResponse();
    @endphp
    @if (session()->has('update_response'))
        <div class="alert alert-success">
            {{ session()->get('update_response') }}
        </div>
    @endif
    <form action="{{ route('responses.update', $response->id) }}" method="post">
        @csrf
        {{ method_field('PUT') }}
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <i class="icon-plus"></i>Edit a Response
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="key">Key*</label>
                            <input
                                    type="text"
                                    class="form-control @error('key') is-invalid @enderror"
                                    id="key"
                                    name="key"
                                    value="{{ $response->key }}"
                                    required
                                    autofocus
                            >
                            @error('key')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="message">Message*</label>
                            <input
                                    type="text"
                                    class="form-control @error('message') is-invalid @enderror"
                                    id="message"
                                    name="message"
                                    value="{{ $response->message }}"
                                    required
                                    autofocus
                            >
                            @error('message')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <a href="{{ route('responses.index') }}" class="btn btn-primary">Back to Listing</a>
                        <button class="btn btn-primary">Update</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
