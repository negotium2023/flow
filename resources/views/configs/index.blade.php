@extends('flow.default')

@section('title') Configs @endsection

@section('header')
    <div class="container-fluid container-title">
        <div class="row">
            <div class="col-md-10"><h3>@yield('title')</h3></div>
            <div class="col-md-2">
                <a href="{{route('theme.create')}}" class="btn btn-sm btn-dark float-lg-right"><i class="fas fa-palette"></i> Custom Colours</a>
            </div>
        </div>        
    </div>
@endsection

@section('content')
    <div class="content-container page-content">
        <div class="row col-md-12 h-100 pr-0">
            @yield('header')
            <div class="container-fluid index-container-content">
                <div class="table-responsive h-100">
                  {{Form::open(['url' => route('configs.update'), 'method' => 'put'])}}
                    <ul class="accordion">
                        <li>
                            <a class="toggle" href="javascript:void(0);">System</a>
                            <ul class="inner show">
                                <li>

                                    <div class="form-group">
                                        {{Form::label('onboard_days', 'Target to onboard a client')}}
                                        <div class="input-group input-group-sm" style="width:99%">
                                            {{Form::number('onboard_days', $config->onboard_days, ['class'=>'form-control form-control-sm','placeholder'=>'Enter selection'])}}
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Days</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {{Form::label('onboards_per_day', 'Target onboarded clients')}}
                                        <div class="input-group input-group-sm" style="width:99%">
                                            {{Form::number('onboards_per_day', $config->onboards_per_day, ['class'=>'form-control form-control-sm','placeholder'=>'Enter selection'])}}
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Per day</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {{Form::label('client_target_data', 'Client Target Data')}}
                                        <div class="input-group input-group-sm">
                                            {{Form::number('client_target_data', $config->client_target_data, ['class'=>'form-control form-control-sm','placeholder'=>'Enter selection'])}}
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {{Form::label('client_converted', 'Client Converted')}}
                                        <div class="input-group" style="width:99%">
                                            {{Form::number('client_converted', $config->client_converted, ['class'=>'form-control form-control-sm','placeholder'=>'Enter selection'])}}
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {{Form::label('client_conversion', 'Client Conversion')}}
                                        <div class="input-group" style="width:99%">
                                            {{Form::number('client_conversion', $config->client_conversion, ['class'=>'form-control form-control-sm','placeholder'=>'Enter selection'])}}
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {{Form::label('action_threshold', 'Threshold for Actions')}}
                                        <div class="input-group input-group-sm" style="width:99%">
                                            {{Form::number('action_threshold', $config->action_threshold, ['class'=>'form-control form-control-sm','placeholder'=>'Enter selection'])}}
                                            <div class="input-group-sm input-group-prepend">
                                                <span class="input-group-sm input-group-text">Days</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {{Form::label('default_onboarding_process', 'Default Onboarding Process')}}
                                        <div class="input-group" style="width:99%">
                                            {{Form::select('default_onboarding_process',$process, ($config->default_onboarding_process ? $config->default_onboarding_process : '0'), ['class'=>'form-control form-control-sm'])}}
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {{Form::label('related_party_process', 'Related Party Process')}}
                                        <div class="input-group" style="width:99%">
                                            {{Form::select('related_party_process',$related_party_process, ($config->related_party_process ? $config->related_party_process : '1'), ['class'=>'form-control form-control-sm'])}}
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {{Form::label('message_subject', 'Enable message subject')}}
                                        <div class="input-group" style="width:99%">
                                            {{Form::select('message_subject',[1=>'Yes',0=>'No'], $config->message_subject, ['class'=>'form-control form-control-sm'])}}
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {{Form::label('enable_support', 'Enable support help')}}
                                        <div class="input-group" style="width:99%">
                                            {{Form::select('enable_support',[1=>'Yes',0=>'No'], $config->enable_support, ['class'=>'form-control form-control-sm'])}}
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {{Form::label('support_email', 'Support mail address')}}
                                        <div class="input-group" style="width:99%">
                                            {{Form::email('support_email', $config->support_email, ['class'=>'form-control form-control-sm'])}}
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {{Form::label('allowed_domain', 'Allowed email domains')}}
                                        <div class="input-group" style="width:99%">
                                            {{Form::email('allowed_domain', $config->allowed_email_domains, ['class'=>'form-control form-control-sm','placeholder'=>'example.com,example2.com'])}}
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {{Form::label('absolute_path', 'Absolute Path')}}
                                        <div class="input-group" style="width:99%">
                                            {{Form::text('absolute_path', $config->absolute_path, ['class'=>'form-control form-control-sm','placeholder'=>'/usr/home/blackhhbsf/demo_src/'])}}
                                        </div>
                                    </div>

                                </li>
                            </ul>
                        </li>

                        <li>
                            <a class="toggle" href="javascript:void(0);">Dashboard</a>
                            <ul class="inner">
                                <li>
                                    <div class="form-group">
                                        {{Form::label('dashboard_process', 'Dashboard Process')}}
                                        <div class="input-group" style="width:99%">
                                            {{Form::select('dashboard_process',$process, ($config->dashboard_process ? $config->dashboard_process : '1'), ['class'=>'form-control form-control-sm','id'=>'process'])}}
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {{Form::label('dashboard_regions', 'Dashboard Regions')}}
                                        <div class="input-group" style="width:99%">
                                            {{Form::select('dashboard_regions[]',$steps, ($config->dashboard_regions ? $config->dashboard_regions : '0'), ['class'=>'form-control form-control-sm','id'=>'dashboard_steps','multiple'])}}
                                            @foreach($errors->get('dashboard_regions') as $error)
                                                <div class="invalid-feedback">
                                                    {{ $error }}
                                                </div>
                                            @endforeach

                                        </div><small class="form-text text-muted">
                                            Hold <kbd>Ctrl</kbd> to select multiple entries
                                        </small>
                                    </div>

                                    <div class="form-group">
                                        {{Form::label('dashboard_avg_step_lead', 'Steps to display for Average Step Lead Time')}}
                                        <div class="input-group" style="width:99%">
                                            {{Form::select('dashboard_avg_step_lead[]',$steps, null, ['class'=>'form-control form-control-sm','id'=>'dashboard_avg_step_lead','multiple'])}}
                                            @foreach($errors->get('dashboard_avg_step_lead') as $error)
                                                <div class="invalid-feedback">
                                                    {{ $error }}
                                                </div>
                                            @endforeach

                                        </div><small class="form-text text-muted">
                                            Hold <kbd>Ctrl</kbd> to select multiple entries
                                        </small>
                                    </div>

                                    <div class="form-group">
                                        {{Form::label('outstanding_activities', 'Step to use for Outstanding Activities')}}
                                        <div class="input-group" style="width:99%">
                                            {{Form::select('outstanding_step',$steps, ($config->dashboard_outstanding_step ? $config->dashboard_outstanding_step : '0'), ['class'=>'form-control form-control-sm','id'=>'outstanding_step'])}}
                                            @foreach($errors->get('outstanding_step') as $error)
                                                <div class="invalid-feedback">
                                                    {{ $error }}
                                                </div>
                                            @endforeach

                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {{Form::label('outstanding_activities', 'Outstanding Activities to show')}}
                                        <div class="input-group" style="width:99%">
                                            {{Form::select('outstanding_activities[]',$steps, ($config->outstanding_activities ? $config->outstanding_activities : '0'), ['class'=>'form-control form-control-sm','id'=>'outstanding_activities','multiple'])}}
                                            @foreach($errors->get('outstanding_activities') as $error)
                                                <div class="invalid-feedback">
                                                    {{ $error }}
                                                </div>
                                            @endforeach

                                        </div><small class="form-text text-muted">
                                            Hold <kbd>Ctrl</kbd> to select multiple entries
                                        </small>
                                    </div>

                                    <div class="form-group">
                                        {{Form::label('dashboard_activities_step_for_age', 'Activity Step to calculate ageing from.')}}
                                        <div class="input-group" style="width:99%">
                                            {{Form::select('dashboard_activities_step_for_age',$steps, ($config->dashboard_activities_step_for_age ? $config->dashboard_activities_step_for_age : '0'), ['class'=>'form-control form-control-sm','id'=>'dashboard_activities_step_for_age'])}}
                                            @foreach($errors->get('outstanding_activities') as $error)
                                                <div class="invalid-feedback">
                                                    {{ $error }}
                                                </div>
                                            @endforeach

                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>

                    <div class="blackboard-fab mr-3 mb-3">
                        <button type="submit" class="btn btn-info btn-lg"><i class="fa fa-save"></i> Save</button>
                    </div>

                    {{Form::close()}}
                </div>
            </div>
        </div>
    </div>

@endsection
@section('extra-css')
    <style>


    </style>
@endsection
@section('extra-js')
    <script>
        $('.toggle').click(function(e) {
            e.preventDefault();

            var $this = $(this);

            if ($this.next().hasClass('show')) {
                $this.next().removeClass('show');
                /*$this.next().slideUp(350);*/
            } else {
                $this.parent().parent().find('li .inner').removeClass('show');
                /*$this.parent().parent().find('li .inner').slideUp(350);*/
                $this.next().toggleClass('show');
                /*$this.next().slideToggle(350);*/
            }
        });

        $(document).ready(function(){

            getStepData();

            function getStepData() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    dataType: 'json',
                    url: '/get_process_steps/'+$('#process').val(),
                    type:'GET',
                    data: {process_id:$('#process').val()}
                }).done(function(data) {
                    let rows = '';
                    $.each( data, function( key, value ) {
                        if(value.selected === '1') {
                            rows = rows + "<option value=" + value.id + " selected>" + value.name + "</option>";
                        } else {
                            rows = rows + "<option value=" + value.id + ">" + value.name + "</option>";
                        }
                    });
                    //alert(rows);
                    $("#dashboard_steps").html(rows);
                });

                $.ajax({
                    dataType: 'json',
                    url: '/get_process_avg_steps/'+$('#process').val(),
                    type:'GET',
                    data: {process_id:$('#process').val()}
                }).done(function(data) {
                    let rows = '';
                    $.each( data, function( key, value ) {
                        if(value.selected === '1') {
                            rows = rows + "<option value=" + value.id + " selected>" + value.name + "</option>";
                        } else {
                            rows = rows + "<option value=" + value.id + ">" + value.name + "</option>";
                        }
                    });
                    //alert(rows);
                    $("#dashboard_avg_step_lead").html(rows);
                });

                $.ajax({
                    dataType: 'json',
                    url: '/get_process_steps_for_ageing/'+$('#process').val(),
                    type:'GET',
                    data: {process_id:$('#process').val()}
                }).done(function(data) {
                    let rows = '';
                    $.each( data, function( key, value ) {
                        if(value.selected === '1') {
                            rows = rows + "<option value=" + value.id + " selected>" + value.name + "</option>";
                        } else {
                            rows = rows + "<option value=" + value.id + ">" + value.name + "</option>";
                        }
                    });
                    //alert(rows);
                    $("#dashboard_activities_step_for_age").html(rows);
                });

                $.ajax({
                    dataType: 'json',
                    url: '/get_outstanding_step/'+$('#process').val(),
                    type:'GET',
                    data: {process_id:$('#process').val()}
                }).done(function(data) {
                    let rows = '';
                    $.each( data, function( key, value ) {
                        if(value.selected === '1') {
                            rows = rows + "<option value=" + value.id + " selected>" + value.name + "</option>";
                        } else {
                            rows = rows + "<option value=" + value.id + ">" + value.name + "</option>";
                        }
                    });
                    //alert(rows);
                    $("#outstanding_step").html(rows);

                    $.ajax({
                        dataType: 'json',
                        url: '/get_outstanding_activities/'+$('#outstanding_step').val(),
                        type:'GET',
                        data: {step_id:$('#outstanding_step').val()}
                    }).done(function(data) {
                        let rows = '';
                        $.each( data, function( key, value ) {
                            if(value.selected === '1') {
                                rows = rows + "<option value=" + value.id + " selected>" + value.name + "</option>";
                            } else {
                                rows = rows + "<option value=" + value.id + ">" + value.name + "</option>";
                            }
                        });
                        //alert(rows);
                        $("#outstanding_activities").html(rows);
                    });
                });
            }

            $("#dashboard_steps").click(function(){
                let count = 0;

                count = $("#dashboard_steps").val().length;
                if(count > 9) {
                    alert("You can only select 9 Process Steps to display on the dahboard. Please select 1 to remove.");
                }
            })

            $("#outstanding_step").on("change",function(){
                $.ajax({
                    dataType: 'json',
                    url: '/get_outstanding_activities/'+$('#outstanding_step').val(),
                    type:'GET',
                    data: {step_id:$('#outstanding_step').val()}
                }).done(function(data) {
                    let rows = '';
                    $.each( data, function( key, value ) {
                        if(value.selected === '1') {
                            rows = rows + "<option value=" + value.id + " selected>" + value.name + "</option>";
                        } else {
                            rows = rows + "<option value=" + value.id + ">" + value.name + "</option>";
                        }
                    });
                    //alert(rows);
                    $("#outstanding_activities").html(rows);
                });
            });

            $("#process").on('change',function(){
               getStepData();
            });
        })
    </script>
@endsection