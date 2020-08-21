@extends('layouts.app')

@section('content')
    <chat :user="{{ auth()->user()->load('client') }}" :role="{{ auth()->user()->roles }}"></chat>
@endsection
