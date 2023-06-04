@extends('layouts.app')
@section('title') Unauthorized @endsection
@section('header')
    <h1><i class="fa fa-exclamation-triangle"></i> @yield('title')</h1>
@endsection
@section('content')
    <div style="display: table;width: 100%;height:100%;">
        <div class="align-content-center" style="display: table-cell;vertical-align: middle;height: 100%;">
            <table class="table" align="center" style="width:auto;margin:0px auto;">
                <tr>
                    <td style="border-top:0px;text-align: center;color: #b7002a;"><h3><i class="fa fa-exclamation-triangle"></i> 403 Unauthorized.</h3><hr style="background: #b7002a;" /></td>
                </tr>
                <tr style="border-top:0px;">
                    <td style="border-top:0px;text-align: center;"><p>It appears you don't have permission to access this page. Please make sure you're authorized to view this content.</p></td>
                </tr>
            </table>
        </div>
    </div>
@endsection
