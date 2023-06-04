@extends('adminlte.default')

@section('title') Submit help request @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <hr />

    {{Form::open(['url' => route('help.store'), 'method' => 'post'])}}

    <div class="form-group mt-3">
        {{Form::label('name', 'Name')}}
        {{Form::text('name', auth()->user()->name(), ['class'=>'form-control','disabled'])}}
    </div>

    <div class="form-group">
        {{Form::label('email', 'Email')}}
        {{Form::text('email', auth()->user()->email, ['class'=>'form-control','disabled'])}}
    </div>

    <div class="form-group">
        {{Form::label('comment', 'Comment')}}
        <textarea name="comment" class="form-control" autofocus placeholder="Add comments here...">
            {{old('Comment')}}
        </textarea>
    </div>

    <div class="form-group">
        {{Form::label('page', 'Page where relevant to the issue')}}
        {{Form::text('page', old('page'), ['class'=>'form-control'])}}
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-sm">Send</button>
    </div>

    {{Form::close()}}
    </div>
@endsection