@extends('adminlte.default')

@section('title') Audit Report @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3><i class="fa fa-line-chart"></i> @yield('title')</h3>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="col-sm-12" style="text-align: left;">
            <form class="mt-3">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="activities_search">Activity</label>
                        <select name="activities_search" class="chosen-select form-control form-control-sm col-sm-12">
                            <option value="">All</option>
                            @foreach($activities as $activity)
                                <option value="{{$activity->name}}" {{(isset($_GET['activities_search']) && $_GET['activities_search'] == (int)$activity->name ? 'selected="selected"' : '')}}>{{$activity->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="client_search">Client</label>
                        {{Form::select('client_search',$clients_dropdown ,old('client_search'),['class'=>'chosen-select form-control form-control-sm col-sm-12'])}}
                    </div>
                    <div class="form-group col-md-3">
                        <label for="rp_search">Related Party</label>
                        {{Form::select('rp_search',$related_party_dropdown ,old('rp_search'),['class'=>'chosen-select form-control form-control-sm col-sm-12'])}}
                    </div>
                    <div class="form-group col-md-3">
                        <label for="f">User</label>
                        <select name="user" class="chosen-select form-control form-control-sm col-sm-12">
                            <option value="">All</option>
                            @foreach($users as $user)
                                <option value="{{$user->id}}" {{(isset($_GET['user']) && $_GET['user'] == (int)$user->id ? 'selected="selected"' : '')}}>{{$user->full_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="f">From: date</label>
                        {{Form::date('f',old('f'),['class'=>'form-control form-control-sm col-sm-12'])}}
                    </div>
                    <div class="form-group col-md-3">
                        <label for="t">To: date</label>
                        {{Form::date('t',old('t'),['class'=>'form-control form-control-sm col-sm-12'])}}
                    </div>
                    <div class="form-group col-md-4" style="margin-top:1.5em">
                        <button type="submit" class="btn btn-sm btn-secondary ml-2 mt-2"><i class="fa fa-search"></i> Search</button>&nbsp;
                        <a href="{{route('reports.auditreport')}}" class="btn btn-sm btn-info mt-2"><i class="fa fa-eraser"></i> Clear</a>
                    </div>
                </div>
            </form>
        </div>
        {{--<div class="col-sm-2 float-right" style="margin-top:-4em;text-align: right;">
            <form id="download_pdf" class="form-inline mt-3" style="display: inline-block" action="{{route('audit.pdfexport')}}">
                <input type="hidden" id="pdf_activities_search" name="pdf_activities_search" value="{{isset($_GET['activities_search'])?$_GET['activities_search']:''}}" />
                <input type="hidden" id="pdf_client_search" name="pdf_client_search" value="{{isset($_GET['client_search'])?$_GET['client_search']:''}}" />
                <input type="hidden" id="pdf_user" name="pdf_user" value="{{isset($_GET['user'])?$_GET['user']:''}}" />
                <input type="hidden" id="pdf_f" name="pdf_f" value="{{isset($_GET['f'])?$_GET['f']:''}}" />
                <input type="hidden" id="pdf_t" name="pdf_t" value="{{isset($_GET['t'])?$_GET['t']:''}}" />
                <button style="margin-right: 5px;" type="submit" class="btn btn-default btn-sm"><i class="fa fa-file-pdf-o"></i> PDF</button>
            </form>
            <form id="download_xls" class="form-inline mt-3" style="display: inline-block" action="{{route('audit.export')}}">
                <input type="hidden" id="pdf_activities_search" name="pdf_activities_search" value="{{isset($_GET['activities_search'])?$_GET['activities_search']:''}}" />
                <input type="hidden" id="pdf_client_search" name="pdf_client_search" value="{{isset($_GET['client_search'])?$_GET['client_search']:''}}" />
                <input type="hidden" id="pdf_user" name="pdf_user" value="{{isset($_GET['user'])?$_GET['user']:''}}" />
                <input type="hidden" id="pdf_f" name="pdf_f" value="{{isset($_GET['f'])?$_GET['f']:''}}" />
                <input type="hidden" id="pdf_t" name="pdf_t" value="{{isset($_GET['t'])?$_GET['t']:''}}" />
                <button style="margin-right: 10px;" type="submit" class="btn btn-default btn-sm"><i class="fa fa-file-excel-o"></i> Excel</button>
            </form>
        </div>--}}
        <hr>
            <div class="table-responsive js-pscroll">
                <table id="logs" class="table table-responsive table-bordered table-sm table-hover" style="border: 1px solid #dee2e6;display: block;overflow-x:auto !important;max-height: 75vh;border-collapse: collapse">
                    <thead>
                    <tr width="100%">
                        <th width="20%">Action</th>
                        <th width="40%">Client</th>
                        {{--<th width="40%">Related Party</th>--}}
                        <th width="20%">Activity Name</th>
                        <th width="20%">Activity Value</th>
                        <th width="20%">User</th>
                        <th width="20%">Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($log_array as $activity)
                        <tr {{($activity["user_id"] != 0 ? '' : 'style="background:#dee2e6;"')}}>
                            <td>{{$activity["action"]}}</td>
                            <td>{{$activity["client"]}}</td>
                            {{--<td>{{$activity["relatedparty"]}}</td>--}}
                            <td>{{$activity["activity_name"]}}</td>
                            <td>{!! $activity["new_value"] !!}</td>
                            <td>{{($activity["user_id"] != 0 ? $activity["user"] : $activity["client"])}}</td>
                            <td>{{$activity["created_at"]}}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">No records found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

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
            border-top:1px solid #dee2e6!important;
        }

    </style>
@endsection
@section('extra-js')
    <script>
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
    </script>
@endsection