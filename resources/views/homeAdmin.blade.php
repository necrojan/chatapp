@extends('layouts.admin')

@section('content')
    <chat :user="{{ auth()->user()->load('client') }}" :role="{{ auth()->user()->roles }}"></chat>
    <audio id="notify_message">
        <source src="{{ asset('storage/audio/notify.mp3') }}" type="audio/mpeg">
        <source src="{{ asset('storage/audio/notify.ogg') }}" type="audio/ogg">
    </audio>

    <audio id="notify_pool">
        <source src="{{ asset('storage/audio/pool.mp3') }}" type="audio/mpeg">
        <source src="{{ asset('storage/audio/pool.ogg') }}" type="audio/ogg">
    </audio>
@endsection
