@extends('flow.default')
@section('title') Show Report - {{$report_name}}@endsection
@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <div class="nav-btn-group">
            <a href="{{route('custom_report.index')}}" class="btn btn-outline-primary mt-3 btn-sm float-right">Back</a>
        </div>
    </div>
@endsection
@section('content')
    {{--<div class="container-fluid row">
        <div class="col-md-12">
        <form class="mt-3">

            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="q">Matching</label>
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fa fa-search"></i>
                            </div>
                        </div>
                        {{Form::text('q',old('query'),['class'=>'form-control form-control-sm','placeholder'=>'Search...'])}}
                        <button type="submit" class="btn btn-sm btn-secondary ml-2 float-right"><i class="fa fa-search"></i> Search</button>&nbsp;
                        <a href="{{route('custom_report.show',[$report_id,$report_type])}}" class="btn btn-sm btn-info"><i class="fa fa-eraser"></i> Clear</a>
                    </div>
                </div>
            </div></form>
            <div class="form-row">
                <div class="form-group col-sm-12" style="text-align: right;">
                    <form id="download_pdf" class="form-inline mt-3" style="display: inline-block" action="{{route('custom_report.pdfexport', $report_id)}}">
                        <input type="hidden" name="user" value="{{isset($_GET['user'])?$_GET['user']:''}}" />
                        <input type="hidden" name="committee" value="{{isset($_GET['committee'])?$_GET['committee']:''}}" />
                        <input type="hidden" name="trigger" value="{{isset($_GET['trigger'])?$_GET['trigger']:''}}" />
                        <input type="hidden" name="f" value="{{isset($_GET['f'])?$_GET['f']:''}}" />
                        <input type="hidden" name="t" value="{{isset($_GET['t'])?$_GET['t']:''}}" />
                        <input type="hidden" name="q" value="{{isset($_GET['q'])?$_GET['q']:''}}" />
                        <button style="margin-right:5px;" type="submit" class="btn btn-default btn-sm"><i class="fa fa-file-pdf-o"></i> PDF</button>
                    </form>
                    <form id="download_excel" class="form-inline mt-3" style="display: inline-block" action="{{route('custom_report.export', $report_id)}}">
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
    <hr>--}}
    <div class="content-container page-content">
        <div class="row col-md-12 h-100 pr-0">
            @yield('header')
            <div class="container-fluid index-container-content" style="height: calc(100% - 43px);">
                <div class="form-row">
                    <div class="form-group col-sm-12 p-0 m-0" style="text-align: right;">
                        <form id="download_pdf" class="form-inline mt-3" style="display: inline-block" action="{{route('custom_report.pdfexport', [$report_id,$report_type])}}">
                            <input type="hidden" name="user" value="{{isset($_GET['user'])?$_GET['user']:''}}" />
                            <input type="hidden" name="f" value="{{isset($_GET['f'])?$_GET['f']:''}}" />
                            <input type="hidden" name="t" value="{{isset($_GET['t'])?$_GET['t']:''}}" />
                            <input type="hidden" name="q" value="{{isset($_GET['q'])?$_GET['q']:''}}" />
                            <button style="margin-right:5px;" type="submit" class="btn btn-info btn-sm"><i class="fas fa-file-pdf"></i> PDF</button>
                        </form>
                        <form id="download_excel" class="form-inline mt-3" style="display: inline-block" action="{{route('custom_report.export', [$report_id,$report_type])}}">
                            <input type="hidden" name="user" value="{{isset($_GET['user'])?$_GET['user']:''}}" />
                            <input type="hidden" name="f" value="{{isset($_GET['f'])?$_GET['f']:''}}" />
                            <input type="hidden" name="t" value="{{isset($_GET['t'])?$_GET['t']:''}}" />
                            <input type="hidden" name="q" value="{{isset($_GET['q'])?$_GET['q']:''}}" />
                            <button type="submit" class="btn btn-info btn-sm"><i class="fas fa-file-excel"></i> Excel</button>
                        </form>
                    </div>
                </div>
                <div class="table-responsive mt-3" style="border: 1px solid #dee2e6;display: block;overflow-x:auto !important;max-height: 75vh;border-collapse: collapse">
                    <table class="table table-bordered table-sm table-hover m-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Process Stage</th>
                                @foreach($fields as $key => $val)
                                    @if($val != null)
                                        <th>{{$val}}</th>
                                    @endif
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                        @php $related_parties_count = 0; @endphp
                        @forelse($clients as $client)
                            @if(isset($client['id']))
                            <tr>
                                <td class="table100-firstcol"><a href="{{route('clients.overview',[$client["id"],$client["process_id"],$client["step_id"]])}}">{{$client['company']}}</a></td>
                                @foreach ($step_names as $step_name)
                                    @if ($step_name->id == $client['step_id'])
                                    <td><a href="{{route('clients.show',$client["id"])}}">{{$step_name->name}}</a></td>
                                    @break
                                    @endif
                                @endforeach
                                @foreach($client["data"] as $key => $val)
                                        <td><a href="{{route('clients.show',$client["id"])}}">@if($val != strip_tags($val)) {!! $val !!} @else {{$val}} @endif</a></td>
                                    @endforeach
                            </tr>
                            @endif
                            {{--@if(isset($client['rp']) && count($client['rp']) > 0)
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
                            @endif--}}
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
    }
    )
</script>
@endsection