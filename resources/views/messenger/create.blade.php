@extends('flow.default')
@section('title') New Message @endsection
@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <a href="{{route('messages')}}" class="btn btn-dark btn-sm float-right"><i class="fa fa-caret-left"></i> Back</a>
    </div>
@endsection
@section('content')
    <div class="content-container">
        <div class="row col-md-12">
            @yield('header')
            <div class="container-fluid">
                <div class="table-responsive">
                    <form action="{{ route('messages.store') }}" method="post">
                        {{ csrf_field() }}
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">To</label>
                                @if($users->count() > 0)
                                    <select name="recipients" class="form-control form-control-sm select2 chosen-select" multiple>
                                        @foreach($users as $user)

                                            <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>

                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <!-- Subject Form Input -->
                            <div class="form-group">
                                <label class="control-label">Subject</label>
                                <input type="text" class="form-control form-control-sm" name="subject" placeholder="Subject"
                                       value="{{ old('subject') }}">
                            </div>

                            <!-- Message Form Input -->
                            <div class="form-group">
                                <label class="control-label">Message</label>
                                <textarea name="message" rows="10" id ="message" class="my-editor form-control form-control-sm">@if(Session::has('page_url')) Hi<br /><br />please have a look at <a href="{{Session::get('page_url')}}">{{Session::get('page_url')}}</a>. @endif</textarea>
                            </div>

                        <!-- Submit Form Input -->
                            <div class="blackboard-fab mr-3 mb-3">
                                <button type="submit" class="btn btn-info btn-lg"><i class="fa fa-send"></i> Send</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('extra-css')
    <link rel="stylesheet" href="{{asset('chosen/chosen.min.css')}}">
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
            toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link",
            relative_urls: false,

            external_filemanager_path:"{{url('tinymce/filemanager')}}/",
            filemanager_title:"Responsive Filemanager" ,
            external_plugins: { "filemanager" : "{{url('tinymce')}}/filemanager/plugin.min.js"}
        };

        tinymce.init(editor_config);
    </script>
@endsection