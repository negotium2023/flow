@extends('adminlte.default')
@section('title') Personal Access Tokens @endsection
@section('header')
    {{--<div class="container-fluid container-title">
        <h3>@yield('title')</h3>
    </div>--}}
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row mt-3">
            <div class="col-lg-12">
                <passport-personal-access-tokens></passport-personal-access-tokens>
            </div>
        </div>
    </div>
@endsection
{{--
<passport-authorized-clients></passport-authorized-clients>
<passport-personal-access-tokens></passport-personal-access-tokens>--}}
