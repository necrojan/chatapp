@extends('layouts.admin')
@section('content')
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="btn-toolbar justify-content-between">
                <div class="btn-group">
                    <a href="{{ route('responses.personal.create') }}" class="btn btn-primary">
                        <i class="icon-plus"></i> Add new personal response
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Personal Response List</div>
                <div class="card-body">
                    @if (session()->has('delete_personal_response'))
                        <div class="alert alert-success" role="alert">
                            {{ session()->get('delete_personal_response') }}
                        </div>
                    @endif
                    @if (session()->has('create_personal_response'))
                        <div class="alert alert-success">
                            {{ session()->get('create_personal_response') }}
                        </div>
                    @endif
                    <table class="table table-striped responses">
                        <thead>
                        <tr>
                            <th scope="col">Key</th>
                            <th scope="col" class="w-25 text-center">Message</th>
                            @if (auth()->user()->hasRole(['admin', 'agent']))
                                <th scope="col" class="text-center">Actions</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($responses as $response)
                            <tr>
                                <td>{{ $response->key }}</td>
                                <td>{{ $response->message }}</td>
                                @if (auth()->user()->hasRole(['admin', 'agent']))
                                    <td class="text-center">
                                        <a href="{{ route('responses.personal.edit', $response->id) }}" class="btn btn-primary"><i class="icon-pencil"></i></a>
                                        <form action="{{ route('responses.personal.destroy', $response->id) }}" method="POST">
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