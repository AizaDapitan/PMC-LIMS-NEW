@extends('layouts.app')

@section('content')
<edit_chemist-component :chemist = "{{ $chemist }}"></edit_chemist-component>
@endsection