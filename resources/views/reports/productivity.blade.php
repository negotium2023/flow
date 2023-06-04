@extends('adminlte.default')

@section('title') Productivity Report @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3><i class="fa fa-line-chart"></i> @yield('title')</h3>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <form class="form-inline mt-3">
            User: &nbsp;
            {{Form::select('user_search',$users_list ,old('user_search'),['class'=>'form-control form-control-sm'])}}
            &nbsp;From: &nbsp;
            {{Form::date('from',old('from'),['class'=>'form-control form-control-sm'])}}
            &nbsp; To: &nbsp;
            {{Form::date('to',old('to'),['class'=>'form-control form-control-sm','id'=>'to'])}}

            <button type="submit" class="btn btn-sm btn-secondary ml-2 mr-2"><i class="fa fa-search"></i> Search</button>
            <a href="{{route('reports.productivity')}}" class="btn btn-sm btn-info"><i class="fa fa-eraser"></i> Clear</a>
        </form>
        <hr>
        <div class="row">
            <div class="col-sm-6">
                <div class="js-pscroll" style="height: 70vh;max-height:70vh;overflow-x:scroll;">
                    <table class="table table-bordered table-hover" style="overflow-x:scroll;">
                        <thead>
                        <tr class="btn-dark">
                            <th class="multi-select-demo">
                                <select style="width: 100%;" id="multi-select-demo" multiple="multiple">
                                    @foreach($users as $key=>$fvalue)
                                    <option value="{{str_replace(' ','',$fvalue["full_name"])}}">{{$fvalue["full_name"]}}</option>
                                    @endforeach
                                </select>
                                {{Form::hidden('from2',(request()->has('from') ? request()->input('from') : old('from2')),['class'=>'form-control form-control-sm','id'=>'from2'])}}
                                {{Form::hidden('to2',(request()->has('to') ? request()->input('to') : old('to2')),['class'=>'form-control form-control-sm','id'=>'to2'])}}
                            </th>
                            @for($i = 0; $i < count($date_range);$i++)
                                <th style="width: 30px;"><div><span style="writing-mode: vertical-lr;
                             -ms-writing-mode: tb-rl;
                             transform: rotate(180deg);">{{$date_range[$i]}}</span></div></th>
                                {{--<th style="text-align: center" class="rotate"><span>{{$date}}</span></th>--}}
                            @endfor
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $key=>$fvalue)
                            <tr class="trdata {{str_replace(' ','',$fvalue["full_name"])}}" style="vertical-align: middle;">
                                <td style="padding: 0px;">
                                    <span style="display: block;float: left;height: 100">{{$fvalue["full_name"]}}</span>
                                    <span style="display: block;float: right;clear: right;">Assigned</span><br />
                                    <span style="display: block;float: right;">Completed</span>
                                </td>
                                @foreach($date_range as $date)
                                    @if(isset($usage[$fvalue["id"]][(int)\Carbon\Carbon::parse($date)->format("Ymd")]['assigned']))

                                            <td style="text-align: center;padding: 0px;">
                                                <span style="display: block;width: 100%;text-align: center;background-color: rgba(0,0,0,.05);">{{$usage[$fvalue["id"]][(int)\Carbon\Carbon::parse($date)->format("Ymd")]['assigned']}}</span>

                                                <span style="display: block;width: 100%;text-align: center;background: #ffa0b6;">{{$usage[$fvalue["id"]][(int)\Carbon\Carbon::parse($date)->format("Ymd")]['completed']}}</span>
                                            </td>


                                    @else
                                        <td style="text-align: center;padding: 0px;">
                                            <span style="display: block;width: 100%;text-align: center;background-color: rgba(0,0,0,.05);">0</span>
                                            <span style="display: block;width: 100%;text-align: center;background: #ffa0b6;">0</span>
                                        </td>
                                    @endif
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-sm-6">
                <div id="productivity-graph" style="height: 70vh"></div>
            </div>
        </div>
    </div>
@endsection
@section('extra-css')

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

        tbody td:first-child {
            position: -webkit-sticky; /* for Safari */
            position: sticky;
            left: 0;
        }
        thead th:first-child {
            left: -1px;
            z-index: 3;
        }
        tbody td:first-child {
            left: -1px;
            z-index: 1;
            background: #FFFFFF;
            border-left: 1px solid #ffffff
        }

        .column-shadow{
            box-shadow: 8px 0px 10px 0px rgba(0, 0, 0, 0.05);
            -moz-box-shadow: 8px 0px 10px 0px rgba(0, 0, 0, 0.05);
            -webkit-box-shadow: 8px 0px 10px 0px rgba(0, 0, 0, 0.05);
            -o-box-shadow: 8px 0px 10px 0px rgba(0, 0, 0, 0.05);
            -ms-box-shadow: 8px 0px 10px 0px rgba(0, 0, 0, 0.05);
            border-left: 1px solid #dee2e6;
        }

        .highcharts-container {
            overflow: visible !important;
        }

        .MyChartTooltip {
            position: relative;
            z-index: 50;
            border: 2px solid rgb(0, 108, 169);
            border-radius: 5px;
            background-color: #ffffff;
            padding: 5px;
            font-size: 9pt;
        }
    </style>
@endsection
@section('extra-js')
    <script>

        $(document).ready(function() {

            // Build the chart
            Highcharts.theme = {
                color: ( // theme
                    Highcharts.defaultOptions.title.style &&
                    Highcharts.defaultOptions.title.style.color
                ) || 'gray',
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


            var mcategories = [@foreach($totals as $key => $value)
                '{!! Carbon\Carbon::parse($key)->format('Y-m-d') !!}',
                @endforeach
            ];
            var categories = [@foreach($totals as $key => $value)
                '{!! Carbon\Carbon::parse($key)->format('Y-m-d') !!}',
                @endforeach
            ];

            let blackboardDashboard1 = Highcharts.chart('productivity-graph',{
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: ''
                    },
                    subtitle: {
                        text: ''
                    },
                    xAxis: {
                        type: 'category',
                        labels: {
                            y: 20,
                            rotation: 90,
                            align: 'left'
                        },
                        categories: categories,
                        crosshair: true
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: ''
                        },
                        stackLabels: {
                            enabled: false,
                            style: {
                                fontWeight: 'bold',
                                color: ( // theme
                                    Highcharts.defaultOptions.title.style &&
                                    Highcharts.defaultOptions.title.style.color
                                ) || 'gray'
                            }
                        }
                    },
                    tooltip: {
                        headerFormat: '<b style="display: block;width:100%;text-align:center;">{point.x}</b><br />',
                        shared: true,
                        useHTML: true,

                        style: {
                            zIndex: 1000
                        }
                    },
                    plotOptions: {
                        column: {
                            pointPadding: 0,
                            borderWidth: 0,
                            groupPadding: 0,
                            shadow: false,
                            dataLabels: {
                                enabled: true
                            },
                            events: {
                                click: function(event) {
                                    // Grab the date from the HighCharts event object
                                    clickedDate = event.point.category.name;
                                    // Bounce to client view on the given date, filtering by both assigned and completed
                                    location.href = "/clients?s=assigned&user=&p=all&step=&c=yes&f="+ clickedDate+"&t="+ clickedDate;
                                }
                            },
                        },
                    },
                    series: [
                        {
                            name: 'Assigned',
                            data: [
                                @foreach($totals as $key=>$date)
                                    {!! $date['assigned'] !!},
                                @endforeach
                            ], 
                        },
                        {
                            name: 'Completed',
                            data: [
                                @foreach($totals as $key=>$date)
                                    {!! $date['completed'] !!},
                                @endforeach
                            ]
                        }
                    ]
                });
    
            $('#multi-select-demo').multiselect({
                buttonWidth: '250px',
                includeSelectAllOption: true,
                onSelectAll: function(){
                    $('.trdata').show();
                },
                onDeselectAll: function() {
                    $('.trdata').hide();
                },
                onChange: function(element, checked) {

                    if($('.'+element.val()).is(':visible')){
                        $('.'+element.val()).hide();
                    } else {
                        $('.'+element.val()).show();
                    }
                    /*productivityFilter();*/
                }});

            $("#multi-select-demo").multiselect('selectAll', false);
            $("#multi-select-demo").multiselect('updateButtonText');

            function productivityFilter() {
                var allVal=$("#multi-select-demo").val();
                var from=$("#from2").val();
                var to=$("#to2").val();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: '/reports/productivityreportajax',
                    type:"POST",
                    data:{allVal:allVal,from:from,to:to},
                    success:function(data){

                        //console.log(data);
                        let ajaxCategories = [];
                        let ajaxValues = [];

                        let  name1w = data.week_end;
                        var j = null;

                        j = JSON.parse(JSON.stringify(data.users));
                        $.each(data.date_range, function (name4, values4) {
                            /*console.log(values4);*/
                            ajaxCategories.push(values4);
                        })

                        //console.log(data.usage);

                        for (i = 0; i < j.length; i++) {
                            var s = '';
                            let ajaxValues2 = [];
                            let ajaxValues3 = [];



                                /*if(values < jQuery.inArray([name1w.slice(0, 4), name1w.slice(4, 6), name1w.slice(6, 8)].join('-'))) {*/
                                    $.each(data.usage, function (name2, values2) {
                                        /*console.log(values2);*/
                                        $.each(values2, function (name1, values1) {
                                            //console.log(values1);
                                            /*if(typeof(variable) != "undefined" && variable !== null) {*/
                                            ajaxValues.push({name:j[i]['full_name'],data:ajaxValues2});
                                        })
                                    })
                                /*}*/



                            /*})*/
                            /*ajaxValues.push({name:j[i]['full_name'],data:ajaxValues2});*/
                        }


                        console.log(ajaxValues);

                        /*var seriesLength = blackboardDashboard1.series.length;
                        for(var i = seriesLength -1; i > -1; i--) {
                            blackboardDashboard1.series[i].remove();
                        }*/

                        // Update the categories
                        blackboardDashboard1.update({
                            xAxis: {
                                type: 'category',
                                categories: ajaxCategories
                            }
                        });

                        // Set the series
                        blackboardDashboard1.xAxis[0].setCategories(ajaxCategories,false);
                        blackboardDashboard1.series[0].setData(ajaxValues,true);


                        blackboardDashboard1.redraw();
                    }
                });
                return false;
            }
        });


    </script>
@endsection