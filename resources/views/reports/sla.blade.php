@extends('adminlte.default')

@section('title') SLA Report @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3><i class="fa fa-line-chart"></i> @yield('title')</h3>
    </div>
@endsection

@section('content')
    <div class="container-fluid">

        <hr>
        <div class="row">
            <div class="col-sm-12">
                <div>
                    <table class="table" style="overflow-x:scroll;">
                        <thead>
                        <tr style="height: 150px;">
                            <td class="btn-dark" style="border-radius:10px;text-align:center;vertical-align:middle;width:10.75%">Instruction received</td>
                            <td style="padding: 0px;">
                                <span class="first-green">Minimum (days)</span><i class="right first-green-right"></i>
                                <span class="first-yellow">Average (days)</span><i class="right first-yellow-right"></i>
                                <span class="first-red">Max (days)</span><i class="right first-red-right"></i>
                            </td>
                            <td class="btn-dark" style="border-radius:10px;text-align:center;vertical-align:middle;width:10.75%">Clients moved in QA</td>
                            <td style="padding: 0px;">
                                <span class="first-green">Minimum (days)</span><i class="right first-green-right"></i>
                                <span class="first-yellow">Average (days)</span><i class="right first-yellow-right"></i>
                                <span class="first-red">Max (days)</span><i class="right first-red-right"></i>
                            </td>
                            <td class="btn-dark" style="border-radius:10px;text-align:center;vertical-align:middle;width:10.75%">QA Complete</td>
                            <td style="padding: 0px;">
                                <span class="first-green">Minimum (days)</span><i class="right first-green-right"></i>
                                <span class="first-yellow">Average (days)</span><i class="right first-yellow-right"></i>
                                <span class="first-red">Max (days)</span><i class="right first-red-right"></i>
                            </td>
                            <td class="btn-dark" style="border-radius:10px;text-align:center;vertical-align:middle;width:10.75%">Committee decision captured</td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="7"></td>
                        </tr>
                        <tr style="height: 150px;">
                            <td></td>
                            <td style="padding: 0px;">
                                <table class="table tablec">
                                    <tr>
                                        <th>User</th>
                                        <th class="last">Max Days</th>
                                    </tr>
                                    <tr>
                                        <td>Nico</td>
                                        <td>34</td>
                                    </tr>
                                    <tr>
                                        <td>A</td>
                                        <td>5</td>
                                    </tr>
                                    <tr>
                                        <td>B</td>
                                        <td>9</td>
                                    </tr>
                                    <tr>
                                        <td>C</td>
                                        <td>14</td>
                                    </tr>
                                    <tr>
                                        <td>D</td>
                                        <td>7</td>
                                    </tr>
                                    <tr>
                                        <td>E</td>
                                        <td>61</td>
                                    </tr>
                                    <tr>
                                        <td>F</td>
                                        <td>22</td>
                                    </tr>
                                </table>
                            </td>
                            <td></td>
                            <td style="padding: 0px;">
                                <table class="table tablec">
                                    <tr>
                                        <th>User</th>
                                        <th class="last">Max Days</th>
                                    </tr>
                                    <tr>
                                        <td>Nico</td>
                                        <td>34</td>
                                    </tr>
                                    <tr>
                                        <td>A</td>
                                        <td>5</td>
                                    </tr>
                                    <tr>
                                        <td>B</td>
                                        <td>9</td>
                                    </tr>
                                    <tr>
                                        <td>C</td>
                                        <td>14</td>
                                    </tr>
                                    <tr>
                                        <td>D</td>
                                        <td>7</td>
                                    </tr>
                                    <tr>
                                        <td>E</td>
                                        <td>61</td>
                                    </tr>
                                    <tr>
                                        <td>F</td>
                                        <td>22</td>
                                    </tr>
                                </table>

                            </td>
                            <td></td>
                            <td style="padding: 0px;">
                                <table class="table tablec">
                                    <tr>
                                        <th>User</th>
                                        <th class="last">Max Days</th>
                                    </tr>
                                    <tr>
                                        <td>Nico</td>
                                        <td>34</td>
                                    </tr>
                                    <tr>
                                        <td>A</td>
                                        <td>5</td>
                                    </tr>
                                    <tr>
                                        <td>B</td>
                                        <td>9</td>
                                    </tr>
                                    <tr>
                                        <td>C</td>
                                        <td>14</td>
                                    </tr>
                                    <tr>
                                        <td>D</td>
                                        <td>7</td>
                                    </tr>
                                    <tr>
                                        <td>E</td>
                                        <td>61</td>
                                    </tr>
                                    <tr>
                                        <td>F</td>
                                        <td>22</td>
                                    </tr>
                                </table>
                            </td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('extra-css')

    <style>
        thead td i {
            border: solid black;
            border-width: 0 3px 3px 0;
            display: inline-block;
            padding: 3px;
            position: absolute;
        }

        thead td span{
            width:100%;
            display: inline-block;
            position: absolute;
            text-align: center;

        }

        .right {
            transform: rotate(-45deg);
            -webkit-transform: rotate(-45deg);
            position: absolute;
            right:0px;
        }
        thead td{
            border-top:0px !important;
            position: relative;
        }

        tbody td{
            border-top:0px !important;
            position: relative;
        }

        .first-green{
            top:6px;
            border-bottom: 3px solid #00a65a;
        }
        .first-green-right{
            top:27px;
            border-bottom: 3px solid #00a65a;
            border-right: 3px solid #00a65a;
        }

        .first-yellow{
            top:53px;
            border-bottom: 3px solid #FFAE42;
        }
        .first-yellow-right{
            top:74px;
            border-bottom: 3px solid #FFAE42;
            border-right: 3px solid #FFAE42;
        }

        .first-red{
            top:101px;
            border-bottom: 3px solid #b57373;
        }
        .first-red-right{
            top:122px;
            border-bottom: 3px solid #b57373;
            border-right: 3px solid #b57373;
        }

        .tablec th{
            color:#FFFFFF;
            border-top:solid 2px #313131;
            border-bottom:solid 3px #313131;
            background: #a5a5a5;
        }

        .tablec tr:last-child{
            border-bottom:solid 3px #313131;
        }

        .tablec td:last-child{
            text-align: right;
        }

        .tablec tr:nth-of-type(2){
            background:#e7e8e8;
        }

        .tablec tr:nth-of-type(4){
            background:#e7e8e8;
        }

        .tablec tr:nth-of-type(6){
            background:#e7e8e8;
        }

        .tablec tr:nth-of-type(8){
            background:#e7e8e8;
        }

        .tablec tr:nth-of-type(10){
            background:#e7e8e8;
        }

        .tablec tr:nth-of-type(12){
            background:#e7e8e8;
        }
    </style>
@endsection