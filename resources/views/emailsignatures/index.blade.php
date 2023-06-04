@extends('layouts.app')

@section('title') Email Signatures @endsection

@section('header')
    <h1><i class="fa fa-pencil-square-o"></i> @yield('title')</h1>
    <a href="{{route('emailsignatures.create')}}" class="btn btn-outline-light float-right"><i class="fa fa-plus"></i> Email Signature</a>
@endsection

@section('content')
    <div class="content-container page-content">
        <div class="row col-md-12 h-100 pr-0">
            @yield('header')
            <div class="container-fluid index-container-content">
                <div class="table-responsive h-100">
                     <table class="table table-bordered table-sm table-hover"
                </div>
            </div>
        </div>
    </div>
@endsection
