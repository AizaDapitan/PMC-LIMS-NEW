@extends('layouts.app')

@section('content')
<edit_assistant-component :assistant = "{{ $assistant }}"></edit_assistant-component>
@endsection