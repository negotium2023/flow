@extends('adminlte.default')

@section('title') Progress @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        @if(auth()->user()->is('manager'))
            <div class="float-right form-inline">
                <form method="get">
                    View: &nbsp;&nbsp;<select name="p" id="dashboard_view" class="form-control form-control-sm">
                        <option value="{{$config->dashboard_process}}">Process</option>
                        <option value="0">All</option>
                        <option value="0_1">Investigation Process</option>
                        <option value="0_2">Committee Process</option>
                        <option value="0_3">Closure Process</option>
                        <option value="0_4">SLA Process</option>
                    </select>
                    <input type="hidden" name="t" value="{{\Request::get('t')}}">
                    <input type="hidden" name="f" value="{{\Request::get('f')}}">
                    <input type="hidden" name="r" value="{{\Request::get('r')}}">
                </form>
            </div>
        @endif
        <hr>
    </div>
@endsection

@section('content')
    <div class="container-fluid dashboard" id="dashboard">

        @if(\Request::get('p') != '0' && \Request::get('p') != '0_1' && \Request::get('p') != '0_2' && \Request::get('p') != '0_3' && \Request::get('p') != '0_4')
            <div id="graphs" style="{{(\Request::get('p') == '0' ? 'display:none' : 'display:block')}}">
                @php
                    $style = array('0'=>'bg-danger-gradient','1'=>'bg-warning-gradient','2'=>'bg-success-gradient','3'=>'bg-info-gradient','4'=>'bg-primary-gradient','5'=>'bg-secondary-gradient','6'=>'bg-danger-gradient','7'=>'bg-warning-gradient','8'=>'bg-success-gradient','9'=>'bg-info-gradient','10'=>'bg-primary-gradient');
                @endphp
                <div class="row mt-3 dashboard-region">
                    @for($i = 0;$i < count($regions);$i++)

                        {{-- Close the row off and start a new one for every 5 regions --}}
                        @if($i == 5)
                </div>

                <div class="row mt-3 dashboard-region">
                    <div class="col-lg col-md-6">
                        @else
                            <div class="col-lg col-md-6">
                                @endif

                                <div class="card text-white {{--@php echo $style[$i] @endphp--}} blackboard-region" style="background:{{(isset($regions[$i]['colour']) ? $regions[$i]['colour'] : '')}}">
                                    <div class="card-body">
                                        @php $id = $regions[$i]['id'] @endphp
                                        <h4><i class="fa fa-chart-line"></i> {{(!empty($client_step_counts[$id]) ? $client_step_counts[$id] : '0')}}</h4>
                                        <p class="d-inline-block">{{$regions[$i]["name"]}}</p>
                                        @if($regions[$i]['id'] == '5')
                                            <span class="float-right d-inline-block"><a href="{{route('clients.index',['step'=>$regions[$i]['id']])}}&c=no" class="btn btn-sm btn-outline-light"><i class="fa fa-share"></i> View</a></span>
                                        @else
                                            <span class="float-right d-inline-block"><a href="{{route('clients.index',['step'=>$regions[$i]['id']])}}&c=all" class="btn btn-sm btn-outline-light"><i class="fa fa-share"></i> View</a></span>
                                        @endif

                                    </div>
                                </div>
                            </div>


                            @endfor
                            <div class="col-lg col-md-6">
                                <div class="card text-white completed-region blackboard-region">
                                    <div class="card-body">

                                        <h4><i class="fa fa-chart-line"></i> {{(!empty($client_converted_count) ? $client_converted_count : '0')}}</h4>
                                        <p class="d-inline-block">Completed</p>
                                        <span class="float-right d-inline-block"><a href="{{route('clients.index',['step'=>1000])}}&f=2019-01-01&t=2019-12-31" class="btn btn-sm btn-outline-light"><i class="fa fa-share"></i> View</a></span>
                                    </div>
                                </div>
                            </div>
                    </div>

                    <div class="row pt-3">
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    Number of days to complete a client
                                    <div class="float-right">
                                        <div class="btn-group btn-group-sm btn-group-toggle" data-toggle="buttons">
                                            <label class="btn btn-secondary btn-graph active" id="type-1-column">
                                                <input type="radio" name="blackboard-dashboard-1-type"><i class="far fa-chart-bar"></i>
                                            </label>
                                            <label class="btn btn-secondary btn-graph" id="type-1-bar">
                                                <input type="radio" name="blackboard-dashboard-1-type"><i class="fa fa-align-left"></i>
                                            </label>
                                            <label class="btn btn-secondary btn-graph" id="type-1-line">
                                                <input type="radio" name="blackboard-dashboard-1-type"><i class="fa fa-chart-line"></i>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-1 pt-2 pb-2">
                                    <div id="blackboard-dashboard-1" class="m-0" style="height: 250px;"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    Completed Clients
                                    <div class="float-right">
                                        <div class="btn-group btn-group-sm btn-group-toggle" data-toggle="buttons">
                                            <label class="btn btn-secondary btn-graph" id="type-2-column">
                                                <input type="radio" id="month_filter">Month
                                            </label>
                                            <label class="btn btn-secondary btn-graph active" id="type-2-column">
                                                <input type="radio" id="week_filter">Week
                                            </label>
                                            &nbsp;
                                            <label class="btn btn-secondary btn-graph active" id="type-2-column">
                                                <input type="radio" name="blackboard-dashboard-2-type"><i class="far fa-chart-bar"></i>
                                            </label>
                                            <label class="btn btn-secondary btn-graph" id="type-2-bar">
                                                <input type="radio" name="blackboard-dashboard-2-type"><i class="fa fa-align-left"></i>
                                            </label>
                                            <label class="btn btn-secondary btn-graph" id="type-2-line">
                                                <input type="radio" name="blackboard-dashboard-2-type"><i class="fa fa-chart-line"></i>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-1 pt-2 pb-2">
                                    <div id="blackboard-dashboard-2" class="m-0" style="height: 250px;"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    Average Step Lead Time
                                    <div class="float-right">
                                        <div class="btn-group btn-group-sm btn-group-toggle" data-toggle="buttons">
                                            <label class="btn btn-secondary btn-graph active" id="type-3-column">
                                                <input type="radio" name="blackboard-dashboard-3-type"><i class="far fa-chart-bar"></i>
                                            </label>
                                            <label class="btn btn-secondary btn-graph" id="type-3-bar">
                                                <input type="radio" name="blackboard-dashboard-3-type"><i class="fa fa-align-left"></i>
                                            </label>
                                            <label class="btn btn-secondary btn-graph" id="type-3-line">
                                                <input type="radio" name="blackboard-dashboard-3-type"><i class="fa fa-chart-line"></i>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-1 pt-2 pb-2">
                                    <div id="blackboard-dashboard-3" class="m-0" style="height: 250px;"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    Outstanding Activities
                                    <div class="float-right">
                                        <div class="btn-group btn-group-sm btn-group-toggle" data-toggle="buttons">
                                            <label class="btn btn-secondary active" id="type-4-select">
                                                <div class="col-sm-8">
                                                    {{ Form::select('step_id', ['Select Step'] + $outstanding_activity_select, null, ['id'=>'step_id', 'style'=>'width:150px; font-size:1em;line-height:1em;padding:0; height:22px', 'class'=>'form-control form-control-sm']) }}
                                                </div>
                                            </label>
                                            <label class="btn btn-secondary active" id="type-4-column">
                                                <input type="radio" name="blackboard-dashboard-4-type"><i class="far fa-chart-bar"></i>
                                            </label>
                                            <label class="btn btn-secondary btn-graph" id="type-4-bar">
                                                <input type="radio" name="blackboard-dashboard-4-type"><i class="fa fa-align-left"></i>
                                            </label>
                                            <label class="btn btn-secondary  btn-graph" id="type-4-line">
                                                <input type="radio" name="blackboard-dashboard-4-type"><i class="fa fa-chart-line"></i>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-1 pt-2 pb-2">
                                    <div id="blackboard-dashboard-4" class="m-0" style="height: 250px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('extra-js')
    <script src="{!! asset('js/html2canvas.js') !!}"></script>
    @if(\Request::get('p') == '0_4')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.3/dist/Chart.min.js" integrity="sha256-R4pqcOYV8lt7snxMQO/HSbVCFRPMdrhAFMH+vr9giYI=" crossorigin="anonymous"></script>
        <script>
            var slaCanvas = document.getElementById("slaChart");

            var noStarted = {
                label: 'Not Started Yet',
                data: ['{{$mi["not_started_30_days"]}}', '{{$mi["not_started_60_days"]}}}', '{{$mi["not_started_90_days"]}}', '{{$mi["not_started_90_plus_days"]}}'],
                backgroundColor: 'rgba(64, 107, 202, 1)',
                borderWidth: 0
            };

            var inProgress = {
                label: 'In Progress',
                data: ['{{$mi["in_progress_30_days"]}}', '{{$mi["in_progress_60_days"]}}', '{{$mi["in_progress_90_days"]}}', '{{$mi["in_progress_90_plus_days"]}}'],
                backgroundColor: 'rgba(251, 119, 0, 1)',
                borderWidth: 0
            };
            var inQA = {
                label: 'In QA',
                data: ['{{$mi["in_qa_30_days"]}}', '{{$mi["in_qa_60_days"]}}', '{{$mi["in_qa_90_days"]}}', '{{$mi["in_qa_90_plus_days"]}}'],
                backgroundColor: 'rgba(165, 165, 165, 1)',
                borderWidth: 0
            };

            var inExitFinalisation = {
                label: 'In Exit Finalisation',
                data: ['{{$mi["exit_closeout_30_days"]}}', '{{$mi["exit_closeout_60_days"]}}', '{{$mi["exit_closeout_90_days"]}}', '{{$mi["exit_closeout_90_plus_days"]}}'],
                backgroundColor: 'rgba(255, 194, 0, 1)',
                borderWidth: 0
            };

            var slaData = {
                labels: ["<30 Dasy", "30-60 Days", "60-90 Days", ">90 Days"],
                datasets: [noStarted, inProgress, inQA, inExitFinalisation]
            };

            var slaOptions = {
                maintainAspectRatio: false,
                scales: {
                    xAxes: [{
                        gridLines: {
                            display:false
                        },
                        ticks: {
                            autoSkip: false
                        }
                    }],
                    yAxes: []
                },
                legend: {
                    position: 'bottom',
                }
            };

            var barChart = new Chart(slaCanvas, {
                type: 'bar',
                data: slaData,
                options: slaOptions
            });
        </script>
    @endif
    <script>

        var $ul = $("ul");

        var ulWidth = $('#dashboard').width();
        var count = 0;

        var ulWidth2 = ulWidth / 2;

        $(function() {





            //here is the hero, after the capture button is clicked
            //he will take the screen shot of the div and save it as image.
            $('#view_org').click(function(){

                if($('#sla').is(':visible')){

                    var dashWidth = $('#dashboard').width();
                    let miname = 'consolodated';
                    var useWidth = $('#sla').prop('scrollWidth'); //document.getElementById("primary").style.width;
                    var useHeight = $('#dashboard').prop('scrollHeight'); //document.getElementById("primary").style.height;

                    $('#dashboard').css('width',useWidth);
                    $('#dashboard').css('height',useHeight);
                    $('#sla').css('overflow-x','visible');
                    $('#sla').css('overflow-y','visible');

                    var slWidth = $('.main-sidebar').width();

                    $('.main-wrapper').css('margin-left', 0);
                    $('#dashboard').css('padding-left', slWidth);
                    $('h4').css('margin-left', slWidth);
                    $('#overlay').fadeIn();
                    $('#view_org').hide();

                    //make it as html5 canvas
                    div_content = document.querySelector("#dashboard")
                    /*html2canvas(div_content).then(function(c){
                        c.id = 'htmlcanvas';
                        document.body.appendChild(c);
                    })
                    $('#tree').hide();*/
                    /*div_content2 = document.querySelector("#htmlcanvas")*/
                    html2canvas(div_content,{width: useWidth, height: useHeight}).then(function (canvas) {
                        //data = canvas.toDataURL('image/jpeg');

                        //then call a super hero php to save the image
                        //save_img(data);
                        let a = document.createElement('a');
                        a.href = canvas.toDataURL();
                        a.download = miname + '-mi.png';
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);

                        $('.main-wrapper').css('margin-left', slWidth);
                        $('#dashboard').css('padding-left', '0');
                        $('#dashboard').css('padding-right', '0');
                        $('h4').css('margin-left', '0');

                        $('#dashboard').css('width',dashWidth);
                        $('#sla').css('overflow-x','scroll');
                        $('#sla').css('overflow-y','hidden');

                        $('#overlay').fadeOut();
                        $('#view_org').show();
                        /*$el.css('padding-left','7.5px');*/
                        /*html2canvas(div_content,{
                            onrendered: function (canvas) {
                                let a = document.createElement('a');
                                a.href = canvas.toDataURL();
                                a.download = 'organogram.png';
                                document.body.appendChild(a);
                                a.click();
                                document.body.removeChild(a);
                            }*/
                    });

                } else {
                    var $el = $("#dashboard");
                    var elHeight = $el.outerHeight();
                    var elWidth = $el.width();
                    var slWidth = $('.main-sidebar').width();

                    $('.main-wrapper').css('margin-left', 0);
                    $('.main-wrapper').css('padding-left', slWidth);
                    $('#investigation').css('width', elWidth);

                    //get the div content
                    $('#view_org').hide();
                    $('#overlay').fadeIn();

                    let miname = '';

                    if ($('#investigation').is(':visible')) {
                        miname = 'investigation';
                    }

                    if ($('#committee').is(':visible')) {
                        miname = 'committee';
                    }

                    if ($('#sla_r').is(':visible')) {
                        miname = 'sla_r';
                    }

                    if ($('#closure').is(':visible')) {
                        miname = 'closure';
                    }

                    //make it as html5 canvas
                    div_content = document.querySelector("#dashboard")
                    /*html2canvas(div_content).then(function(c){
                        c.id = 'htmlcanvas';
                        document.body.appendChild(c);
                    })
                    $('#tree').hide();*/
                    /*div_content2 = document.querySelector("#htmlcanvas")*/
                    html2canvas(div_content).then(function (canvas) {
                        //data = canvas.toDataURL('image/jpeg');

                        //then call a super hero php to save the image
                        //save_img(data);
                        let a = document.createElement('a');
                        a.href = canvas.toDataURL();
                        a.download = miname + '-mi.png';
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        $('#overlay').fadeOut();
                        $('#view_org').show();
                        /*$el.css('padding-left','7.5px');*/
                        /*html2canvas(div_content,{
                            onrendered: function (canvas) {
                                let a = document.createElement('a');
                                a.href = canvas.toDataURL();
                                a.download = 'organogram.png';
                                document.body.appendChild(a);
                                a.click();
                                document.body.removeChild(a);
                            }*/
                    });
                }
            });

        });

    </script>
    <script>

        $(window).on('load', function(){
            setTimeout(removeLoader, 2); //wait for page load PLUS two seconds.
        });
        function removeLoader(){
            $('#overlay').fadeOut();
        }


        $('#dashboard_view').on('change', function () {
            $('#overlay').fadeIn();
            $(this).closest('form').submit();
            /*if($('#dashboard_view').val() === '1') {
                $('#mi').show();
                $('#graphs').hide();
            }
            else {
                $('#mi').hide();
                $('#graphs').show();
            }*/
        });

        $(document).ready(function() {

            if($("#sla").is(':visible')){
                $('#dashboard_view').val('0');
            }
            if($("#investigation").is(':visible')){
                $('#dashboard_view').val('0_1');
            }
            if($("#committee").is(':visible')){
                $('#dashboard_view').val('0_2');
            }
            if($("#closure").is(':visible')){
                $('#dashboard_view').val('0_3');
            }
            if($("#sla_r").is(':visible')){
                $('#dashboard_view').val('0_4');
            }


            // Build the chart
            Highcharts.theme = {
                colors: ['#86bffd', '#17a2b8'],
                title: {
                    text: ''
                },
                chart: {
                    type: 'column'
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: ''
                    }
                },
                xAxis: {
                    crosshair: true
                },
                credits: {
                    enabled: false
                },
                legend: {
                    enabled: false
                },
            };

            Highcharts.setOptions(Highcharts.theme);

                    @if(\Request::get('p') != '0' && \Request::get('p') != '0_1' && \Request::get('p') != '0_2' && \Request::get('p') != '0_3' && \Request::get('p') != '0_4')
            let blackboardDashboard1 = Highcharts.chart('blackboard-dashboard-1', {
                    yAxis: {
                        labels: {
                            formatter: function (x) {
                                return (this.value) + " days";
                            }
                        },
                        plotLines: [{
                            color: '#f6918c',
                            width: 2,
                            value: {{$config->onboard_days}},
                            dashStyle: 'shortdash',
                            zIndex: 5
                        }]
                    },

                    xAxis: {
                        categories: ['Min', 'Avg', 'Max'],
                    },
                    tooltip: {
                        formatter: function () {
                            return '<small class="text-muted">' + this.x + '</small><br><b>' + this.y + ' days</b>';
                        }
                    },
                    series: [{
                        data: [{{$client_onboard_times["minimum"]}}, {{$client_onboard_times["average"]}}, {{$client_onboard_times["maximum"]}}]
                    }],
                    plotOptions: {
                        series: {
                            cursor: 'pointer',
                            borderRadiusTopLeft: '3px',
                            borderRadiusTopRight: '3px',
                            point: {
                                events: {
                                    click: function () {
                                        switch (this.category) {
                                            case 'Min':
                                                location.href = '{!! route('clients.index',['l'=>request()->input('l'),'p'=>request()->input('p'),'f'=>request()->input('f'),'t'=>request()->input('t'),'c'=>'yes','si'=>'completed_days','so'=>'a']) !!}';
                                                break;
                                            case 'Avg':
                                                location.href = '{!! route('clients.index',['l'=>request()->input('l'),'p'=>request()->input('p'),'f'=>request()->input('f'),'t'=>request()->input('t'),'c'=>'yes','si'=>'completed_days','so'=>'a']) !!}';
                                                break;
                                            case 'Max':
                                                location.href = '{!! route('clients.index',['l'=>request()->input('l'),'p'=>request()->input('p'),'f'=>request()->input('f'),'t'=>request()->input('t'),'c'=>'yes','si'=>'completed_days','so'=>'d']) !!}';
                                                break;
                                        }
                                    }
                                }
                            }
                        }
                    },
                });

            let blackboardDashboard2 = Highcharts.chart('blackboard-dashboard-2', {
                yAxis: {
                    labels: {
                        formatter: function (x) {
                            return (this.value) + " clients";
                        }
                    },
                    plotLines: [{
                        color: '#dc3545',
                        width: 2,
                        value: {{$config->onboards_per_day}},
                        dashStyle: 'shortdash',
                        zIndex: 5
                    }]
                },
                xAxis: {
                    type: 'category',
                    categories: [
                        @foreach($client_onboards as $key => $client)
                            '{{$key}}',
                        @endforeach
                    ]
                },
                tooltip: {
                    formatter: function () {
                        return '<small class="text-muted">' + this.x + '</small><br><b>' + this.y + ' clients</b>';
                    }
                },
                series: [{
                    data: [
                        @foreach($client_onboards as $client)
                        {{$client}},
                        @endforeach
                    ]
                }],
                plotOptions: {
                    series: {
                        cursor: 'pointer',
                        borderRadiusTopLeft: '3px',
                        borderRadiusTopRight: '3px',
                        point: {
                            events: {
                                click: function () {
                                    let start = '';
                                    switch ('{{request()->input('r')}}') {
                                        case 'day':
                                            start = moment(this.category, 'DD MMMM YYYY');
                                            location.href = '{!! route('clients.index',['l'=>request()->input('l'),'p'=>request()->input('p'),'c'=>'yes']) !!}&f=' + start.format('YYYY-MM-DD') + '&t=' + start.format('YYYY-MM-DD');
                                            break;
                                        case 'week':
                                            start = moment(moment(this.category).format('WW YYYY'), 'WW YYYY');
                                            location.href = '{!! route('clients.index',['l'=>request()->input('l'),'p'=>request()->input('p'),'c'=>'yes']) !!}&f=' + start.format('YYYY-MM-DD') + '&t=' + start.add(6, 'days').format('YYYY-MM-DD');
                                            break;
                                        case 'month':
                                            start = moment(this.category, 'MMMM YYYY');
                                            location.href = '{!! route('clients.index',['l'=>request()->input('l'),'p'=>request()->input('p'),'c'=>'yes']) !!}&f=' + start.format('YYYY-MM-DD') + '&t=' + start.add(1, 'months').format('YYYY-MM-DD');
                                            break;
                                        case 'year':
                                            start = moment(this.category, 'YYYY');
                                            location.href = '{!! route('clients.index',['l'=>request()->input('l'),'p'=>request()->input('p'),'c'=>'yes']) !!}&f=' + start.format('YYYY-MM-DD') + '&t=' + start.add(1, 'years').format('YYYY-MM-DD');
                                            break;
                                    }
                                }
                            }
                        }
                    }
                }
            });

            let blackboardDashboard3 = Highcharts.chart('blackboard-dashboard-3', {
                yAxis: {
                    labels: {
                        formatter: function (x) {
                            return (this.value) + " days";
                        }
                    },
                },
                xAxis: {
                    categories: [
                        @foreach($process_average_times as $name => $step)
                            '{{$name}}',
                        @endforeach
                    ]
                },
                tooltip: {
                    formatter: function () {
                        return '<small class="text-muted">' + this.x + '</small><br><b>' + this.y + ' days</b>';
                    }
                },
                series: [{
                    data: [
                        @foreach($process_average_times as $name => $step)
                        {{$step}},
                        @endforeach
                    ]
                }],
                plotOptions: {
                    series: {
                        borderRadiusTopLeft: '3px',
                        borderRadiusTopRight: '3px'
                    }
                }
            });

            let blackboardDashboard4 = Highcharts.chart('blackboard-dashboard-4', {
                yAxis: [
                    {
                        title: {},
                        style: {
                            color: Highcharts.getOptions().colors[0]
                        }
                    },
                    {
                        title: {},
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        },
                        opposite: true
                    }
                ],
                xAxis: {
                    type: 'category',
                    categories: [
                        @foreach($process_outstanding_activities as $name => $amount)
                            '{{$name}}',
                        @endforeach
                    ]
                },
                series: [
                    {
                        name: 'Outstanding Activities',
                        data: [
                            @foreach($process_outstanding_activities as $amount)
                            {{$amount['user']}},
                            @endforeach
                        ]
                    },
                    /*{
                        name: 'Client Activities',
                        data: [
{{--@foreach($process_outstanding_activities as $amount)
                        {{$amount['client']}},
                        @endforeach--}}
                    ]
                }*/
                ],
                tooltip: {
                    shared: true
                },
                plotOptions: {
                    series: {
                        cursor: 'pointer',
                        borderRadiusTopLeft: '3px',
                        borderRadiusTopRight: '3px',
                        point: {
                            events: {
                                click: function () {
                                    location.href = '{!! route('clients.index',['l'=>request()->input('l'),'p'=>request()->input('p')]) !!}&oa='+this.category+'&s=all&c=all&step={!! $config->dashboard_outstanding_step !!}';
                                }
                            }
                        }
                    }
                }
            });

            $('#type-1-bar').click(function () {
                blackboardDashboard1.update({
                    chart: {
                        type: 'bar'
                    }
                });
            });

            $('#type-1-line').click(function () {
                blackboardDashboard1.update({
                    chart: {
                        type: 'line'
                    }
                });
            });

            $('#type-1-column').click(function () {
                blackboardDashboard1.update({
                    chart: {
                        type: 'column'
                    }
                });
            });

            $('#type-2-bar').click(function () {
                blackboardDashboard2.update({
                    chart: {
                        type: 'bar'
                    }
                });
            });

            $('#type-2-line').click(function () {
                blackboardDashboard2.update({
                    chart: {
                        type: 'line'
                    }
                });
            });

            $('#type-2-column').click(function () {
                blackboardDashboard2.update({
                    chart: {
                        type: 'column'
                    }
                });
            });

            $('#type-3-bar').click(function () {
                blackboardDashboard3.update({
                    chart: {
                        type: 'bar'
                    }
                });
            });

            $('#type-3-line').click(function () {
                blackboardDashboard3.update({
                    chart: {
                        type: 'line'
                    }
                });
            });

            $('#type-3-column').click(function () {
                blackboardDashboard3.update({
                    chart: {
                        type: 'column'
                    }
                });
            });

            $('#type-4-bar').click(function () {

                blackboardDashboard4.update({
                    chart: {
                        type: 'bar',
                    },
                });
            });

            $('#type-4-line').click(function () {
                blackboardDashboard4.update({
                    chart: {
                        type: 'line'
                    }
                });
            });

            $('#type-4-column').click(function () {
                blackboardDashboard4.update({
                    chart: {
                        type: 'column'
                    }
                });
            });

            // OUTSTANDING ACTIVITIES
            // Change graph dependent on select change
            $('#step_id').on('change', function () {
                $(function() {

                    let ajaxCategories = [];
                    let ajaxValues = [];
                    let params = {
                        'process_id': '{!! request()->input('p') !!}',
                        'step_id': $('#step_id').val()
                    }

                    // Fire an ajax call
                    $.getJSON('graphs/getoutstandingactivitiesajax', params, function(data){
                        // Break apart the json returned into categories and series values
                        $.each(data, function(name, values) {
                            ajaxCategories.push(name);
                            ajaxValues.push(values.user);
                        });

                        // Update the categories
                        blackboardDashboard4.update({
                            xAxis: {
                                type: 'category',
                                categories: ajaxCategories
                            },
                        });

                        // Set the series
                        blackboardDashboard4.xAxis[0].setCategories(ajaxCategories);
                        blackboardDashboard4.series[0].setData(ajaxValues);
                    });
                });
            });

            // COMPLETED CLIENTS
            // Change graph dependent on button click
            $('#month_filter').on('click', function () {

                $('#month_filter').addClass('active');
                $('#week_filter').removeClass('active');

                $(function() {

                    let ajaxCategories = [];
                    let ajaxValues = [];
                    let params = {
                        'process_id': '{!! request()->input('p') !!}',
                        'from': '{!! $from !!}',
                        'to': '{!! $to !!}',
                        'range': 'month'
                    }

                    // Fire an ajax call
                    $.getJSON('graphs/getcompletedclientsajax', params, function(data){
                        // Break apart the json returned into categories and series values
                        $.each(data, function(name, value) {
                            ajaxCategories.push(name);
                            ajaxValues.push(value);
                        });

                        console.log(ajaxCategories);
                        console.log(ajaxValues);

                        // Update the categories
                        blackboardDashboard2.update({
                            xAxis: {
                                type: 'category',
                                categories: ajaxCategories
                            },
                        });

                        // Set the series
                        blackboardDashboard2.series[0].setData(ajaxValues);
                    });
                });
            });

            // Change graph dependent on button click
            $('#week_filter').on('click', function () {

                $('#week_filter').addClass('active');
                $('#month_filter').removeClass('active');

                $(function() {

                    let ajaxCategories = [];
                    let ajaxValues = [];
                    let params = {
                        'process_id': '{!! request()->input('p') !!}',
                        'from': '{!! $from !!}',
                        'to': '{!! $to !!}',
                        'range': 'week'
                    }

                    // Fire an ajax call
                    $.getJSON('graphs/getcompletedclientsajax', params, function(data){
                        // Break apart the json returned into categories and series values
                        $.each(data, function(name, value) {
                            ajaxCategories.push(name);
                            ajaxValues.push(value);
                        });

                        console.log(ajaxCategories);
                        console.log(ajaxValues);

                        // Update the categories
                        blackboardDashboard2.update({
                            xAxis: {
                                type: 'category',
                                categories: ajaxCategories
                            },
                        });

                        // Set the series
                        blackboardDashboard2.series[0].setData(ajaxValues);
                    });
                });
            });

            @endif
        })
    </script>


@endsection