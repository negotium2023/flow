@extends('adminlte.default')
@section('title') Create Report @endsection
@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <a href="{{route('reports.index')}}" class="btn btn-dark btn-sm float-right"><i class="fa fa-caret-left"></i> Back</a>
    </div>
@endsection
@section('content')
    <div class="container-fluid">
    <hr/>
    {{Form::open(['url' => route('reports.store'), 'method' => 'post','files'=>true])}}
    <div class="form-group mt-3">
        {{Form::label('name', 'Report Name:')}}
        {{Form::text('name',old('name'),['class'=>'form-control'. ($errors->has('name') ? ' is-invalid' : ''),'placeholder'=>'Name'])}}
        @foreach($errors->get('name') as $error)
            <div class="invalid-feedback">
                {{ $error }}
            </div>
        @endforeach
    </div>
    <div class="form-group mt-3">
        {{Form::label('activity', 'Activity to be reported on:')}}
        {{Form::select('activity',$activities ,old('activity'),['class'=>'form-control form-control-sm'])}}
        @foreach($errors->get('activity') as $error)
            <div class="invalid-feedback">
                {{ $error }}
            </div>
        @endforeach
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-sm">Save</button>
    </div>
    {{Form::close()}}
    </div>
@endsection