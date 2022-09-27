@extends('layouts.app')
@section('content')
<edit_supervisor-component :supervisor = "{{ $supervisor }}"></edit_supervisor-component>
@endsection

