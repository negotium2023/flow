@extends('adminlte.default')
@section('title') {{$activity->name}} Report @endsection
@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <a href="{{route('reports.index')}}" class="btn btn-dark btn-sm float-right"><i class="fa fa-caret-left"></i> Back</a>
    </div>
@endsection
@section('content')
    <div class="container-fluid row">
        <div class="col-md-12">

            <form class="mt-3 col-sm-12">
                <div class="form-row">

                    <div class="form-group col-md-2">
                        <label for="f">Assigned User</label>
                        {{Form::select('user',$assigned_user,old('user'),['class'=>'chosen-select form-control form-control-sm', 'placeholder'=>'User'])}}
                    </div>
                    <div class="form-group col-md-1">
                        <label for="f">Committee</label>
                        {{Form::select('committee',$committee,old('committee'),['class'=>'chosen-select form-control form-control-sm', 'placeholder' => 'Committee'])}}
                    </div>
                    <div class="form-group col-md-2">
                        <label for="f">Trigger Type</label>
                        {{Form::select('trigger',$trigger,old('committee'),['class'=>'chosen-select form-control form-control-sm', 'placeholder' => 'Trigger Type'])}}
                    </div>
                    <div class="form-group col-md-2">
                        <label for="f">From: instruction date</label>
                        {{Form::date('f',old('f'),['class'=>'form-control form-control-sm'])}}
                    </div>
                    <div class="form-group col-md-2">
                        <label for="t">To: instruction date </label>
                        {{Form::date('t',old('t'),['class'=>'form-control form-control-sm'])}}
                    </div>
                    <div class="form-group col-md-3">
                        <label for="q">Matching</label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fa fa-search"></i>
                                </div>
                            </div>
                            {{Form::text('q',old('q'),['class'=>'form-control form-control-sm col-sm-8','placeholder'=>'Search...'])}}
                            <button type="submit" class="btn btn-sm btn-secondary ml-2" style="float: right !important;"><i class="fa fa-search"></i> Search</button>&nbsp;
                            <a href="{{route('reports.show',$activity)}}" class="btn btn-sm btn-info"><i class="fa fa-eraser"></i> Clear</a>
                        </div>
                    </div>

                </div>
            </form>
            <div class="form-row">
                <div class="col-sm-12" style="text-align: right;">
                    <form id="download_pdf" class="form-inline mt-3" style="display: inline-block" action="{{route('reports.pdfexport', $activity)}}">
                        <input type="hidden" name="user" value="{{isset($_GET['user'])?$_GET['user']:''}}" />
                        <input type="hidden" name="committee" value="{{isset($_GET['committee'])?$_GET['committee']:''}}" />
                        <input type="hidden" name="trigger" value="{{isset($_GET['trigger'])?$_GET['trigger']:''}}" />
                        <input type="hidden" name="f" value="{{isset($_GET['f'])?$_GET['f']:''}}" />
                        <input type="hidden" name="t" value="{{isset($_GET['t'])?$_GET['t']:''}}" />
                        <input type="hidden" name="q" value="{{isset($_GET['q'])?$_GET['q']:''}}" />
                        <button style="margin-right: 5px;" type="submit" class="btn btn-default btn-sm"><i class="fa fa-file-pdf-o"></i> PDF</button>
                    </form>
                    <form id="download_excel" class="form-inline mt-3" style="display: inline-block" action="{{route('reports.export', $activity)}}">
                        <input type="hidden" name="user" value="{{isset($_GET['user'])?$_GET['user']:''}}" />
                        <input type="hidden" name="committee" value="{{isset($_GET['committee'])?$_GET['committee']:''}}" />
                        <input type="hidden" name="trigger" value="{{isset($_GET['trigger'])?$_GET['trigger']:''}}" />
                        <input type="hidden" name="f" value="{{isset($_GET['f'])?$_GET['f']:''}}" />
                        <input type="hidden" name="t" value="{{isset($_GET['t'])?$_GET['t']:''}}" />
                        <input type="hidden" name="q" value="{{isset($_GET['q'])?$_GET['q']:''}}" />
                        <button type="submit" class="btn btn-default btn-sm"><i class="fa fa-file-excel-o"></i> Excel</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
    <div class="container-fluid">
    <div class="row">
    </div>
    <hr>
        <div class="container-fluid">
            <div class="js-pscroll">
                <table class="table table-bordered table-sm table-hover" style="border: 1px solid #dee2e6;display: table;overflow-x:auto !important;max-height: 75vh;border-collapse: collapse;min-width: 100%;">
            <thead class="btn-dark">
            <tr>
                <th></th>
                <th>Name</th>
                <th>Case Number</th>
                <th>CIF Code</th>
                <th>Committee</th>
                <th>Trigger Type</th>
                <th>Activity Name: {{$activity->name}}</th>
                {{--{!! $activity->actionable_type == "App\ActionableText" || $activity->actionable_type == "App\ActionableDate" || $activity->actionable_type == "App\ActionableDropdown"/*|| $activity->actionable_type == "App\ActionableDocument"*/ ? '<th>Activity Value</th>' : '' !!}--}}
                <th>Instruction Date</th>
                <th>Assigned User</th>

            </tr>
            </thead>
            <tbody>
            @forelse($clients as $client)
                @if(isset($client['id']))
                <tr>
                    <td class="table100-firstcol"><a href="{{route('clients.show',$client['id'])}}">{{$client['type']}}</a></td>
                    <td><a href="{{route('clients.show',$client['id'])}}">{{$client['company']}}</a></td>
                    <td><a href="{{route('clients.show',$client['id'])}}">{{$client['case_nr']}}</a></td>
                    <td><a href="{{route('clients.show',$client['id'])}}">{{$client['cif_code']}}</a></td>
                    <td><a href="{{route('clients.show',$client['id'])}}">{{$client['committee']}}</a></td>
                    <td><a href="{{route('clients.show',$client['id'])}}">{{$client['trigger']}}</a></td>

                    {!! $activity->actionable_type == "App\ActionableDropdown" ? '<td><a href="'.route('clients.show',$client['id']).'">'.$client["activity_data"].'</a></td>' : '' !!}
                    {!! $activity->actionable_type == "App\ActionableDocument" || $activity->actionable_type == "App\ActionableBoolean" ? '<td><a href="'.route('clients.show',$client['id']).'">'.$client['completed_yn'].'</a></td>' : '' !!}
                    {!! $activity->actionable_type == "App\ActionableText" || $activity->actionable_type == "App\ActionableDate" ? '<td><a href="'.route('clients.show',$client['id']).'">'.$client['data_value'].'</a></td>' : '' !!}
                    <td><a href="{{route('clients.show',$client["id"])}}">{{$client['instruction_date']}}</a></td>
                    <td><a href="{{route('clients.show',$client["id"])}}">{{$client['consultant']}}</a></td>
                </tr>
                @endif
                @if(isset($client['rp']) && count($client['rp']) > 0)
                    @foreach($client['rp'] as $rp)
                        <tr class="bg-gray-light">
                            <td>{{$rp['type']}}</td>
                            <td><a href="{{route('relatedparty.show',['client_id' => $rp["client_id"],'process_id' => $rp["process"],'step_id' => $rp["step"],'related_party_id'=>$rp["id"]])}}">{{$rp['company']}}</a></td>
                            <td><a href="{{route('relatedparty.show',['client_id' => $rp["client_id"],'process_id' => $rp["process"],'step_id' => $rp["step"],'related_party_id'=>$rp["id"]])}}">{{$rp['case_nr']}}</a></td>
                            <td><a href="{{route('relatedparty.show',['client_id' => $rp["client_id"],'process_id' => $rp["process"],'step_id' => $rp["step"],'related_party_id'=>$rp["id"]])}}">{{$rp['cif_code']}}</a></td>
                            <td><a href="{{route('relatedparty.show',['client_id' => $rp["client_id"],'process_id' => $rp["process"],'step_id' => $rp["step"],'related_party_id'=>$rp["id"]])}}">{{$rp['committee']}}</a></td>
                            <td><a href="{{route('relatedparty.show',['client_id' => $rp["client_id"],'process_id' => $rp["process"],'step_id' => $rp["step"],'related_party_id'=>$rp["id"]])}}">{{$rp['trigger']}}</a></td>
                            @foreach($rp["data"] as $key => $val)
                                <td><a href="{{route('relatedparty.show',['client_id' => $rp["client_id"],'process_id' => $rp["process"],'step_id' => $rp["step"],'related_party_id'=>$rp["id"]])}}">@if($val != strip_tags($val)) {!! $val !!} @else {{$val}} @endif</a></td>
                            @endforeach
                            <td><a href="{{route('relatedparty.show',['client_id' => $rp["client_id"],'process_id' => $rp["process"],'step_id' => $rp["step"],'related_party_id'=>$rp["id"]])}}">{{$rp['instruction_date']}}</a></td>
                            <td><a href="{{route('relatedparty.show',['client_id' => $rp["client_id"],'process_id' => $rp["process"],'step_id' => $rp["step"],'related_party_id'=>$rp["id"]])}}">@if($rp['consultant']['consultant'] != null){{$rp['consultant']['consultant']->first_name}} {{$rp['consultant']['consultant']->last_name}} @endif</a></td>
                        </tr>
                    @endforeach
                @endif
            @empty
                <tr>
                    <td colspan="100%" class="text-center"><small class="text-muted">No clients match those criteria.</small></td></td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
        <small class="text-muted">Found <b>{{$total}}</b> clients matching those criteria.</small>
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
    <script>
        $('select[name="activity"], [name="step"]').change(function () {
            $('#customreportsform').submit();

        });

        $(document).ready(function()
            {
                $('select').on('change', function () {
                    $(this).closest('form').submit();
                });

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

                });

                if($('#activity option:selected').val() > 0) {
                    $('#activity_selected_name').val($('#activity option:selected').text());
                }

                if($('#step option:selected').val() > 0) {
                    $('#process_step_selected_name').val($('#step option:selected').text());
                }
            }


        )
    </script>
@endsection