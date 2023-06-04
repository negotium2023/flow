@extends('layouts.app')

@section('title') Add Email Signature @endsection

@section('header')
    <h1><i class="fa fa-pencil-square-o"></i> @yield('title')</h1>
@endsection

@section('content')
    {{Form::open(['url' => route('emailsignatures.store'), 'method' => 'post','files'=>true])}}

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
        {{Form::label('Signature Content')}}
        {{ Form::textarea('content', null, ['class'=>'form-control my-editor','size' => '30x5']) }}
        @foreach($errors->get('content') as $error)
            <div class="invalid-feedback">
                {{ $error }}
            </div>
        @endforeach
    </div>
    <div class="form-group">
        <button type="submit" class="btn">Save</button>
    </div>

    {{Form::close()}}
@endsection
@section('extra-js')
    <script>
        var editor_config = {
            path_absolute : "/",
            selector: "textarea.my-editor",
            plugins: [
                "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                "searchreplace wordcount visualblocks visualchars code fullscreen",
                "insertdatetime media nonbreaking save table contextmenu directionality",
                "emoticons template paste textcolor colorpicker textpattern"
            ],
            toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media",
            relative_urls: false,
            file_browser_callback : function(field_name, url, type, win) {
                var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
                var y = window.innerHeight|| document.documentElement.clientHeight|| document.getElementsByTagName('body')[0].clientHeight;

                var cmsURL = editor_config.path_absolute + 'laravel-filemanager?field_name=' + field_name;
                if (type == 'image') {
                    cmsURL = cmsURL + "&type=Images";
                } else {
                    cmsURL = cmsURL + "&type=Files";
                }

                tinyMCE.activeEditor.windowManager.open({
                    file : cmsURL,
                    title : 'Filemanager',
                    width : x * 0.8,
                    height : y * 0.8,
                    resizable : "yes",
                    close_previous : "no"
                });
            }
        };

        tinymce.init(editor_config);
    </script>
@endsection