@extends('layouts.app')
@section('title') Service Unavailable 503 @endsection
@section('header')
    <h1><i class="fa fa-exclamation-triangle"></i> @yield('title')</h1>
@endsection
@section('content')
    <div style="display: table;width: 100%;height:100%;">
        <div class="align-content-center" style="display: table-cell;vertical-align: middle;height: 100%;">
            <table class="table" align="center" style="width:auto;margin:0px auto;">
                <tr>
                    <td style="border-top:0px;text-align: center;color: #b7002a;"><h3><i class="fa fa-exclamation-triangle"></i> 503 Service Unavailable.</h3><hr style="background: #b7002a;" /></td>
                </tr>
                <tr style="border-top:0px;">
                    <td style="border-top:0px;text-align: center;"><p>The service you requested is temporarily unavailable.</p></td>
                </tr>
            </table>
        </div>
    </div>
@endsection
