@extends('adminlte.default')

@section('title') Insight @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
    <blackboard-insight add-user-link="{{route('clients.create')}}"></blackboard-insight>
    </div>
@endsection

