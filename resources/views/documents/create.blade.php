@extends('flow.default')

@section('title') Upload Document @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <div class="nav-btn-group">
            <a href="javascript:void(0)" onclick="saveDocument()" class="btn btn-success btn-lg mt-3 ml-2 float-right">Save</a>
            <a href="{{route('documents.index')}}" class="btn btn-outline-primary btn-sm mt-3">Back</a>
        </div>
    </div>
@endsection

@section('content')
    <div class="content-container page-content">
        <div class="row col-md-12 h-100 pr-0">
            @yield('header')
            <div class="container-fluid index-container-content">
                <div class="table-responsive h-100">
                {{Form::open(['url' => route('documents.store'), 'method' => 'post','files'=>true,'id'=>'save_document_form'])}}

                <div class="form-group mt-3">
                    {{Form::label('name', 'Name')}}
                    {{Form::text('name',old('name'),['class'=>'form-control'. ($errors->has('name') ? ' is-invalid' : ''),'placeholder'=>'Name'])}}
                    @foreach($errors->get('name') as $error)
                        <div class="invalid-feedback">
                            {{ $error }}
                        </div>
                    @endforeach
                </div>

                <div class="form-group">
                    {{Form::label('file', 'File')}}
                    {{Form::file('file',['class'=>'form-control'. ($errors->has('file') ? ' is-invalid' : ''),'placeholder'=>'File'])}}
                    @foreach($errors->get('file') as $error)
                        <div class="invalid-feedback">
                            {{ $error }}
                        </div>
                    @endforeach
                </div>

                @if(request()->has('client') && request()->has('process_id') && request()->has('step_id'))
                    {{Form::hidden('client',request()->input('client'))}}
                    {{Form::hidden('process_id',request()->input('process_id'))}}
                    {{Form::hidden('step_id',request()->input('step_id'))}}
                @endif

                {{Form::close()}}
            </div>
            </div>
        </div>
    </div>
@endsection