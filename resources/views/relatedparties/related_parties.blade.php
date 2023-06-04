@php
    $counter = 0;



    function recursiveRelatedParty($related_parties, $client_id, &$counter, $related_party_rarent_id, $process_id, $max_step,$r,$committee,$project,$casenr,$instruction_date){

            if(!isset($related_parties[$related_party_rarent_id])){
                return 0;
            }
            foreach($related_parties[$related_party_rarent_id] as $related_party){
                if(!isset($related_party["id"])){
                    continue;
                }

                $related_party_level_id = isset($related_party->level_id)?$related_party->level_id:-1;

                ++$counter;
@endphp
<li style="width: 100%;display: block;"><span class="parent"><a href="{{route('relatedparty.show',['client_id' => $client_id,'process_id' => $related_party["process_id"],'step_id' => $r,'related_party_id'=>$related_party["related_party_id"]])}}" style="color:#FFFFFF;">{{(isset($related_party["description"])?$related_party["description"]:'').' '.(isset($related_party["company"]) && $related_party["company"] != '' ? '- '.$related_party["company"]. ' - ':'- ').''.(isset($related_party["name"])?$related_party["name"]:'')}}</a>
                            <span style="float: right;text-align:right;padding:0px;">
                                                @if($related_party_level_id != $max_step)<a class="panel-btn btn-secondary" href="javascrip:void(0)" onclick="addRelatedParty({{$client_id}}, {{$related_party["id"]}}, '{{$committee}}','{{$project}}','{{$casenr}}','{{$instruction_date}}')"><i class="fas fa-plus"></i> </a>&nbsp;@endif

                                                    @if((\Request::route()->getName() == 'relatedparty.show') && request()->segment(3) === $related_party["related_party_id"])
                                                        <a href="javascript:void(0)" class="showHideR panel-btn btn-secondary"><i class="fas fa-eye"></i> </a>
                                                    @else
                                                        <a href="{{route('relatedparty.show',['client_id' => $client_id,'process_id' => $related_party["process_id"],'step_id' => $r,'related_party_id'=>$related_party["related_party_id"]])}}" class="panel-btn btn-secondary"><i class="fas fa-eye"></i> </a>
                                                    @endif
                                                    @if($related_party_level_id != $max_step)<a class="panel-btn btn-secondary" href="javascrip:void(0)" onclick="manageRelatedParty({{$related_party["related_party_id"]}})"><i class="fas fa-tasks"></i></a>@endif
                                                    {{--@if($related_party_level_id != $max_step)<a class="panel-btn btn-secondary" href="javascrip:void(0)" onclick="deleteRelatedParty({{$related_party["related_party_id"]}}, {{$related_party["parent_id"]}})"><i class="fas fa-trash"></i></a>@endif--}}</span></span>
    <ul style="width: 100%">
        @php

            if(isset($related_parties[$related_party["id"]]) && isset($related_parties[$related_party["parent_id"]])){
                recursiveRelatedParty($related_parties, $client_id, $counter, $related_party["id"], $process_id, $max_step, $r,$committee,$project,$casenr,$instruction_date);
        }
        @endphp

    </ul>
</li>
@php
    }
}

function recursiveRelatedPartyDropdown($related_parties, $client_id, &$counter, $related_party_rarent_id, $process_id, $max_step,$r,$committee,$project,$casenr,$instruction_date){

            if(!isset($related_parties[$related_party_rarent_id])){
                return 0;
            }
            foreach($related_parties[$related_party_rarent_id] as $related_party){
                if(!isset($related_party["id"])){
                    continue;
                }

                $related_party_level_id = isset($related_party->level_id)?$related_party->level_id:-1;

                ++$counter;

            echo '<option value="'.$related_party["id"].'">'.(isset($related_party["description"])?$related_party["description"]:'').'</option>';


            if(isset($related_parties[$related_party["id"]]) && isset($related_parties[$related_party["parent_id"]])){
                recursiveRelatedPartyDropdown($related_parties, $client_id, $counter, $related_party["id"], $process_id, $max_step, $r, $committee, $project, $casenr, $instruction_date);
            }
        @endphp


@php
    }
}
@endphp
<div class="col-sm-12">
        <h3 class="d-inline">Related Parties</h3>
        <ul id="tree" style="width: 100%">
            <li style="width: 100%;"><span class="parent">@if($client->company != null){{$client->company}}@elseif($client->first_name != null){{$client->first_name}} {{isset($client->last_name)?$client->last_name:''}}@else Not Captured @endif<span style="float: right;text-align:right;padding:0px;width:auto;">

                            <a class="panel-btn btn-secondary" href="javascript:void(0)" onclick="addRelatedParty({{$client->id}}, 0 , '{{($client->committee_id > 0 ? $client->committee_id : 0)}}','{{($client->project_id > 0 ? $client->project->name : '')}}','{{$client->case_number}}','{{$client->instruction_date}}','{{($client->trigger_type_id > 0 ? $client->trigger_type_id : 0)}}')"><i class="fas fa-plus"></i> </a>{{--&nbsp;
                            <a class="panel-btn btn-secondary" href="javascript:void(0)" onclick="copyRelatedParty({{$client->id}}, 0)"><i class="fas fa-copy"></i> </a>--}}

                        </span></span>

            <ul>
                @php
                        $related_party_parent_id = 0;
/*                        echo $casenr;
                        exit;*/
                        recursiveRelatedParty($related_parties, $client_id, $counter, $related_party_parent_id, $process_id, $max_step,$r,$committee,$project,$casenr,$instruction_date);
            @endphp

        </ul>
    </li>
    </ul>
    </div>
<div class="modal fade" id="modalRelatedParties" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="width: 750px;">
            <div class="modal-header text-center" style="border-bottom: 0px;padding:.5rem;">
                <h5 class="modal-title" id="relatedmodalheader"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mx-3">
                <input type="hidden" name="clientid" id="relatedmodalclientid" />
                <input type="hidden" name="relatedpartyid" id="relatedmodalrelatedid" />
                <div class="row step1">
                    <div class="md-form col-sm-12 pb-3 text-left form-inline">
                        <label data-error="wrong" data-success="right" for="defaultForm-pass" class="col-sm-4" style="justify-content: left">Parent Related Parties.</label>
                        <select name="relatedmodalparent" id="relatedmodalparent" class="chosen-select form-control form-control-sm" multiple>
                            <option value="0">{{isset($client->first_name)?$client->first_name:''}} {{isset($client->last_name)?$client->last_name:''}}</option>
                            @php
                                recursiveRelatedPartyDropdown($related_parties, $client_id, $counter, $related_party_parent_id, $process_id, $max_step,$r,$committee,$project,$casenr,$instruction_date);
                            @endphp
                        </select>
                    </div>
                    <div class="md-form col-sm-12 pb-3 text-left form-inline">
                        <label data-error="wrong" data-success="right" for="defaultForm-pass" class="col-sm-4 text-right" style="justify-content: left">Nature of Relationship.</label>
                        <input type="text" id="relatedmodaldescription" name="relatedmodaldescription" class="form-control form-control-sm validate col-sm-8">
                    </div>
                    <div class="md-form col-sm-12 pb-3 text-left form-inline">
                        <label data-error="wrong" data-success="right" for="defaultForm-pass" class="col-sm-4" style="justify-content: left">First Names.</label>
                        <input type="text" id="relatedmodalfirstname" name="relatedmodalfirstname" class="form-control form-control-sm validate col-sm-8">
                    </div>
                    <div class="md-form col-sm-12 pb-3 text-left form-inline">
                        <label data-error="wrong" data-success="right" for="defaultForm-pass" class="col-sm-4" style="justify-content: left">Surame.</label>
                        <input type="text" id="relatedmodallastname" name="relatedmodallastname" class="form-control form-control-sm validate col-sm-8">
                    </div>
                    <div class="md-form col-sm-12 pb-3 text-left form-inline">
                        <label data-error="wrong" data-success="right" for="defaultForm-pass" class="col-sm-4" style="justify-content: left">Initials.</label>
                        <input type="text" id="relatedmodalinitials" name="relatedmodalinitials" class="form-control form-control-sm validate col-sm-8">
                    </div>
                    <div class="md-form col-sm-12 pb-3 text-left form-inline">
                        <label data-error="wrong" data-success="right" for="defaultForm-pass" class="col-sm-4" style="justify-content: left">ID/Passsport Number.</label>
                        <input type="text" id="relatedmodalidnumber" name="relatedmodalidnumber" class="form-control form-control-sm validate col-sm-8">
                    </div>
                    <div class="md-form col-sm-12 pb-3 text-left form-inline">
                        <label data-error="wrong" data-success="right" for="defaultForm-pass" class="col-sm-4" style="justify-content: left">Email.</label>
                        <input type="text" id="relatedmodalemail" name="relatedmodalemail" class="form-control form-control-sm validate col-sm-8">
                    </div>
                    <div class="md-form col-sm-12 pb-3 text-left form-inline">
                        <label data-error="wrong" data-success="right" for="defaultForm-pass" class="col-sm-4" style="justify-content: left">Cellphone Number.</label>
                        <input type="text" id="relatedmodalcontact" name="relatedmodalcontact" class="form-control form-control-sm validate col-sm-8">
                    </div>


                    <div class="md-form mb-4 col-sm-12 text-center">
                        <button class="btn btn-sm btn-default" id="addrelatedsave">Save</button>&nbsp;
                        <button class="btn btn-sm btn-default" id="addrelatedupdate">Update</button>&nbsp;
                        <button class="btn btn-sm btn-default" id="relatedcancel">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalRelatedPartiesCopy" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="width: 750px;">
            <div class="modal-header text-center" style="border-bottom: 0px;padding:.5rem;">
                <h5 class="modal-title" id="copyrelatedmodalheader"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mx-3">
                <input type="hidden" name="copyclientid" id="copyrelatedmodalclientid" />
                <input type="hidden" name="copyrelatedpartyid" id="copyrelatedmodalrelatedid" />
                <div class="row step1">
                    <div class="md-form col-sm-12 pb-3 text-left form-inline">
                        <label data-error="wrong" data-success="right" for="defaultForm-pass" class="col-sm-4" style="justify-content: left">Parent Related Parties.</label>
                        <input type="hidden" name="old_copyrelatedmodalparent" id="old_copyrelatedmodalparent">
                        <select name="copyrelatedmodalparent" id="copyrelatedmodalparent" class="chosen-select form-control form-control-sm" multiple>
                            <option value="0">{{isset($client->first_name)?$client->first_name:''}} {{isset($client->last_name)?$client->last_name:''}}</option>
                            @php
                                recursiveRelatedPartyDropdown($related_parties, $client_id, $counter, $related_party_parent_id, $process_id, $max_step,$r,$committee,$project,$casenr,$instruction_date);
                            @endphp
                        </select>
                    </div>
                    <div class="md-form mb-4 col-sm-12 text-center">
                        <button class="btn btn-sm btn-default" id="addrelatedcopysave">Save</button>&nbsp;
                        <button class="btn btn-sm btn-default" id="relatedcopycancel">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@section('extra-css')
    <link rel="stylesheet" href="{{asset('chosen/chosen.min.css')}}">
    <style>
        #relatedmodalcommitteeautocomplete-list,#relatedmodalprojectautocomplete-list{
            width: 65.3%;
            float: right;
            margin-left: 33.7%;
            margin-top: -1rem;
        }
        .chosen-container{
            width:66% !important;
        }
        #related_parties_accordion{
            width: 100%;
        }

        .panel{
            width: 100%;
            padding-left: 30px;
        }

        .panel-heading{
            background-color: #DD0033;
            border-radius: 4px;
            margin-top: 7px;
            margin-right: 0px;
            margin-bottom: 0px;
            margin-left: 7px;
            padding: 7px 0px 7px 7px;
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
            margin-right: 0px;
            /*border-width: 1px;
            border-style: solid;*/
            border-color: red;
            padding: 10px 0px 10px 10px;
        }

        a.panel-btn{
            color: #ffffff !important;
            border-radius: 4px;
            padding-left: 7px;
            padding-right: 7px;
            /*background-color: #343a40;
            border-color: #343a40;*/
            box-shadow: 0 1px 1px rgba(0,0,0,.075);
            line-height: 1.2rem;
        }

        a.panel-btn:hover{
            background-color: #000000;
        }

    </style>

@endsection
@section('extra-js')
    <script>
        $(function(){

            let projects = [@foreach($projects as $autocomplete_project) {!! '"'.$autocomplete_project->name.'",' !!} @endforeach];

            autocomplete(document.getElementById("relatedmodalproject"), projects);
        })
    </script>
@endsection