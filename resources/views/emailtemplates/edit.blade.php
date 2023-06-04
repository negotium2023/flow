@extends('flow.default')

@section('title') Edit Email Template @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <div class="nav-btn-group">
            <a href="javascript:void(0)" onclick="saveEmailTemplate()" class="btn btn-success btn-lg mt-3 ml-2 float-right">Save</a>
            <a href="{{route('emailtemplates.index')}}" class="btn btn-outline-primary btn-sm mt-3"><i class="fa fa-caret-left"></i> Back</a>
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
                    {{Form::open(['url' => route('emailtemplates.update',$result), 'method' => 'post','files'=>true])}}

                    <div class="form-group mt-3">
                        {{Form::label('name', 'Name')}}
                        {{Form::text('name',$result->name,['class'=>'form-control'. ($errors->has('name') ? ' is-invalid' : ''),'placeholder'=>'Name'])}}
                        @foreach($errors->get('name') as $error)
                            <div class="invalid-feedback">
                                {{ $error }}
                            </div>
                        @endforeach
                    </div>

                    <div class="form-group mt-3">
                        {{Form::label('Email Subject')}}
                        {{Form::text('subject',$result->email_subject,['class'=>'form-control'. ($errors->has('subject') ? ' is-invalid' : ''),'placeholder'=>'Email Subject'])}}
                        @foreach($errors->get('subject') as $error)
                            <div class="invalid-feedback">
                                {{ $error }}
                            </div>
                        @endforeach
                    </div>

                    <div class="form-group">
                        {{Form::label('Email Body')}}
                        {{ Form::textarea('content', $result->email_content, ['class'=>'form-control my-editor','size' => '30x5']) }}
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
@section('extra-js')
    <script>
        var editor_config = {
            path_absolute : "/",
            relative_urls: false,
            convert_urls : false,
            selector: "textarea.my-editor",
            setup: function (editor) {
                editor.on('change', function () {
                    tinymce.triggerSave();
                });
            },
            plugins: [
                "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                "searchreplace wordcount visualblocks visualchars code fullscreen",
                "insertdatetime media nonbreaking save table contextmenu directionality",
                "emoticons template paste textcolor colorpicker textpattern"
            ],
            toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media",
            relative_urls: false,

            external_filemanager_path:"{{url('tinymce/filemanager')}}/",
            filemanager_title:"Responsive Filemanager" ,
            external_plugins: { "filemanager" : "{{url('tinymce')}}/filemanager/plugin.min.js"}
        };

        tinymce.init(editor_config);
    </script>
@endsection