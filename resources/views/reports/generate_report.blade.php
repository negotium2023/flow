@extends('adminlte.default')
@section('title') Generate Report @endsection
@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        {{--<a href="{{route('reports.index')}}" class="btn btn-dark btn-sm float-right"><i class="fa fa-caret-left"></i> Back</a>--}}
    </div>
@endsection
@section('content')
    <div class="container-fluid">
        {{--<div class="row">
            <div class="col-sm-9">
                <form id="customreportsform" class="form-inline mt-3">
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fa fa-search"></i>
                            </div>
                        </div>
                        {{Form::text('s',old('s'),['class'=>'form-control form-control-sm','placeholder'=>'Search...'])}}
                    </div>
                    <button type="submit" class="btn btn-sm btn-secondary ml-2 mr-2"><i class="fa fa-search"></i> Search</button>
                </form>
            </div>
        </div>--}}
        <hr>
        <div>
            {{Form::open(['url' => route('reports.generate_report_export'), 'method' => 'post'])}}
            <div class="form-group ml-3">
                {{Form::label('select_template', 'Template')}}
                <div class="input-group">
                    <td>{{Form::select('template',$templates,old('template'),['class'=>'form-control form-control-sm'. ($errors->has('template') ? ' is-invalid' : ''),'id'=>'template_id'])}}</td>
                    @foreach($errors->get('template') as $error)
                        <div class="invalid-feedback">
                            {{ $error }}
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="form-group ml-3 activity_div">
                {{Form::label('client_search', 'Client')}}
                <div class="input-group form-inline mb-2">
                    {{Form::text('client_search', old('client_search'), ['class'=>'form-control form-control-sm col-sm-2', 'id' => 'client_search'])}}
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-search"></i> </span>
                    </div>
                    &nbsp;
                    <a href="javascript:void(0)" class="btn btn-sm btn-info form-inline" id="clear_search" /><i class="fa fa-eraser"></i> Clear</a>
                </div>
                <div class="input-group table-responsive{{($errors->has('client_id') ? ' is-invalid' : '')}}">
                    <table class="table table-fixed table-clients">
                        <thead style="width: 100%;">
                        <tr class="bg-dark" style="background: #343a40;">
                            <th class="col-sm-1" style="background: #343a40;">Select</th>
                            <th class="col-sm-7" style="background: #343a40;">Client</th>
                            <th class="col-sm-4" style="background: #343a40;">ID Number</th>
                        </tr>
                        </thead>
                        <tbody style="width: 100%;" id="report_client">
                        <tr style="width: 100%;">
                            <td colspan="3" class="col-sm-12 loader" align="center" style="border-bottom: 0px;">
                            </td></tr>
                        </tbody>
                    </table>
                </div>
                @foreach($errors->get('client_id') as $error)
                    <div class="invalid-feedback">
                        {{ $error }}
                    </div>
                @endforeach
            </div>
            <div class="blackboard-fab mr-3 mb-3">
                <button type="submit" class="btn btn-info btn-lg form-inline" id="view_report">Generate Report</button>
                <button class="btn btn-info btn-lg form-inline" id="view_org">Generate Treeview</button>
            </div>
            {{Form::close()}}
        </div>
    </div>
    <div class="modal fade" id="report_details">
        <div class="modal-dialog" style="width:650px !important;max-width:650px;">
            <div class="modal-content">
                <div class="modal-header text-center" style="border-bottom: 0px;padding:.5rem;">
                    <h5 class="modal-title">Report Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body mx-3">
                    {{Form::open(['url' => route('reports.generate_report_export'), 'method' => 'post','id'=>'report_details_form','name'=>'report_details_form'])}}
                    <table class="table table-borderless">
                        <tr>
                            <td width="30%">
                        <input type="hidden" name="client_id" class="clientid">
                        <input type="hidden" name="template" class="templateid">
                                <label>Reason for Review:</label></td>
                            <td>
                        <input type="text" name="report_reason" class="report_reason form-control form-control-sm pl-2" spellcheck="true">
                            </td>
                        </tr><tr>
                            <td><label>Report Title:</label></td>
                            <td><input type="text" name="report_description" class="report_description form-control form-control-sm pl-2" spellcheck="true"></td>
                        </tr>
                        <tr>
                            <td><label>Committee Name:</label></td>
                            <td><input type="text" name="report_committee" class="report_committee form-control form-control-sm pl-2" spellcheck="true"></td>
                        </tr>
                        <tr>
                            <td><label>Date of meeting:</label></td>
                            <td><input type="date" name="report_date" class="report_date form-control form-control-sm pl-2"></td>
                        </tr>
                    </table>
                    <div class="form-group text-center">
                        <button class="btn btn-sm btn-default" id="reportDetailSave">Save</button>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('extra-css')
    <style>

        .table-fixed {
            width: 100%;
        }
        .table-fixed tbody {
            height: 350px;
            overflow-y: auto;
            width: 100%;
        }
        .table-fixed thead, .table-fixed tbody, .table-fixed tr, .table-fixed td, .table-fixed th {
            display: block;
        }
        .table-fixed tbody td {
            float: left;
        }
        .table-fixed thead tr th {
            float: left;
        }

        .table-fixed tr.bg-dark{
            background: #343a40;
        }
    </style>
@endsection
@section('extra-js')
    <script>
        $(function(){
            $('#view_report').on('hidden.bs.modal',function () {
                $('#report_details').find('.clientid').val('');
                $('#report_details').find('.templateid').val('');
                $('#report_details').find('.report_reason').val('');
                $('#report_details').find('.report_description').val('');
                $('#report_details').find('.report_date').val('');
                $('#report_details').find('.report_committee').val('');
            })

            $('#report_details').modal('hide');

            getClients();

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

            var xTriggered = 0;

            $('#client_search').on('keyup',function (event){
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

                let search = $('#client_search').val();

                if($('#client_search').val().length > 0) {
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
                        url: '/search_clients/' + search,
                        type: 'GET',
                        beforeSend: function () {
                            $('.loader').html('<tr><td colspan="3" class="col-sm-12">' + spinner + '</td></tr>');
                        },
                        success: function (data) {
                            let rows = '';
                            let idnum = '';
                            $.each(data, function (key, value) {
                                if(value.id_number.length > 0) {
                                    idnum = value.id_number;
                                } else {
                                    idnum = '&nbsp;';
                                }
                                rows = rows + '<tr><td align="left" class="col-sm-1"><input type="radio" name="client_id[]" class="client_id" value="' + value.id + '" /></td><td class="col-sm-7">' + value.name + '</td><td class="col-sm-4">' + idnum + '</td></tr>';
                            });
                            //alert(rows);
                            $("#report_client").html(rows);
                        }
                    });
                } else {
                    getClients()
                }
            });

            $('#clear_search').on('click',function(){
                $('#client_search').val('');
                getClients()
            });

            $("#reportDetailSave").on('click',function(e){
                e.preventDefault();
                $('#overlay').fadeIn();

                let client = $('body').find("#report_client input[type='radio']:checked").val();
                if(client) {
                    window.open('/organogram/' + client, '_blank');
                }
                setTimeout(function(){$('#report_details_form').submit(); $('#overlay').fadeOut();},5000);

                $('#report_details').modal('hide');

                }

            )

            $("#view_report").click(function(e){
                e.preventDefault();

                let type = $('body').find("#template_id").val();
                    let client_id = $('body').find("#report_client input[type='radio']:checked").val();
                    let template_id = $('body').find("#template_id").val();
                    $('#report_details').modal('show');
                    $('#report_details').find('.report_reason').val('');
                    $('#report_details').find('.report_description').val('');
                    $('#report_details').find('.report_date').val('');
                    $('#report_details').find('.report_committee').val('');
                    $('#report_details').find('.clientid').val(client_id);
                    $('#report_details').find('.templateid').val(template_id);

            })

            $("#view_org").on('click',function(e){
                e.preventDefault();
                let client = $('body').find("#report_client input[type='radio']:checked").val();
                if(client) {
                    window.open('/organogram/' + client, '_blank');
                }
            })

            function getClients() {
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

                let err = 0;
                if(err === 0) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        dataType: 'json',
                        url: '/get_clients',
                        type: 'GET',
                        beforeSend: function () {
                            $('.loader').html('<tr><td colspan="3" class="col-sm-12">' + spinner + '</td></tr>');
                        },
                        success: function (data) {
                            let rows = '';
                            $.each(data, function (key, value) {
                                rows = rows + '<tr><td align="left" class="col-sm-1"><input type="radio" name="client_id[]" class="client_id" value="' + value.id + '" /></td><td class="col-sm-7">' + value.name + '</td><td class="col-sm-4">' + value.id_number + '</td></tr>';
                            });
                            //alert(rows);
                            $(".table-clients").addClass('table-striped');
                            $("#report_client").html(rows);
                        }
                    });
                }
            }


        });



    </script>
@endsection