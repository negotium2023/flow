@extends('adminlte.default')

@section('title') Usage Report @endsection

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
            {{Form::date('to',old('to'),['class'=>'form-control form-control-sm'])}}

            <button type="submit" class="btn btn-sm btn-secondary ml-2 mr-2"><i class="fa fa-search"></i> Search</button>
            <a href="{{route('reports.usage')}}" class="btn btn-sm btn-info"><i class="fa fa-eraser"></i> Clear</a>
        </form>
        <hr>
        <div class="js-pscroll" style="overflow-x:scroll;">
            <table class="table table-bordered" style="overflow-x:scroll;">
            <thead>
                <tr class="btn-dark">
                    <th></th>
                @foreach($date_range as $date)
                             <th style="text-align: center">{{$date}}</th>
                @endforeach
                </tr>
            </thead>
            <tbody>
                    @foreach($users as $key=>$fvalue)
                        <tr>
                            <td>{{$fvalue["full_name"]}}</td>
                                @foreach($date_range as $date)
                                    @if(isset($usage[$fvalue["id"]][(int)\Carbon\Carbon::parse($date)->format("Ymd")]))
                                            <td style="text-align: center">{{$usage[$fvalue["id"]][(int)\Carbon\Carbon::parse($date)->format("Ymd")]}}</td>
                                        @else
                                            <td style="text-align: center">0</td>
                                    @endif
                                @endforeach
                        </tr>
                    @endforeach
            </tbody>
            </table>
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
    </style>
@endsection
@section('extra-js')
    {{--<script>
        $(document).ready(function() {
            $('#logs').DataTable();

            $('.js-pscroll').each(function () {
                var ps = new PerfectScrollbar(this);

                $(window).on('resize', function () {
                    ps.update();
                })

                $(this).on('ps-x-reach-start', function () {
                    $('.table100-firstcol').removeClass('column-shadow');
                });

                $(this).on('ps-scroll-x', function () {
                    $('.table100-firstcol').addClass('column-shadow');
                });

            })
        })
    </script>--}}
@endsection