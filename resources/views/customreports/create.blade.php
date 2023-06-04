@extends('flow.default')
@section('title') Create Report @endsection
@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <div class="nav-btn-group">
            <a href="javascript:void(0)" onclick="saveReport()" class="btn btn-success btn-sm mt-3 ml-2 float-right">Save</a>
            <a href="{{route('custom_report.index')}}" class="btn btn-outline-primary btn-sm mt-3 float-right">Back</a>
        </div>
    </div>
@endsection
@section('content')
    <div class="content-container page-content">
        <div class="row col-md-12 h-100 pr-0">
            @yield('header')
            <div class="container-fluid index-container-content" style="overflow: auto !important;">
                {{Form::open(['url' => route('custom_report.store'), 'method' => 'post','files'=>true,'id'=>'reportform'])}}
                <div class="form-group mt-3">
                    {{Form::label('name', 'Report Name:')}}
                    {{Form::text('name',old('name'),['class'=>'form-control form-control-sm'. ($errors->has('name') ? ' is-invalid' : ''),'placeholder'=>'Name'])}}
                    @foreach($errors->get('name') as $error)
                        <div class="invalid-feedback">
                            {{ $error }}
                        </div>
                    @endforeach
                </div>
                <div class="form-group mt-3">
                    {{Form::label('report_type', 'Report Type')}}
                    <div class="col-lg-8">
                        <div role="radiogroup" class="mt-0">
                            <input type="radio" class="group_step" value="process" name="group_step" id="group_step-enabled" ref="grouped">
                            <label for="group_step-enabled">Process</label><!-- remove whitespace
                                                                    --><input type="radio" class="group_step" value="crm" name="group_step" id="group_step-disabled" checked><!-- remove whitespace
                                                                    --><label for="group_step-disabled">CRM</label>

                            <span class="selection-indicator"></span>
                        </div>
                    </div>
                </div>
                <div class="form-group mt-3" id="processdiv" style="display: none;">
                    {{Form::label('process', 'Process to use for report:')}}

                    {{Form::select('process',$process ,old('process'),['class'=>'form-control form-control-sm','id' => 'process'])}}
                    @foreach($errors->get('process') as $error)
                        <div class="invalid-feedback">
                            {{ $error }}
                        </div>
                    @endforeach
                </div>
                <div class="form-group mt-3" id="crmdiv" style="display: none;">
                    {{Form::label('crm', 'CRM to use for report:')}}

                    {{Form::select('crm',$crm ,old('crm'),['class'=>'form-control form-control-sm','id' => 'crm'])}}
                    @foreach($errors->get('process') as $error)
                        <div class="invalid-feedback">
                            {{ $error }}
                        </div>
                    @endforeach
                </div>
                <div class="form-group mt-3">
                    {{Form::label('group', 'Group Report')}}
                    <div class="col-lg-4 text-left">
                        <div>
                            <input name="group_report" id="group_report" ref="grouped" type="checkbox" />
                        </div>
                    </div>
                    @foreach($errors->get('group_report') as $error)
                        <div class="invalid-feedback">
                            {{ $error }}
                        </div>
                    @endforeach
                </div>
                <div class="form-group mt-3">
                    {{Form::label('activity', 'Activity columns to display on report:')}}
                </div>
                <div class="form-group pb-3 mb-3" id="activities">

                </div>
                {{Form::close()}}
            </div>
        </div>
    </div>
@endsection
@section('extra-js')
    <script>
        $(document).ready(function() {

            if($('input[name="group_step"]:checked').val() === 'process'){
                $('#processdiv').show();
                $('#crmdiv').hide();
                getActivities('process',$('#process').val());
            }
            if($('input[name="group_step"]:checked').val() === 'crm'){
                $('#processdiv').hide();
                $('#crmdiv').show();
                getActivities('crm',$('#crm').val());
            }

            function getActivities(type,id) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    dataType: 'json',
                    url: '/get_report_activities/' + id,
                    type: 'GET',
                    data: {type:type,process_id: id}
                }).done(function (data) {
                    let rows = '<div class="col-sm-12 pull-left pb-2" style="min-height:50px;display: inline-block"><span style="display: table-cell"><input type="checkbox" name="activity__all" id ="activity_all" value="all" onclick="selectAll()" /></span><span style="display: table-cell;word-break: break-word;padding-left:5px;overflow-wrap: break-word;">Select All</span></div>';
                    $.each(data, function (key, value) {
                        rows = rows + '<div class="col-sm-12 pull-left pb-2" style="min-height:50px;display: inline-block"><span style="display: table-cell"><span style="display: table-cell;word-break: break-word;overflow-wrap: break-word;font-weight:bold;">' + value.name + '</span></div>';
                        $.each(value.activity, function (key, value) {
                            if(value.grouping == 0) {
                                rows = rows + '<div class="col-sm-4 pull-left pb-2" style="min-height:50px;display: inline-block"><span style="display: table-cell"><input type="checkbox" class="cactivity" name="activity[]" value="' + value.id + '" /></span><span style="display: table-cell;word-break: break-word;padding-left:5px;overflow-wrap: break-word;">' + value.name + '</span></div>';
                            } else {
                                rows = rows + '<div class="col-sm-4 pull-left pb-2" style="min-height:50px;display: inline-block"><span style="display: table-cell"><input type="checkbox" class="cactivity" name="activity[]" value="' + value.id + '" /></span><span style="display: table-cell;word-break: break-word;padding-left:5px;overflow-wrap: break-word;">' + value.name + '&nbsp;&nbsp;&nbsp;<i class="fa fa-object-group" aria-hidden="true" style="font-size:0.75em"></i></span></div>';
                            }
                        });
                    });
                    //alert(rows);
                    $("#activities").html(rows);
                });
            }

            $('input[name="group_step"]').on("change",function(){
                //alert($('input[name="group_step"]:checked').val());
                if($('input[name="group_step"]:checked').val() === 'process'){
                    $('#processdiv').show();
                    $('#crmdiv').hide();
                    getActivities('process',$('#process').val());
                }
                if($('input[name="group_step"]:checked').val() === 'crm'){
                    $('#processdiv').hide();
                    $('#crmdiv').show();
                    getActivities('crm',$('#crm').val());
                }
                //getActivities();
            })

            $('#process').on("change",function(){
                getActivities('process',$('#process').val());
            })

            $('#crm').on("change",function(){
                getActivities('crm',$('#crm').val());
            })

        });

        function selectAll(){
            if($("#activity_all").is(':checked')){
                $('.cactivity').each(function () {
                    if($(this).is(':disabled')) {
                        $(this).prop('checked', false);
                    } else {
                        $(this).prop('checked', true);
                    }
                })
            } else {
                $('.cactivity').each(function () {
                    if($(this).is(':disabled')) {
                        $(this).prop('checked', false);
                    } else {
                        $(this).prop('checked', false);
                    }
                })
            }
        }
    </script>
@endsection