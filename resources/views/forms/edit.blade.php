@extends('flow.default')

@section('title') Edit CRM @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <div class="nav-btn-group">
            <a href="javascript:void(0)" onclick="saveForm()" class="btn btn-success btn-lg mt-3 ml-2 float-right">Save</a>
            <a href="{{route('forms.index')}}" class="btn btn-outline-primary btn-sm mt-3">Back</a>
        </div>
    </div>
@endsection

@section('content')
    <div class="content-container page-content">
        <div class="row col-md-12 h-100 pr-0">
            @yield('header')
            <div class="container-fluid index-container-content">
                <div class="table-responsive h-100">
                {{Form::open(['url' => route('forms.update',$form), 'method' => 'post','class'=>'mt-3 mb-3','files'=>true,'autocomplete' => 'off','id'=>'save_form_form'])}}
                {{Form::hidden('form_id',$form->id,['class'=>'form-control','id'=>'form_id'])}}
                    <div class="form-group">
                        {{Form::label('name', 'Name')}}
                        {{Form::text('name',$form->name,['class'=>'form-control form-control-sm'. ($errors->has('name') ? ' is-invalid' : ''),'placeholder'=>'Name'])}}
                        @foreach($errors->get('name') as $error)
                            <div class="invalid-feedback">
                                {{ $error }}
                            </div>
                        @endforeach
                    </div>

                    <div class="form-group">
                        {{Form::label('category', 'Category')}}
                        {{Form::select('category',$categories,$form->category_id,['class'=>'form-control form-control-sm'. ($errors->has('category') ? ' is-invalid' : ''),'placeholder'=>'Category'])}}
                        @foreach($errors->get('category') as $error)
                            <div class="invalid-feedback">
                                {{ $error }}
                            </div>
                        @endforeach
                    </div>

                    <div class="form-group">
                        {{Form::label('label', 'Label')}}
                        {{Form::text('label',$form->label,['class'=>'form-control form-control-sm'. ($errors->has('label') ? ' is-invalid' : ''),'placeholder'=>'Label'])}}
                        @foreach($errors->get('label') as $error)
                            <div class="invalid-feedback">
                                {{ $error }}
                            </div>
                        @endforeach
                    </div>

                {{Form::close()}}
            </div>
            </div>
        </div>
    </div>
@endsection