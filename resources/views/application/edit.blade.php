@extends('layouts.app')

@section('pagecss')
@endsection
@section('content')
<edit_application-component :schedule = "{{ $schedule }}"></edit_application-component>

@endsection
