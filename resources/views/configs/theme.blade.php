@extends('adminlte.default')

@section('title') Custom Stylesheet @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <hr />
        <div class="col-sm-12">
            {{Form::open(['url' => (isset($theme)?route('theme.update', $theme) : route('theme.store')), 'method' => (isset($theme)?'PATCH':'POST')])}}
                <div class="row">
                    <div class="form-group col-md-4">
                        {!! Form::label('primary', 'Primary Color') !!}
                        {!! Form::text('primary',(isset($theme->primary)?$theme->primary:'ffffff') , ['class' => 'form-control form-control-sm pick-a-color']) !!}
                    </div>
                    <div class="form-group col-md-4">
                        {!! Form::label('secondary', 'Secondary Color') !!}
                        {!! Form::text('secondary',(isset($theme->secondary)?$theme->secondary:'777777') , ['class' => 'form-control form-control-sm pick-a-color']) !!}
                    </div>
                    <div class="form-group col-md-4">
                        {!! Form::label('active', 'Active Links Color') !!}
                        {!! Form::text('active', (isset($theme->active)?$theme->active:'a1a1a1') , ['class' => 'form-control form-control-sm pick-a-color']) !!}
                    </div>
                    <div class="form-group col-md-6">
                        {!! Form::label('sidebar_background', 'Sidebar Background Color') !!}
                        {!! Form::text('sidebar_background', (isset($theme->sidebar_background)?$theme->sidebar_background:'a1a1a1') , ['class' => 'form-control form-control-sm pick-a-color']) !!}
                    </div>
                    <div class="form-group col-md-6">
                        {!! Form::label('sidebar_text', 'Sidebar Text Color') !!}
                        {!! Form::text('sidebar_text', (isset($theme->sidebar_text)?$theme->sidebar_text:'a1a1a1') , ['class' => 'form-control form-control-sm pick-a-color']) !!}
                    </div>
                </div>
            <hr>
            <div class="text-center">
                {!! Form::submit('Save', ['class' => 'btn btn-dark']) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>

@endsection
@section('extra-css')
    <link rel="stylesheet" href="{{asset('css/pick-a-color-1.2.3.min.css')}}">
@endsection
@section('extra-js')
    <script src="{{asset('js/tinycolor-0.9.15.min.js')}}"></script>
    <script src="{{ asset('js/pick-a-color-1.2.3.min.js') }}"></script>
    <script>
        $(".pick-a-color").pickAColor();
    </script>
@endsection
