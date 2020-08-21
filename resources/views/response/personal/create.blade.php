@extends('layouts.admin')

@section('content')
    <form action="{{ route('responses.personal.store') }}" method="post">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <i class="icon-plus"></i> Add a Personal Response
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="key">Key*</label>
                            <input
                                    type="text"
                                    class="form-control @error('key') is-invalid @enderror"
                                    id="key"
                                    name="key"
                                    value="{{ old('key') }}"
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
                            <textarea
                                    class="form-control @error('message') is-invalid @enderror"
                                    id="message"
                                    name="message"
                                    value="{{ old('message') }}"
                                    required
                            ></textarea>
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
                        <button class="btn btn-primary">Create</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection