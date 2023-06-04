@extends('flow.default')

@section('title') Edit Action @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <div class="nav-btn-group">
            <a href="javascript:void(0)" onclick="saveAction()" class="btn btn-success btn-lg mt-3 ml-2 float-right">Save</a>
            <a href="{{route('action.index')}}" class="btn btn-outline-primary btn-sm mt-3">Back</a>
        </div>
    </div>
@endsection

@section('content')
    <div class="content-container page-content">
        <div class="row col-md-12 h-100 pr-0">
            @yield('header')
            <div class="container-fluid index-container-content" style="overflow-y: auto;">
            <div class="col-sm-12">
                {{Form::open(['url' => route('action.update',$action["id"]), 'method' => 'post','class'=>'mt-3 mb-3','autocomplete' => 'off','id'=>'save_action_form','files'=>true])}}
                <div class="form-group ml-3">
                    {{Form::label('action_name', 'Name')}}
                    <div class="input-group">
                        {{Form::text('action_name', $action["name"], ['class'=>'form-control form-control-sm','id'=>'action_name'])}}
                        {{Form::hidden('action_id', $action["id"], ['class'=>'form-control form-control-sm','id'=>'action_id'])}}

                    </div>
                </div>
                <div class="form-group ml-3">
                    {{Form::label('action_description', 'Description')}}
                    <div class="input-group">
                        {{Form::text('action_description', $action["description"], ['class'=>'form-control form-control-sm','id'=>'action_description'])}}

                    </div>
                </div>
                <div class="form-group ml-3">
                    {{Form::label('action_process', 'Process')}}
                    <div class="input-group">
                        {{Form::select('action_process2',$process, $action["process_id"], ['class'=>'form-control form-control-sm','disabled' => 'disabled'])}}
                        <input type="hidden" name="action_process" value="{{$action["process_id"]}}" id="actions_process" />
                    </div>
                </div>
                <div class="form-group ml-3">
                    {{Form::label('action_step', 'Step')}}
                    <div class="input-group">
                        {{Form::select('action_step2',$steps, $action["step_id"], ['class'=>'form-control form-control-sm','disabled' => 'disabled'])}}
                        <input type="hidden" name="action_step" value="{{$action["step_id"]}}" id="action_step" />
                    </div>
                </div>
                <div class="form-group ml-3 activity_div">
                    {{Form::label('action_process', 'Activities')}}
                    <div class="input-group form-inline mb-2">
                        {{Form::text('action_search', old('action_search'), ['class'=>'form-control form-control-sm col-sm-2', 'id' => 'action_search'])}}
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-search"></i> </span>
                        </div>
                        &nbsp;
                        <a href="javascript:void(0)" class="btn btn-sm btn-info form-inline" id="clear_search" /><i class="fa fa-eraser"></i> Clear</a>
                    </div>
                    <div class="input-group">
                        <table class="table table-bordered table-responsive table-activities" style="width: 100%;max-height:350px;height:350px;">
                            <thead style="width: 100%;">
                            <tr class="bg-dark">
                                <th class="last">Select</th>
                                <th style="width: 100%;">Activity</th>
                            </tr>
                            </thead>
                            <tbody style="width: 100%;" id="actions_activities">
                            <tr style="width:100%"><td colspan="2" class="loader" align="center" style="border-bottom: 0px;"></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="form-group ml-3">
                    {{Form::label('action_recipients', 'Recipients')}}
                    <div class="input-group">
                        {{Form::select('action_recipients[]',$recipients, explode(",", $action->users), ['class'=>'chosen-select form-control form-control-sm','multiple','id'=>'action_recipients'])}}
                    </div>
                </div>
                    {{Form::close()}}
{{--                <div class="form-group ml-3">
                    {{Form::label('action_clients', 'Clients')}}
                    <div class="input-group">
                        {{Form::select('action_clients[]',$clients, $action_clients, ['class'=>'chosen-select form-control form-control-sm','multiple','id'=>'action_clients'])}}
                    </div>
                </div>
                <div class="form-group ml-3 activity_div">
                    {{Form::label('action_due_date', 'Due Date')}}
                    <div class="input-group form-inline mb-2">
                        {{Form::date('action_due_date', $action["due_date"], ['class'=>'form-control form-control-sm col-sm-2','id'=>'action_due_date'])}}
                    </div>
                </div>--}}
                {{--<div class="blackboard-fab mr-3 mb-3">
                    {{Form::open(['url' => route('action.update',$action["id"]), 'method' => 'post','style'=>'display:inline-block;flex-flow:row wrap;align-items:center;'])}}
                    <input type="hidden" name="save_action_name" id="save_action_name" />
                    <input type="hidden" name="save_action_description" id="save_action_description" />
                    <input type="hidden" name="save_action_process" id="save_action_process" />
                    <input type="hidden" name="save_action_recipients" id="save_action_recipients" />
                    <input type="hidden" name="save_action_clients" id="save_action_clients" />
                    <input type="hidden" name="save_action_due_date" id="save_action_due_date" />
                    <button class="btn btn-info btn-md form-inline" id="save"><i class="fa fa-save"></i> Save</button>
                    {{Form::close()}}
                    --}}{{--{{Form::open(['url' => route('action.update_send',$action["id"]), 'method' => 'post','style'=>'display:inline-block;flex-flow:row wrap;align-items:center;'])}}
                    <input type="hidden" name="savea_action_name" id="savea_action_name" />
                    <input type="hidden" name="savea_action_description" id="savea_action_description" />
                    <input type="hidden" name="savea_action_process" id="savea_action_process" />
                    <input type="hidden" name="savea_action_recipients" id="savea_action_recipients" />
                    <input type="hidden" name="savea_action_clients" id="savea_action_clients" />
                    <input type="hidden" name="savea_action_due_date" id="savea_action_due_date" />
                    <button class="btn btn-info btn-md form-inline" id="saveandsend"><i class="far fa-paper-plane"></i> Save &amp; Send</button>
                    {{Form::close()}}--}}{{--
                </div>--}}
            </div>
            </div>
        </div>
    </div>
@endsection
@section('extra-css')
    <link rel="stylesheet" href="{{asset('chosen/chosen.min.css')}}">
    <style>

        thead th {
            position: -webkit-sticky; /* for Safari */
            position: sticky;
            top: 0;
            z-index: 2;
            color: #fff;
            background-color: #343a40;
            box-shadow: 0 1px 1px rgba(0,0,0,.075);
        }

        [data-href] {
            cursor: pointer;
        }

        .loader{
            width: 100%;
            height:100%;
            text-align: center;
            justify-content: center;
            align-items: center;
            vertical-align: middle;
        }

        .loader svg{
            display: block;
            margin: 0px auto;
        }
        .loader svg path,
        .loader svg rect{
            fill: #ccc;
            margin: 0 auto;
        }
    </style>
@endsection
@section('extra-js')
    <script>
        $(function(){

            var spinner = '<svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"\n' +
                '     width="40px" height="40px" preserveAspectRatio="xMaxYMax meet" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;">\n' +
                '  <path fill="#fff" d="M43.935,25.145c0-10.318-8.364-18.683-18.683-18.683c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615c8.072,0,14.615,6.543,14.615,14.615H43.935z">\n' +
                '    <animateTransform attributeType="xml"\n' +
                '      attributeName="transform"\n' +
                '      type="rotate"\n' +
                '      from="0 25 25"\n' +
                '      to="360 25 25"\n' +
                '      dur="1s"\n' +
                '      repeatCount="indefinite"/>\n' +
                '    </path>\n' +
                '  </svg>';

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                dataType: 'json',
                url: '/get_edit_action_process_activities/' + $('#actions_process').val() + '/' + $('#action_step').val() + '/' + $('#action_id').val(),
                type: 'POST',
                data: {process_id: $('#actions_process').val()},
                beforeSend: function () {
                    $('.loader').html(spinner);
                },
                success: function (data) {
                    let rows = '';
                    $.each(data, function (key, value) {
                        if (value.selected === '1') {
                            rows = rows + '<tr><td align="center"><input type="checkbox" id="action_activity_' + value.id + '" name="action_activities[]" class="action_activity" value="' + value.id + '" checked="checked" /></td><td>' + value.name + '</td></tr>';
                        } else {
                            rows = rows + '<tr><td align="center"><input type="checkbox" id="action_activity_' + value.id + '" name="action_activities[]" class="action_activity" value="' + value.id + '" /></td><td>' + value.name + '</td></tr>';
                        }

                    });
                    //alert(rows);
                    $(".table-activities").addClass('table-striped');
                    $("#actions_activities").html(rows);
                }
            });

            /*$('#saveandsend').on("click",function (e) {

                e.preventDefault();

                let err = 0;

                let action_name = $('#action_name').val();
                let action_description = $('#action_description').val();
                let actions_process = $('#actions_process').val();
                let action_recipients = $("#action_recipients").val();
                let action_clients = $("#action_clients").val();
                let action_due_date = $("#action_due_date").val();

                if(action_name.length === 0){
                    err++;
                    $('#action_name').addClass('is-invalid');
                } else {
                    $('#action_name').removeClass('is-invalid');
                    $('#savea_action_name').val(action_name);
                }
                if(action_description.length === 0){
                    err++;
                    $('#action_description').addClass('is-invalid');
                } else {
                    $('#action_description').removeClass('is-invalid');
                    $('#savea_action_description').val(action_description);
                }

                if(actions_process.length === 0){
                    err++;
                    $('#action_process').addClass('is-invalid');
                } else {
                    $('#action_process').removeClass('is-invalid');
                    $('#savea_action_process').val(actions_process);
                }

                if(action_recipients.length === 0){
                    err++;
                    $('#action_recipients').addClass('is-invalid');
                } else {
                    $('#action_recipients').removeClass('is-invalid');
                    $('#savea_action_recipients').val(action_recipients);
                }

                if(action_clients.length === 0){
                    err++;
                    $('#action_clients').addClass('is-invalid');
                } else {
                    $('#action_clients').removeClass('is-invalid');
                    $('#savea_action_clients').val(action_clients);
                }

                if(action_due_date.length === 0){
                    err++;
                    $('#action_due_date').addClass('is-invalid');
                } else {
                    $('#action_due_date').removeClass('is-invalid');
                    $('#savea_action_due_date').val(action_due_date);
                }

                if(err === 0){
                    $(this).parents('form:first').submit();
                }

            })*/

            $('#save').on("click",function (e) {
                e.preventDefault();

                let err = 0;

                let action_name = $('#action_name').val();
                let action_description = $('#action_description').val();
                let actions_process = $('#actions_process').val();
                let action_recipients = $("#action_recipients").val();

                if(action_name.length === 0){
                    err++;
                    $('#action_name').addClass('is-invalid');
                } else {
                    $('#action_name').removeClass('is-invalid');
                    $('#save_action_name').val(action_name);
                }
                if(action_description.length === 0){
                    err++;
                    $('#action_description').addClass('is-invalid');
                } else {
                    $('#action_description').removeClass('is-invalid');
                    $('#save_action_description').val(action_description);
                }

                if(actions_process.length === 0){
                    err++;
                    $('#action_process').addClass('is-invalid');
                } else {
                    $('#action_process').removeClass('is-invalid');
                    $('#save_action_process').val(actions_process);
                }

                if(action_recipients.length === 0){
                    err++;
                    $('#action_recipients').addClass('is-invalid');
                } else {
                    $('#action_recipients').removeClass('is-invalid');
                    $('#save_action_recipients').val(action_recipients);
                }

                if(err === 0){
                    $(this).parents('form:first').submit();
                }

            })

            $('#clear_search').on('click',function(){
                let process_id = $('#actions_process').val();
                let step_id = $('#action_step').val();

                $('#action_search').val('');
                getSelectedActivities(process_id,step_id)
            });

            var xTriggered = 0;

            $('#action_search').on('keyup',function (event){

                let search = $('#action_search').val();
                let process_id = $('#actions_process').val();
                let step_id = $('#action_step').val();

                if($('#action_search').val().length > 0) {
                    if (event.which == 13) {
                        event.preventDefault();
                    }
                    xTriggered++;

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        dataType: 'json',
                        url: '/search_action_activities/' + process_id + '/' + step_id + '/' + search,
                        type: 'POST',
                        data: {process_id: process_id,step_id:step_id, search: search},
                        beforeSend: function () {
                            $('.loader').html(spinner);
                        },
                        success: function (data) {
                            let rows = '';
                            $.each(data, function (key, value) {
                                if (value.selected === '1') {
                                    rows = rows + '<tr><td align="center"><input type="checkbox" id="action_activity_' + value.id + '" name="action_activities[]" class="action_activity" value="' + value.id + '" checked="checked" /></td><td>' + value.name + '</td></tr>';
                                } else {
                                    rows = rows + '<tr><td align="center"><input type="checkbox" id="action_activity_' + value.id + '" name="action_activities[]" class="action_activity" value="' + value.id + '" /></td><td>' + value.name + '</td></tr>';
                                }

                            });
                            //alert(rows);
                            $("#actions_activities").html(rows);
                        }
                    });
                } else {
                    getSelectedActivities(process_id,step_id)
                }
            });

            function getSelectedActivities(process_id,step_id) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    dataType: 'json',
                    url: '/get_action_process_selected_activities/' + process_id + '/' + step_id,
                    type: 'POST',
                    data: {process_id: process_id},
                    beforeSend: function () {
                        $('.loader').html(spinner);
                    },
                    success: function (data) {
                        let rows = '';
                        $.each(data, function (key, value) {
                            if (value.selected === '1') {
                                rows = rows + '<tr><td align="center"><input type="checkbox" id="action_activity_' + value.id + '" name="action_activities[]" class="action_activity" value="' + value.id + '" checked="checked" /></td><td>' + value.name + '</td></tr>';
                            } else {
                                rows = rows + '<tr><td align="center"><input type="checkbox" id="action_activity_' + value.id + '" name="action_activities[]" class="action_activity" value="' + value.id + '" /></td><td>' + value.name + '</td></tr>';
                            }

                        });
                        //alert(rows);
                        $(".table-activities").addClass('table-striped');
                        $("#actions_activities").html(rows);
                    }
                });
            }


            $("#actions_activities").on("click",".action_activity", function(){
                //if($(this).is(':checked')) {
                let id = $(this).val();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name=\"csrf-token\"]').attr('content')
                    }
                });
                $.ajax({
                    dataType: 'json',
                    url: '/store_action_activity/' + id,
                    type: 'post',
                    data: {id: id}
                }).done(function (data) {

                });
                //}
            });
        });



    </script>
@endsection
