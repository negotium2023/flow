@extends('layouts.app')

@section('title') Client Target Data @endsection

@section('header')
    <h1><i class="fa fa-line-chart"></i> @yield('title')</h1>
    <a href="" class="btn btn-outline-light float-right"><i class="fa fa-download"></i> PDF</a>
@endsection

@section('content')
    <form class="form-inline mt-3">
        From &nbsp;
        {{Form::date('f',old('f'),['class'=>'form-control form-control-sm','id'=>'from_date'])}}
        &nbsp; to &nbsp;
        {{Form::date('t',old('t'),['class'=>'form-control form-control-sm','id'=>'end_date'])}}
    </form>

    <hr>

    <p>
        Coming soon
    </p>

@endsection

@section('extra-js')
    <script>
        $("#from_date, #end_date").change(function () {
            $(this).closest('form').submit();
        });
    </script>
@endsection