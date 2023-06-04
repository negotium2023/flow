@extends('layouts.app')

@section('title') Fees generated @endsection

@section('header')
    <h1><i class="fa fa-line-chart"></i> @yield('title')</h1>
    <a href="" class="btn btn-outline-light float-right"><i class="fa fa-download"></i> PDF</a>
@endsection

@section('content')
    <form class="form-inline mt-3">
        End date &nbsp;
        {{Form::date('t',old('t'),['class'=>'form-control form-control-sm','id'=>'converted_end_date'])}}
        &nbsp; for &nbsp;
        {{Form::select('p',$processes ,old('p'),['class'=>'form-control form-control-sm'])}}
    </form>

    <hr>

    <p>To be confirmed with client.</p>
@endsection