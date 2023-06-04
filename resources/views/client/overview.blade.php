@extends('client.show')

@section('tab-content')
    <div class="row col-md-12 h-100 m-0 p-0">
        <div class="col-md-6 h-100 pt-0 pb-0 pl-0">
            <div class="card-group overview-addnote">
            <div class="card h-100">
                <h5 class="card-title">Add a new note</h5>
                <div class="card-body pt-0">
                    {{Form::open(['url' => route('clients.storecomment', $client), 'method' => 'post','id'=>'add_comment'])}}
                        {{Form::text('heading',old('heading'),['class'=>'form-control form-control-sm','placeholder'=>'Add heading','id'=>'title'])}}
                        {{Form::textarea('comment',old('comment'),['cols'=>'10','rows'=>'3','class'=>'form-control form-control-sm','placeholder'=>'Type your note here','id'=>'comment'])}}
                    <input type="submit" class="btn btn-success overview-note-button float-right" value="Save note">
                    {{Form::close()}}

                </div>
            </div>
            </div>
            <div class="card-group overview-openapplications">
            <div class="card h-100">
                <h5 class="card-title d-inline-block float-left">Processes in progress<a href="javascript:void(0)" onclick="startNewApplication({{$client->id}},{{$client->process_id}})" class="float-right d-inline-block" style="font-size: 14px;line-height: 24px;"><i class="fa fa-plus"></i> Start process</a></h5>
                <div class="card-body overflow-auto open-applications grid-items">
                    <div class="spinner"></div>
                </div>
            </div>
            </div>
        </div>

        <div class="col-md-6 h-100 pt-0 pb-0 pr-0">
            <div class="card h-100 overflow-auto">
                <h5 class="card-title">Notes</h5>
                <div class="card-body client-notes pt-0">
                    <div style="height: 100%;margin:auto;width:100%">
                    <div class="spinner"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('extra-js')
    <script>

        function deletecomment(id){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '/clients/deleteclientcomment/' + id,
                type: "POST",
                data: {comment: id},
                success: function (data) {
                    toastr.success('<strong>Success!</strong> ' + data);

                    toastr.options.timeOut = 1000;

                    $.ajax({
                        url: '/clients/' + {{ $client->id }} + '/getcomments',
                        type: "GET",
                        dataType: "json",
                        success: function (data) {

                            let row = '';

                            $.each(data.data, function(key,value) {
                                row = row + '<div class="card">' +
                                    '<span class="pull-right btn-danger clickable close-icon" onclick="deletecomment('+value.id+')" data-effect="fadeOut"><i class="fa fa-times"></i></span>' +
                                    '<div class="card-block">' +
                                    '<blockquote class="card-blockquote">' +
                                    '<div class="blockquote-header">' + value.title + '</div>' +
                                    '<div class="blockquote-body">' + value.comment + '</div>' +
                                    '<div class="blockquote-footer">'+value.cdate+'<a href="/profile/' + value.user_id + '" class="float-right">' + value.user_name + '</a></div>' +
                                    '</blockquote>' +
                                    '</div>' +
                                    '</div>';
                            });

                            $('.client-notes').html(row);
                        }
                    });
                }
            });
        }
        $(function (){

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '/clients/' + {{ $client->id }} + '/getcomments',
                type: "GET",
                dataType: "json",
                success: function (data) {
                    let row = '';

                    if(data.data.length > 0) {
                        $.each(data.data, function(key,value) {
                            row = row + '<div class="card">' +
                                '<span class="pull-right btn-danger clickable close-icon" onclick="deletecomment('+value.id+')" data-effect="fadeOut"><i class="fa fa-times"></i></span>' +
                                '<div class="card-block">' +
                                '<blockquote class="card-blockquote">' +
                                '<div class="blockquote-header">' + value.title + '</div>' +
                                '<div class="blockquote-body">' + value.comment + '</div>' +
                                '<div class="blockquote-footer">'+value.cdate+'<a href="/profile/' + value.user_id + '" class="float-right">' + value.user_name + '</a></div>' +
                                '</blockquote>' +
                                '</div>' +
                                '</div>';
                        });
                    } else {
                        row = row + '<div class="alert alert-info">There are currently no Notes for this client.</div>';
                    }

                    $('.client-notes').html(row);
                }
            });

            $.ajax({
                url: '/clients/' + {{ $client->id }} + '/current_applications',
                type: "GET",
                dataType: "json",
                success: function (data) {

                    let row = '';

                    if(data.length > 0) {
                        $.each(data, function (key, value) {
                            row = row + '<div class="d-table" style="width: 100%;border: 1px solid #ecf1f4;margin-bottom:0.75rem;">' +
                                '<div class="grid-icon">' +
                                '<i class="far fa-file-alt"></i>' +
                                '</div>' +
                                '<div class="grid-text">' +
                                '<span class="grid-heading">' + value.name + '</span>' +
                                'Date started:' +
                                '</div>' +
                                '<div class="grid-btn">' +
                                '<a href="/clients/' + {{ $client->id }} +'/progress/' + value.process_id + '/' + value.step_id + '/0" class="btn btn-primary btn-block">Continue</a>' +
                                '</div>' +
                                '</div>';
                        });
                    } else {
                        row = row + '<div class="alert alert-info">There are currently no Applications in progress for this client.</div>';
                    }

                    $('.open-applications').html(row);
                }
            });

            $('#add_comment').submit(function (e) {
                e.preventDefault();

                let err = 0;
                let title = $('#title').val();
                let comment = $('#comment').val();

                if(title.length === 0){
                    err++;
                    $('#title').addClass('is-invalid').removeClass('is-valid');
                } else {
                    $('#title').removeClass('is-invalid').addClass('is-valid');
                }

                if(comment.length === 0){
                    err++;
                    $('#comment').addClass('is-invalid').removeClass('is-valid');
                } else {
                    $('#comment').removeClass('is-invalid').addClass('is-valid');
                }

                if(err === 0) {
                    $.ajax({
                        url: '/clients/' + {{ $client->id }} +'/storecomment',
                        type: "POST",
                        data: {title: title, comment: comment},
                        success: function (data) {
                            toastr.success('<strong>Success!</strong> ' + data);

                            toastr.options.timeOut = 1000;

                            $('#title').removeClass('is-valid').val('');
                            $('#comment').removeClass('is-valid').val('');

                            $.ajax({
                                url: '/clients/' + {{ $client->id }} + '/getcomments',
                                type: "GET",
                                dataType: "json",
                                success: function (data) {
                                    let row = '';

                                    $.each(data.data, function(key,value) {
                                        row = row + '<div class="card">' +
                                            '<span class="pull-right btn-danger clickable close-icon" onclick="deletecomment('+value.id+')" data-effect="fadeOut"><i class="fa fa-times"></i></span>' +
                                            '<div class="card-block">' +
                                            '<blockquote class="card-blockquote">' +
                                            '<div class="blockquote-header">' + value.title + '</div>' +
                                            '<div class="blockquote-body">' + value.comment + '</div>' +
                                            '<div class="blockquote-footer">'+value.cdate+'<a href="/profile/' + value.user_id + '" class="float-right">' + value.user_name + '</a></div>' +
                                            '</blockquote>' +
                                            '</div>' +
                                            '</div>';
                                    });

                                    $('.client-notes').html(row);
                                }
                            });
                        }
                    });
                }
            });
        })
    </script>
@endsection
