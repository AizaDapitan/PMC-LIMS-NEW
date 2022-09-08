@extends('layouts.app')
@section('content')
<view_digester-component :forapproval = "'{{ $forapproval }}'" :worksheet = "{{ $worksheet }}"></view_digester-component>
@endsection