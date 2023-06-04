@extends('flow.default')

@section('title') Edit Whatsapp Template @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <div class="nav-btn-group">
            <a href="javascript:void(0)" onclick="saveWhatsappTemplate()" class="btn btn-success btn-lg mt-3 ml-2 float-right">Save</a>
            <a href="{{route('whatsapptemplates.index')}}" class="btn btn-outline-primary btn-sm mt-3"><i class="fa fa-caret-left"></i> Back</a>
        </div>
    </div>
@endsection

@section('content')
    <div class="content-container page-content">
        <div class="row col-md-12 h-100 pr-0">
            @yield('header')
            <div class="container-fluid index-container-content">
                <div class="table-responsive h-100">
                    @foreach($template as $result)
                        {{Form::open(['url' => route('whatsapptemplates.update',$result), 'method' => 'post','files'=>true, 'id' => 'whatsappform'])}}

                        <div class="form-group mt-3">
                            {{Form::label('name', 'Name')}}
                            {{Form::text('name',$result->name,['class'=>'form-control'. ($errors->has('name') ? ' is-invalid' : ''),'placeholder'=>'Name'])}}
                            @foreach($errors->get('name') as $error)
                                <div class="invalid-feedback">
                                    {{ $error }}
                                </div>
                            @endforeach
                        </div>

                        <div class="form-group">
                            {{Form::label('Whatsapp Body')}}
                            {{ Form::textarea('content', $result->whatsapp_content, ['class'=>'form-control my-editor','size' => '30x5']) }}
                            @foreach($errors->get('content') as $error)
                                <div class="invalid-feedback">
                                    {{ $error }}
                                </div>
                            @endforeach
                        </div>

                        {{Form::close()}}
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection