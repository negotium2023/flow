
<form action="{{ route('messages.update', $thread->id) }}" method="post">
{{ method_field('put') }}
{{ csrf_field() }}

<!-- Message Form Input -->
    <div class="form-group">
        <textarea name="message" rows="10" class="form-control formcontrol-sm my-editor">{{ old('message') }}</textarea>
    </div>

    @if($users->count() > 0)
        {{--<div class="checkbox">
            @foreach($users as $user)
                <label title="{{ $user->first_name }}">
                    <input type="checkbox" name="recipients[]" value="{{ $user->id }}">{{ $user->first_name }}
                </label>
            @endforeach
        </div>--}}
        {{--<select name="recipients" class="select2 chosen-select" multiple>
        @foreach($users as $user)

                <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>

        @endforeach
        </select>--}}
@endif

<!-- Submit Form Input -->
    <div class="blackboard-fab mr-3 mb-3">
        <button type="submit" class="btn btn-info btn-lg"><i class="fa fa-send"></i> Send</button>
    </div>
</form>
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