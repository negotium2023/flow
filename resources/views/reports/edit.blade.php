@extends('adminlte.default')
@section('title') Edit Report @endsection
@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
    </div>
@endsection
@section('content')
    <div class="container-fluid">
    <hr/>
    @foreach($reports as $result)
    {{Form::open(['url' => route('reports.update',['reportid' => $result->id]), 'method' => 'post','files'=>true])}}
    <div class="form-group mt-3">
        {{Form::label('name', 'Report Name:')}}
        {{Form::text('name',$result->name,['class'=>'form-control'. ($errors->has('name') ? ' is-invalid' : ''),'placeholder'=>'Name'])}}
        @foreach($errors->get('name') as $error)
            <div class="invalid-feedback">
                {{ $error }}
            </div>
        @endforeach
    </div>
    <div class="form-group mt-3">
        {{Form::label('activity', 'Activity to be reported on:')}}
        {{Form::select('activity',$activities ,$result->activity_id,['class'=>'form-control form-control-sm'])}}
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

    @endforeach
    </div>
@endsection