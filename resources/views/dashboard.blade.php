@extends('flow.default')

@section('title') Dashboard @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <div style="position: absolute;right:0px;top:20px;">
            <form method="get" class="form-inline mt-0">
                {{Form::select('p',$processes,old('p'),['class'=>'form-control form-control-sm','placeholder'=>'Please select','id'=>'dashboard_process'])}}
                {{-- <button type="submit" class="btn btn-sm btn-secondary ml-2 mr-2"><i class="fa fa-search"></i> Search</button> --}}
            </form>
        </div>
    </div>
@endsection

@section('content')
    <div class="content-container page-content">
        <div class="row col-md-12 h-100">
            <div class="container-fluid container-title">
                <h3>@yield('header')</h3>
            </div>
            <div class="container-fluid container-content dashboard" style="overflow: auto;overflow-x: hidden;">
                @php
                    $style = array('0'=>'bg-danger-gradient','1'=>'bg-warning-gradient','2'=>'bg-success-gradient','3'=>'bg-info-gradient','4'=>'bg-primary-gradient','5'=>'bg-secondary-gradient','6'=>'bg-danger-gradient','7'=>'bg-warning-gradient','8'=>'bg-success-gradient','9'=>'bg-info-gradient','10'=>'bg-primary-gradient');
                @endphp
                <div class="row mt-1 dashboard-region">
                    @for($i = 0;$i < count($regions);$i++)

                        {{-- Close the row off and start a new one for every 5 regions --}}
                        @if($i == 5)
                </div>
                <div class="row mt-1 dashboard-region">
                    <div class="col-lg col-md-6">
                        @else
                            <div class="col-lg col-md-6">
                                @endif

                                <div class="card text-white {{--@php echo $style[$i] @endphp--}} blackboard-region" style="background:{{(isset($regions[$i]['colour']) ? $regions[$i]['colour'] : '')}}">
                                    <div class="card-body" style="padding: 0.75rem;">
                                        @php $id = $regions[$i]['id'] @endphp
                                        <h4><i class="fa fa-chart-line"></i> {{(!empty($client_step_counts[$id]) ? $client_step_counts[$id] : '0')}}</h4>
                                        <p class="d-inline-block w-100">{{$regions[$i]["name"]}}</p>
                                        @if($regions[$i]['id'] == '5')
                                            <span class="float-right d-inline-block"><a href="{{route('clients.index',['p'=>$regions[$i]['process_id'],'step'=>$regions[$i]['id']])}}&c=no" class="btn btn-sm btn-outline-light"><i class="fa fa-share"></i> View</a></span>
                                        @else
                                            <span class="float-right d-inline-block"><a href="{{route('clients.index',['p'=>$regions[$i]['process_id'],'step'=>$regions[$i]['id']])}}&c=no" class="btn btn-sm btn-outline-light"><i class="fa fa-share"></i> View</a></span>
                                        @endif

                                    </div>
                                </div>
                            </div>


                            @endfor
                            @if($client_converted_count != '-1')
                                <div class="col-lg col-md-6">
                                    <div class="card text-white completed-region blackboard-region">
                                        <div class="card-body" style="padding: 0.75rem;">

                                            <h4><i class="fa fa-chart-line"></i> {{(!empty($client_converted_count) ? $client_converted_count : '0')}}</h4>
                                            <p class="d-inline-block w-100">Completed</p>
                                            @if($config->show_converted_currency_total != null && $config->show_converted_currency_total == '1')
                                                <p class="d-inline-block">{{$converted_value}}</p>
                                            @endif
                                            <span class="float-right d-inline-block"><a href="{{route('clients.index',['step'=>1000])}}&p={{$process_id}}&f=2022-01-01&t=2022-12-31" class="btn btn-sm btn-outline-light"><i class="fa fa-share"></i> View</a></span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                    </div>

                    <div class="row pt-1">
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    Converted Clients
                                    <div class="float-right">
                                        <div class="btn-group btn-group-sm btn-group-toggle">
                                            {{-- <label class="btn btn-secondary btn-graph" id="type-4-select">
                                            <div class="col-sm-8">
                                                {{ Form::select('completed_clients', ['current'=>'Current','all'=>'All','3'=>'Past 3 months','6'=>'Past 6 months','9'=>'Past 9 months','12'=>'Past 12 months'], null, ['id'=>'completed_clients', 'style'=>'width:150px; font-size:1em;line-height:1em;padding:0; height:22px', 'class'=>'form-control form-control-sm']) }}
                                            </div></label> --}}
                                        </div>
                                        <div class="btn-group btn-group-sm btn-group-toggle" data-toggle="buttons">
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
                                    Outstanding Activities for {{$outstanding_activity_name}}
                                    <div class="float-right">
                                        <div class="btn-group btn-group-sm btn-group-toggle" data-toggle="buttons">
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

                        @if(\Request::get('p') == '7' || \Request::get('p') == '8')
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header">
                                        Outstanding Activities for Escalated to Priority
                                        <div class="float-right">
                                            <div class="btn-group btn-group-sm btn-group-toggle" data-toggle="buttons">
                                                <label class="btn btn-secondary active" id="type-4-column">
                                                    <input type="radio" name="blackboard-dashboard-5-type"><i class="far fa-chart-bar"></i>
                                                </label>
                                                <label class="btn btn-secondary btn-graph" id="type-4-bar">
                                                    <input type="radio" name="blackboard-dashboard-5-type"><i class="fa fa-align-left"></i>
                                                </label>
                                                <label class="btn btn-secondary  btn-graph" id="type-4-line">
                                                    <input type="radio" name="blackboard-dashboard-5-type"><i class="fa fa-chart-line"></i>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body p-1 pt-2 pb-2">
                                        <div id="blackboard-dashboard-5" class="m-0" style="height: 250px;"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header">
                                        Outstanding Activities for CCH Priority Setup
                                        <div class="float-right">
                                            <div class="btn-group btn-group-sm btn-group-toggle" data-toggle="buttons">
                                                <label class="btn btn-secondary active" id="type-4-column">
                                                    <input type="radio" name="blackboard-dashboard-6-type"><i class="far fa-chart-bar"></i>
                                                </label>
                                                <label class="btn btn-secondary btn-graph" id="type-4-bar">
                                                    <input type="radio" name="blackboard-dashboard-6-type"><i class="fa fa-align-left"></i>
                                                </label>
                                                <label class="btn btn-secondary  btn-graph" id="type-4-line">
                                                    <input type="radio" name="blackboard-dashboard-6-type"><i class="fa fa-chart-line"></i>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body p-1 pt-2 pb-2">
                                        <div id="blackboard-dashboard-6" class="m-0" style="height: 250px;"></div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
            </div>
            </div>
        </div>

        @endsection

        @section('extra-js')
            <script>

$(document).ready(function() {
            $('#dashboard_process').on('change',function (){
                this.form.submit();
            });

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
                        @if($client > 0)
                        {{$client}},
                        @endif
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
                                @if($amount['user'] > 0)
                                {{$amount['user']}},
                                @endif
                            @endforeach
                        ]
                    },
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
                                    location.href = '{!! route('clients.index',['l'=>request()->input('l'),'p'=>request()->input('p')]) !!}&oa='+this.category+'&s=all&c=all&step={!! $outstanding_step_id !!}';
                                }
                            }
                        }
                    }
                }
            });

                    @if(\Request::get('p') == '7')
            let blackboardDashboard5 = Highcharts.chart('blackboard-dashboard-5', {
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
                        categories: [
                            @foreach($bck_outstanding1 as $name => $amount)
                                '{{$name}}',
                            @endforeach
                        ]
                    },
                    series: [
                        {
                            name: 'Outstanding Activities',
                            data: [
                                @foreach($bck_outstanding1 as $amount)
                                    @if($amount['user'] > 0)
                                    {{$amount['user']}},
                                    @endif
                                @endforeach
                            ]
                        },
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
                                        location.href = '{!! route('clients.index',['l'=>request()->input('l'),'p'=>request()->input('p')]) !!}&oa='+this.category+'&s=all&c=all&step=31';
                                    }
                                }
                            }
                        }
                    }
                });

            let blackboardDashboard6 = Highcharts.chart('blackboard-dashboard-6', {
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
                    categories: [
                        @foreach($bck_outstanding2 as $name => $amount)
                            '{{$name}}',
                        @endforeach
                    ]
                },
                series: [
                    {
                        name: 'Outstanding Activities',
                        data: [
                            @foreach($bck_outstanding2 as $amount)
                                @if($amount['user'] > 0)
                                {{$amount['user']}},
                                @endif
                            @endforeach
                        ]
                    },
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
                                    location.href = '{!! route('clients.index',['l'=>request()->input('l'),'p'=>request()->input('p')]) !!}&oa='+this.category+'&s=all&c=all&step=32';
                                }
                            }
                        }
                    }
                }
            });
            @endif

                    @if(\Request::get('p') == '8')
            let blackboardDashboard5 = Highcharts.chart('blackboard-dashboard-5', {
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
                        categories: [
                            @foreach($david_outstanding1 as $name => $amount)
                                '{{$name}}',
                            @endforeach
                        ]
                    },
                    series: [
                        {
                            name: 'Outstanding Activities',
                            data: [
                                @foreach($david_outstanding1 as $amount)
                                    @if($amount['user'] > 0)
                                        {{$amount['user']}},
                                    @endif
                                @endforeach
                            ]
                        },
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
                                        location.href = '{!! route('clients.index',['l'=>request()->input('l'),'p'=>request()->input('p')]) !!}&oa='+this.category+'&s=all&c=all&step=31';
                                    }
                                }
                            }
                        }
                    }
                });

            let blackboardDashboard6 = Highcharts.chart('blackboard-dashboard-6', {
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
                    categories: [
                        @foreach($david_outstanding2 as $name => $amount)
                            '{{$name}}',
                        @endforeach
                    ]
                },
                series: [
                    {
                        name: 'Outstanding Activities',
                        data: [
                            @foreach($david_outstanding2 as $amount)
                                @if($amount['user'] > 0)
                                    {{$amount['user']}},
                                @endif
                            @endforeach
                        ]
                    },
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
                                    location.href = '{!! route('clients.index',['l'=>request()->input('l'),'p'=>request()->input('p')]) !!}&oa='+this.category+'&s=all&c=all&step=32';
                                }
                            }
                        }
                    }
                }
            });
            @endif

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
                        type: 'bar'
                    }
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

            $('#completed_clients').on('change', function () {
                $(function() {

                    let ajaxCategories = [];
                    let ajaxValues = [];
                    let params = {
                        'process_id': '{!! request()->input('p') !!}',
                        'months': $('#completed_clients').val()
                    }

                    // Fire an ajax call
                    $.getJSON('graphs/getcompletedclientsajax', params, function(data){
                        // Break apart the json returned into categories and series values
                        $.each(data, function(name, value) {
                            ajaxCategories.push(name);
                            ajaxValues.push(value*1);
                        });

                        //console.log(ajaxCategories);
                        console.log(ajaxValues);

                        // Update the categories
                        blackboardDashboard2.update({
                            xAxis: {
                                type: 'category',
                                categories: ajaxCategories
                            },
                        });

                        // Set the series
                        blackboardDashboard2.xAxis[0].setCategories(ajaxCategories,false);
                        blackboardDashboard2.series[0].setData(ajaxValues, true);

                        blackboardDashboard2.redraw();
                    });
                });
                });
        })
            </script>
@endsection