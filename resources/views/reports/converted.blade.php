@extends('layouts.app')

@section('title') Number of converted clients @endsection

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

    <div class="table-responsive">
        <table class="table table-bordered table-sm table-hover">
            <thead class="thead-light">
            <tr>
                <th>Period</th>
                <th>Actual</th>
                <th>Target</th>
                <th>Difference</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Year to date</td>
                <td>{{$clients['last_year']['actual']}}</td>
                <td>{{$clients['last_year']['target']}}</td>
                <td class="{{($clients['last_year']['difference']>=0) ? 'text-success' : 'text-danger'}}">{{$clients['last_year']['difference']}}</td>
            </tr>
            <tr>
                <td>Quarter</td>
                <td>{{$clients['last_quarter']['actual']}}</td>
                <td>{{$clients['last_quarter']['target']}}</td>
                <td class="{{($clients['last_quarter']['difference']>=0) ? 'text-success' : 'text-danger'}}">{{$clients['last_quarter']['difference']}}</td>
            </tr>
            <tr>
                <td>Month to date</td>
                <td>{{$clients['last_month']['actual']}}</td>
                <td>{{$clients['last_month']['target']}}</td>
                <td class="{{($clients['last_month']['difference']>=0) ? 'text-success' : 'text-danger'}}">{{$clients['last_month']['difference']}}</td>
            </tr>
            </tbody>
        </table>
    </div>

    <small class="text-muted">Showing data until: <b>{{$end_date}}</b></small>
@endsection

@section('extra-js')
    <script>
        $("#converted_end_date").change(function () {
            $(this).closest('form').submit();
        });
    </script>
@endsection