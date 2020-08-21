@extends('layouts.app')

@section('content')
    <chat :user="{{ $user }}" :role="{{ auth()->user()->roles }}"></chat>
@endsection