@extends('layouts.app')
@section('title') HTTP Error 401 @endsection
@section('header')
    <h1><i class="fa fa-exclamation-triangle"></i> @yield('title')</h1>
@endsection
@section('content')
    <div style="display: table;width: 100%;height:100%;">
        <div class="align-content-center" style="display: table-cell;vertical-align: middle;height: 100%;">
            <table class="table" align="center" style="width:auto;margin:0px auto;">
                <tr>
                    <td style="border-top:0px;text-align: center;color: #b7002a;"><h3><i class="fa fa-exclamation-triangle"></i> 401 HTTP Error.</h3><hr style="background: #b7002a;" /></td>
                </tr>
                <tr style="border-top:0px;">
                    <td style="border-top:0px;text-align: center"><p>You do not have the correct permissions to view this page.</p></td>
                </tr>
            </table>
        </div>
    </div>
@endsection
