@extends('layouts.app')
@section('content')
<edit_user-component :user = "{{ $user }}"></edit_user-component>
@endsection

