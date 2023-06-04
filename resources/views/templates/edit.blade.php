@extends('flow.default')

@section('title') Edit Document Template @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <div class="nav-btn-group">
            <a href="javascript:void(0)" onclick="saveTemplate()" class="btn btn-success btn-lg mt-3 ml-2 float-right">Save</a>
            <a href="{{route('templates.index')}}" class="btn btn-outline-primary btn-sm mt-3">Back</a>
        </div>
    </div>
@endsection

@section('content')
    <div class="content-container page-content">
        <div class="row col-md-12 h-100 pr-0">
            @yield('header')
            <div class="container-fluid index-container-content">
                <div class="table-responsive h-100">
                {{Form::open(['url' => route('templates.update',$template), 'method' => 'put','files'=>true,'id'=>'save_template_form'])}}

                    <div class="form-group mt-3">
                        {{Form::label('ttype', 'Template Type')}}
                        {{Form::select('ttype',['0'=>'General','1'=>'Report','2'=>'Activity'],($template->template_type_id != null ? $template->template_type_id : '0'),['class'=>'form-control form-control-sm'. ($errors->has('name') ? ' is-invalid' : ''),'id'=>'ttype'])}}
                        @foreach($errors->get('ttype') as $error)
                            <div class="invalid-feedback">
                                {{ $error }}
                            </div>
                        @endforeach
                    </div>

                    <div class="form-group mt-3">
                        {{Form::label('name', 'Name')}}
                        {{Form::text('name',$template->name,['class'=>'form-control form-control-sm'. ($errors->has('name') ? ' is-invalid' : ''),'placeholder'=>'Name'])}}
                        @foreach($errors->get('name') as $error)
                            <div class="invalid-feedback">
                                {{ $error }}
                            </div>
                        @endforeach
                    </div>

                <div class="form-group">
                    {{Form::label('process', 'Application')}}
                    {{Form::select('process',$process,$template->process_id,['class'=>'form-control form-control-sm'. ($errors->has('process') ? ' is-invalid' : ''),'id'=>'process'])}}
                    @foreach($errors->get('process') as $error)
                        <div class="invalid-feedback">
                            {{ $error }}
                        </div>
                    @endforeach
                </div>

                    <div class="form-group">
                        {{Form::label('file', 'Template File')}}
                        {{Form::file('file',['class'=>'form-control form-control-sm'. ($errors->has('file') ? ' is-invalid' : ''),'placeholder'=>'File'])}}
                        @foreach($errors->get('file') as $error)
                            <div class="invalid-feedback">
                                {{ $error }}
                            </div>
                        @endforeach
                        <small>
                            Leave empty to keep <a href="{{route('template',['q'=>$template->file])}}" target="_blank" download="{{$template->name}}.{{$template->type()}}">previous file</a>
                        </small>
                    </div>

                    <div class="form-group">
                        {{Form::label('file', 'Variables')}}
                        <div class="row col-sm-12 table-responsive" v-pre>
                            <table class="table table-fixed">
                                <thead>
                                <tr>
                                    <th class="col-sm-4">Step</th>
                                    <th class="col-sm-4">Activity Name</th>
                                    <th class="col-sm-4">Variable</th>
                                </tr>
                                </thead>
                                <tbody id="template_vars" class="loader" style="height: 300px;overflow-y: auto;display:block;overflow-x: hidden;">
                                <tr>
                                    <td colspan="3" class="col-sm-12">
                                        <div class="alert alert-info col-sm-12">Please select a process to populate available variables for template generation.</div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
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
            height: 200px;
            overflow-y: auto;
            width: 100%;
        }
        .table-fixed thead, .table-fixed tbody, .table-fixed tr, .table-fixed td, .table-fixed th {
            display: block;
        }
        .table-fixed tbody td:first-child {
            clear: both;
        }
        .table-fixed tbody td {
            float: left;
            word-break: break-all;
        }
        .table-fixed thead tr th {
            float: left;
        }
    </style>
@endsection
@section('extra-js')
    <script>
        $(function(){

            if($('#process').val() != '0') {
                getVars($('#process').val());
            }

            $('#process').on('change',function(){
                let process_id = $('#process').val();

                getVars(process_id);
            });
        })

        function getVars(process_id){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            getClientVars();
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

            $.ajax({
                url: '/templates/activities/' + process_id,
                type: "GET",
                dataType: "json",
                beforeSend: function () {
                    $('.loader').html('<tr><td colspan="3" class="col-sm-12">' + spinner + '</td></tr>');
                },
                success: function (data) {

                    let rows = '<tr>';
                    let count = 0;
                    $.each(data, function (key, value) {
                        count++;
                        rows = rows + '<td class="col-sm-4">' + value.step + '</td>';
                        rows = rows + '<td class="col-sm-4">' + value.name + '</td>';
                        rows = rows + '<td class="col-sm-4">$&#123;' + value.variable + '&#125;</td>';
                        if (count === 1) {
                            rows = rows + '</tr><tr>';
                            count = 0;
                        }
                    });

                    $("#template_vars").append(rows);
                }
            });
        }

        function getClientVars(){
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

            $.ajax({
                url: '/templates/clients',
                type: "POST",
                dataType: "json",
                beforeSend: function () {
                    $('.loader').html('<tr><td colspan="3" class="col-sm-12">' + spinner + '</td></tr>');
                },
                success: function (data) {

                    let rows = '<tr>';
                    let count = 0;
                    $.each(data, function (key, value) {
                        count++;
                        rows = rows + '<td class="col-sm-4">' + value.step + '</td>';
                        rows = rows + '<td class="col-sm-4">' + value.name + '</td>';
                        rows = rows + '<td class="col-sm-4">$&#123;' + value.variable + '&#125;</td>';
                        if (count === 1) {
                            rows = rows + '</tr><tr>';
                            count = 0;
                        }
                    });

                    $("#template_vars").html(rows);
                }
            });
        }
    </script>
@endsection