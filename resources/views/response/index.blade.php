@extends('layouts.admin')
@section('content')
    @if (auth()->user()->hasRole('admin'))
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="btn-toolbar justify-content-between">
                    <div class="btn-group">
                        <a href="{{ route('responses.create') }}" class="btn btn-primary">
                            <i class="icon-plus"></i> Add new
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Response List</div>
                <div class="card-body">
                    @if (session()->has('delete_response'))
                        <div class="alert alert-success" role="alert">
                            {{ session()->get('delete_response') }}
                        </div>
                    @endif
                    @if (session()->has('create_response'))
                        <div class="alert alert-success">
                            {{ session()->get('create_response') }}
                        </div>
                    @endif
                    <table class="table table-striped responses">
                        <thead>
                        <tr>
                            <th scope="col">Key</th>
                            <th scope="col" class="w-25 text-center">Message</th>
                            @if (auth()->user()->hasRole('admin'))
                                <th scope="col" class="text-center">Actions</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($responses as $response)
                            <tr>
                                <td>{{ $response->key }}</td>
                                <td>{{ $response->message }}</td>
                                @if (auth()->user()->hasRole('admin'))
                                    <td class="text-center">
                                        <a href="{{ route('responses.edit', $response->id) }}" class="btn btn-primary"><i class="icon-pencil"></i></a>
                                        <form action="{{ route('responses.destroy', $response->id) }}" method="POST">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}
                                            <button class="btn btn-danger"><i class="icon-trash"></i></button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td>No responses found</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                    {{ $responses->links() }}
                </div>
            </div>
        </div>
    </div>
    <join></join>
@endsection