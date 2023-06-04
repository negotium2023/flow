@extends('client.show')

@section('tab-content')
    <div class="panel-group" id="related_parties_accordion">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <div style="display: inline; width: 90%;" data-toggle="collapse" data-parent="#accordion1" href="#collapseOne">
                        {{isset($client->first_name)?$client->first_name:''}} {{isset($client->last_name)?$client->last_name:''}}
                    </div>
                    <span style="float: right;">
                        <a class="panel-btn" href="{{route('relatedparty.add', [$client->id, 0])}}">+</a>&nbsp;<a href="{{route('relatedparty.activities', [$client->id, 0])}}" class="panel-btn">&#183;&#183;&#183;</a>
                    </span>
                </h4>
            </div>
            <div id="collapseOne" class="panel-collapse collapse in">
                <div class="panel-body">Panel 1</div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <div data-toggle="collapse" data-parent="#accordion1" href="#collapseTwo">
                        Collapsible Group Item #2
                    </div>
                </h4>
            </div>
            <div id="collapseTwo" class="panel-collapse collapse">
                <div class="panel-body">
                    <div class="panel-body">
                        <h2>Heading</h2>
                        <div class="panel-group" id="accordion21">
                            <div class="panel">
                                <div data-toggle="collapse" data-parent="#accordion21" href="#collapseTwoOne">
                                    View details 2.1 &raquo;
                                </div>
                                <div id="collapseTwoOne" class="panel-collapse collapse">
                                    <div class="panel-body">Details 1</div>
                                </div>
                            </div>
                            <div class="panel ">
                                <div data-toggle="collapse" data-parent="#accordion21" href="#collapseTwoTwo">
                                    View details 2.2 &raquo;
                                </div>
                                <div id="collapseTwoTwo" class="panel-collapse collapse">
                                    <div class="panel-body">Details 2</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <div data-toggle="collapse" data-parent="#accordion1" href="#collapseThree">
                        Collapsible Group Item #3
                    </div>
                </h4>
            </div>
            <div id="collapseThree" class="panel-collapse collapse">
                <div class="panel-body">

                    <div class="panel-group" id="accordion2">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <div data-toggle="collapse" data-parent="#accordion2" href="#collapseThreeOne">
                                        Collapsible Group Item #3.1
                                    </div>
                                </h4>
                            </div>
                            <div id="collapseThreeOne" class="panel-collapse collapse in">
                                <div class="panel-body">Panel 3.1</div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <div data-toggle="collapse" data-parent="#accordion2" href="#collapseThreeTwo">
                                        Collapsible Group Item #3.2
                                    </div>
                                </h4>
                            </div>
                            <div id="collapseThreeTwo" class="panel-collapse collapse">
                                <div class="panel-body">Panel 3.2</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

@endsection
@section('extra-css')
    <style>
        #related_parties_accordion{
            width: 100%;
        }
        .panel{
            width: 100%;
        }
        .panel-heading{
            background-color: #DD0033;
            border-radius: 4px;
            margin: 7px;
            padding: 7px;
            font-size: 14px;
            color: #ffffff !important;
        }
        .panel-title{
            margin: 7px;
            padding: 4px;
            font-size: 16px;
            color: #ffffff !important;
            cursor: pointer;
        }

        .panel-body{
            margin-left: 7px;
            margin-right: 7px;
            border-width: 1px;
            border-style: solid;
            border-color: red;
            padding: 10px;
        }

        a.panel-btn{
            color: #ffffff !important;
            border-radius: 4px;
            padding-left: 7px;
            padding-right: 7px;
            background-color: #343a40;
            border-color: #343a40;
            box-shadow: 0 1px 1px rgba(0,0,0,.075);
        }

        a.panel-btn:hover{
            background-color: #000000;
        }

    </style>
@endsection