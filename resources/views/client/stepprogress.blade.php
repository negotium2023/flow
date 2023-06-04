@extends('client.show')

@section('tab-content')
    <div class="client-detail p-0 h-100">
        <div class="content-container m-0 p-0">
            @yield('header')
            <div class="container-fluid h-100 overflow-hidden">
                @include('client.process')
                <div class="col" style="overflow: auto;
                height: calc(100% - {{($client->crm_id == "5" ? '247px' : ($is_form == 1 ? '280px' : '180px'))}});
                margin-top: 15px;
            overflow-x:hidden;">

                    {{Form::open(['url' => route('clients.storeprogress',$client->id), 'method' => 'post','files'=>true,'id'=>'stepprogress'])}}
                    <input type="hidden" value="{{$is_form}}" name="is_form" id="is_form">
                    <input type="hidden" value="{{implode(',',$step_invisibil)}}" name="step_invisibil" id="step_invisibil">
                    <input type="hidden" value="{{$client->id}}" name="client_id" id="client_id">
                    <input type="hidden" value="{{$step->process_id}}" name="process_id" id="process_id">
                    <input type="hidden" value="{{$step->id}}" name="step_id" id="step_id">
                    <input type="hidden" value="{{$step->id}}" name="active_step_id" id="active_step_id">
                    <input type="hidden" value="{{$step->process_id}}" name="rule_process_id" id="rule_process_id">
                    <input type="hidden" value="{{$step->id}}" name="rule_step_id" id="rule_step_id">
                    @foreach($process_progress as $key => $step)
                        <div id="step_header_{{$step['id']}}" class="col-md-12 p-2 clearfix">
                            <h3 id="{{$step['order']}}" class="d-inline">
                                {{$step['name']}}
                            </h3>
                            {{-- <span class="float-right form-inline clientbasket-all">
                                <span  style="padding-left:8px;"><input type="checkbox" id="select-all" class="form-check-input"> <label for="select-all" class="form-check-label" data-toggle="tooltip" data-html="true" title="Select All/Add All" style="display:inline-block;margin-right:10px;"></label></span>
                            </span> --}}
                            <div class="float-right form-inline">
                                Move to&nbsp;
                                <div id="step_{{$step['id']}}" class=""></div>
                                @if(isset($steps) && isset($client))
                                    <select class="form-control form-control-sm change-step">
                                        @foreach($steps as $step2)
                                            <option value="{{$step2['id']}}" data-path="{{route('clients.progress',$client)}}/{{$step2['id']}}" {{(isset($client) && $client["step_id"] == $step2['id'] ? 'selected' : '')}} onclick="completeStep2({{$step2['id']}})">{{$step2['name']}}</option>
                                        @endforeach
                                    </select>
                                @else

                                @endif
                                &nbsp;or&nbsp;
                                <button type="button" class="btn btn-sm btn-secondary float-right form-inline" onclick="completeStep({{$step['id']}})"><i class="fa fa-check"></i> Auto-complete</button>
                            </div>
                            <input type="hidden" value="{{$max_group}}" name="max_group" id="max_group">
                            
                        </div>
                        @for ($i = 0; $i <= 5; $i++)
                            @if($i == 0 || $i == 1 || $i == 3 || $i == 5) <div class="row" style="width:100%;{{($i > 0 && $i < 5 ? 'margin-left:5.5px !important;' : '')}}"> @endif
                                <div class="grid-{{$i}} {{($i > 0 && $i < 5 ? 'card col-md-6' : 'card col-md-12')}} pb-2 pt-2" style="{{(in_array($i,$grid_array) ? '' : 'display:none;')}}float: left;{{($i > 0 && $i < 5 ? 'max-width:49.5%;margin:1px !important;' : 'max-width: 98.25%;margin: 0px auto !important;')}}">
                                    @foreach($step['activities'] as $activity)

                                        @if($activity['position'] == $i)

                                            @if($activity['type']=='heading')
                                                <span class="list_{{$activity['id']}} group-{{$activity["grouping"]}}" style="{{($activity["grouped"] != null && $activity["grouping"] <= $max_group ? (in_array($activity['id'],$activity_invisibil) ? 'display:none;' : 'display:table;') : 'display:none;')}}width:{{$activity['level']}}%;margin-left: calc(100% - {{$activity['level']}}%);background-color:{{$activity['color'] != 'hsl(0,0%,0%)' ? $activity['color'] : ''}};padding:5px;
                                                {{(isset($activity['styles']['font_size']) ? 'font-size:'.$activity['styles']['font_size'].';' : '')}}
                                                {{(isset($activity['styles']['font_family']) ? 'font-family:'.$activity['styles']['font_family'].';' : '')}}
                                                {{(isset($activity['styles']['bold']) && $activity['styles']['bold'] == 1 ? 'font-weight:bold;' : '')}}
                                                {{(isset($activity['styles']['underline']) && $activity['styles']['underline'] == 1 ? 'text-decoration:underline;' : '')}}
                                                {{(isset($activity['styles']['italic']) && $activity['styles']['italic'] == 1 ? 'font-style:italic;' : '')}}
                                                        ">{{$activity['name']}}</span>
                                                <span class="list_{{$activity['id']}}" style="{{($activity["grouped"] != null && $activity["grouping"] <= $max_group ? (in_array($activity['id'],$activity_invisibil) ? 'display:none;' : 'display:table;') : 'display:none;')}}width:{{$activity['level']}}%;margin-left: calc(100% - {{$activity['level']}}%);padding:5px;">{!! $activity['text_content'] !!}</span>
                                            @elseif($activity['type']=='subheading')
                                                <span class="list_{{$activity['id']}}" style="{{($activity["grouped"] != null && $activity["grouping"] <= $max_group ? (in_array($activity['id'],$activity_invisibil) ? 'display:none;' : 'display:table;') : 'display:none;')}}width:{{$activity['level']}}%;margin-left: calc(100% - {{$activity['level']}}%);background-color:{{$activity['color'] != 'hsl(0,0%,0%)' ? $activity['color'] : ''}};padding:5px;
                                                {{(isset($activity['styles']['font_size']) ? 'font-size:'.$activity['styles']['font_size'].';' : '')}}
                                                {{(isset($activity['styles']['font_family']) ? 'font-family:'.$activity['styles']['font_family'].';' : '')}}
                                                {{(isset($activity['styles']['bold']) && $activity['styles']['bold'] == 1 ? 'font-weight:bold;' : '')}}
                                                {{(isset($activity['styles']['underline']) && $activity['styles']['underline'] == 1 ? 'text-decoration:underline;' : '')}}
                                                {{(isset($activity['styles']['italic']) && $activity['styles']['italic'] == 1 ? 'font-style:italic;' : '')}}
                                                        ">{{$activity['name']}}</span>
                                                <span class="list_{{$activity['id']}}" style="{{($activity["grouped"] != null && $activity["grouping"] <= $max_group ? (in_array($activity['id'],$activity_invisibil) ? 'display:none;' : 'display:table;') : 'display:none;')}}width:{{$activity['level']}}%;margin-left: calc(100% - {{$activity['level']}}%);">{!! $activity['text_content'] !!}</span>
                                            @elseif($activity['type']=='content')
                                                <span style="{{(in_array($activity['id'],$activity_invisibil) ? 'display:none;' : 'display:table;')}}width:{{$activity['level']}}%;margin-left: calc(100% - {{$activity['level']}}%);background-color:{{$activity['color'] != 'hsl(0,0%,0%)' ? $activity['color'] : ''}};padding:5px;
                                                {{(isset($activity['styles']['font_size']) ? 'font-size:'.$activity['styles']['font_size'].';' : '')}}
                                                {{(isset($activity['styles']['bold']) && $activity['styles']['bold'] == 1 ? 'font-weight:bold;' : '')}}
                                                {{(isset($activity['styles']['underline']) && $activity['styles']['underline'] == 1 ? 'text-decoration:underline;' : '')}}
                                                {{(isset($activity['styles']['italic']) && $activity['styles']['italic'] == 1 ? 'font-style:italic;' : '')}}
                                                {{(isset($activity['styles']['font_family']) ? 'font-family:'.$activity['styles']['font_family'].';' : '')}}
                                                        " class="list_{{$activity['id']}}">{{$activity['name']}}</span>
                                                <span style="{{(in_array($activity['id'],$activity_invisibil) ? 'display:none;' : 'display:table;')}}padding:5px;width:{{$activity['level']}}%;margin-left: calc(100% - {{$activity['level']}}%);" class="list_{{$activity['id']}}">{!! $activity['text_content'] !!}</span>
                                            @else
                                                <div id="list_{{$activity['id']}}" class="list-group-item activity group-{{$activity["grouping"]}}" style="{{($activity["grouped"] != null && $activity["grouping"] <= $max_group ? (in_array($activity['id'],$activity_invisibil) ? 'display:none;' : 'display:table;') : 'display:none;')}}width:{{$activity['level']}}%;margin-left: calc(100% - {{$activity['level']}}%);background-color:{{$activity['color'] != 'hsl(0,0%,0%)' ? $activity['color'] : ''}}">
                                                    <div class="display:table-cell">
                                                        @if($activity['type'] == 'dropdown')
                                                            @php

                                                                $arr = (array)$activity['dropdown_items'];
                                                                $arr2 = (array)$activity['dropdown_values'];
                                                                $arr3 = (array)$activity['mirror_value'];

                                                            @endphp
                                                            <input type="hidden" id="old_{{$activity['id']}}" name="old_{{$activity['id']}}" value="{{(!empty($arr2) ? implode(',',$arr2) : old($activity['id']))}}">
                                                        @elseif($activity['type'] == 'amount')
                                                            <input type="hidden" id="old_currency_{{$activity['id']}}" name="old_currency_{{$activity['id']}}" value="{{(isset($activity['currency']) ? $activity['currency'] : old($activity['id']))}}">
                                                            <input type="hidden" id="old_{{$activity['id']}}" name="old_{{$activity['id']}}" value="{{(isset($activity['value']) ? $activity['value'] : old($activity['id']))}}">
                                                        @else
                                                            <input type="hidden" id="old_{{$activity['id']}}" name="old_{{$activity['id']}}" value="{{(isset($activity['value']) ? $activity['value'] : old($activity['id']))}}">
                                                        @endif
                                                        <span style="width:84%;float: left;display:block;">
                                            <span style="
                                                {{(isset($activity['styles']['font_size']) ? 'font-size:'.$activity['styles']['font_size'].';' : '')}}
                                            {{(isset($activity['styles']['font_family']) ? 'font-family:'.$activity['styles']['font_family'].';' : '')}}
                                            {{(isset($activity['styles']['bold']) && $activity['styles']['bold'] == 1 ? 'font-weight:bold;' : '')}}
                                            {{(isset($activity['styles']['underline']) && $activity['styles']['underline'] == 1 ? 'text-decoration:underline;' : '')}}
                                            {{(isset($activity['styles']['italic']) && $activity['styles']['italic'] == 1 ? 'font-style:italic;' : '')}}
                                                    ">{{$activity['name']}}</span> @if($activity['mirror_count'] > 1) <a href="javascript:void(0)" onclick="getMirrorValues({{$client->id}},{{$activity['id']}},{{$activity['type']}})">Change value</a> @endif
                                            <small class="text-muted">{{-- [{{$activity['type_display']}}] --}}@if($activity['kpi']==1) <span class="fa fa-asterisk" title="Activity is required for step completion" style="color:#FF0000"></span> @endif</small>
                                        </span>


                                                        @if($activity['procedure']==1) <span class="badge badge-pill badge-info" title="Activity is required for step completion">Procedure</span> @endif
                                                        @if($activity['avalue']==1) <span class="badge badge-pill badge-secondary" title="Activity is required for step completion">Value</span> @endif
                                                        @if($activity['grouped']==1) <span class="badge badge-pill badge-secondary" title="Activity is required for step completion">Group {{$activity["grouping"]}} Activity</span> @endif
                                                        @if($activity['tooltip'] != null)

                                                            <a href="javascript:void(0)" style="display: inline-block;" class="has-tooltip"><i class="fa fa-info-circle"></i><span class="tooltip2 tooltip-top">{!! $activity['tooltip'] !!}</span></a>

                                                        @endif

                                                        <div style="float: right;margin-right:5px; display: inline-block;margin-top: -3px;padding-bottom: 3px;text-align: right;" class="form-inline">

                                                            <ul class="navbar-nav ml-auto">
                                                                <li class="nav-item dropdown">
                                                                    <a href="javascript:void(0)" style="display: inline-block;color:#000000 !important;margin-top:0px;" data-toggle="tooltip" data-html="true" title="Add Action" onclick="addAction({{$client->id}},{{$activity['id']}},{{$client->process_id}},{{$step['id']}})"><i class="fas fa-user-plus"></i> </a>

                                                                </li>

                                                            </ul>

                                                        </div>
                                                        <div style="float: right;margin-top: -3px;margin-right: 10px;padding-bottom: 3px;padding-left:10px;" class="form-inline clientbasket">

                                                            <input type="checkbox" data-client="{{$client->id}}" class="form-check-input form-inline select-this" id="chk_{{$activity['id']}}" name="add_to_basket[]" value="{{$activity['id']}}" {{ in_array($activity['id'], $in_basket) ? 'checked' : '' }}  style="margin-top:0.4rem">
                                                            <label for="chk_{{$activity['id']}}" data-toggle="tooltip" data-html="true" title="Add To Client Basket" class="form-check-label"></label>

                                                        </div>
                                                        <div style="float: right; display: inline-block;margin-top: -3px;padding-bottom: 3px;text-align: right;">


                                                            @if(isset($activity['comment']) && $activity['comment'] == 1)
                                                                <div style="float: right; display: inline-block;margin-top: 0px;margin-right:5px;padding-bottom: 3px;text-align: right;" class="form-inline">
                                                                    <ul class="navbar-nav ml-auto">
                                                                        <li class="nav-item dropdown">
                                                                            @if(isset($activity_comment[$activity['id']]))

                                                                                <a href="javascript:void(0)"  data-toggle="dropdown" style="display: inline-block;"><i class="far fa-comment-alt" id="comment_count_fa_{{ $activity["id"] }}" {{(isset($activity_comment[$activity['id']]) ? 'style=color:rgba(50,193,75,.7)' : '')}}></i>&nbsp;<span class="badge badge-pill badge-success" id="comment_count_{{ $activity["id"] }}">{{(isset($activity_comment[$activity['id']]) ? $activity_comment[$activity['id']] : '')}}</span><input type="hidden" id="old_comment_count_{{$activity["id"]}}" value="{{$activity_comment[$activity['id']]}}" /></a>
                                                                            @else

                                                                                <a href="javascript:void(0)"  data-toggle="dropdown" style="display: inline-block;"><i class="far fa-comment-alt" id="comment_count_fa_{{ $activity["id"] }}"></i>&nbsp;<span class="badge badge-pill badge-dark" id="comment_count_{{ $activity["id"] }}">0</span><input type="hidden" id="old_comment_count_{{$activity["id"]}}" value="0" /></a>
                                                                            @endif
                                                                            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                                                                <a href="javascript:void(0)" class="dropdown-item" onclick="addComment({{ $client->id }},{{ $activity['id'] }})">
                                                                                    Add Comment
                                                                                </a>
                                                                                {{--<div class="dropdown-divider"></div>--}}
                                                                                <a href="javascript:void(0)" class="dropdown-item" onclick="showComment({{$client->id}},{{$activity['id']}})">
                                                                                    View Comments
                                                                                </a>

                                                                            </div>
                                                                        </li>
                                                                    </ul>

                                                                </div>
                                                            @endif

                                                            @if(isset($activities) && array_key_exists($activity['id'],$activities))
                                                                @php
                                                                    $user_string = '';

                                                                    foreach ($activities[$activity["id"]]["user"] as $user){
                                                                        //foreach ($user as $value){
                                                                            $user_string = $user_string.$user.'<br />';
                                                                        //}
                                                                    }
                                                                @endphp

                                                                <div style="float: right;margin-right:5px; display: inline-block;margin-top: -3px;padding-bottom: 3px;text-align: right;" class="form-inline">
                                                                    <ul class="navbar-nav ml-auto">
                                                                        <li class="nav-item dropdown">
                                                                            <a href="javascript:void(0)" style="display: inline-block;" rel="tooltip" data-original-title="{{ $user_string }}" data-toggle="tooltip" title="{{ $user_string }}"><i class="far fa-user"></i> </a>
                                                                        </li>

                                                                    </ul>

                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="clearfix"></div>

                                                        @if($activity['type']=='date')
                                                            <input name="{{$activity['id']}}" type="date" min="1900-01-01" {{(isset($activity['future_date']) && $activity['future_date'] == '1' ? '' : 'max='.Carbon\Carbon::parse(now())->format("Y-m-d"))}} value="{{(isset($activity['value']) ? $activity['value'] : (old($activity['id']) != null ? old($activity['id']) : $activity['mirror_value']))}}" class="form-control form-control-sm" placeholder="Insert date..." />
                                                        @endif

                                                        @if($activity['type']=='text')
                                                            <input type="text" name="{{$activity['id']}}" {{--id="{{$activity['id']}}" --}}value="{{(isset($activity['value']) && $activity['value'] != '' ? $activity['value'] : (old($activity['id']) != null ? old($activity['id']) : $activity['mirror_value']))}}" class="form-control form-control-sm"  onkeyup="checkDependantActivities({{$activity["id"]}})" />

                                                        @endif

                                                        @if($activity['type']=='percentage')
                                                            <div class="input-group mb-3 input-group-sm">
                                                                <input type="number" min="0" max="100" step="1" name="{{$activity['id']}}" value="{{(isset($activity['value']) ? $activity['value'] : old($activity['id']))}}" class="form-control form-control-sm col-md-1" spellcheck="true" />
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">&percnt;</span>
                                                                    </div>

                                                                </div>

                                                        @endif

                                                        @if($activity['type']=='integer')
                                                            <input type="number" min="0" step="1" name="{{$activity['id']}}" value="{{(isset($activity['value']) ? $activity['value'] : (old($activity['id']) != null ? old($activity['id']) : $activity['mirror_value']))}}" class="form-control form-control-sm" spellcheck="true" />
                                                        @endif

                                                        @if($activity['type']=='amount')
                                                                <div class="input-group" style="width:99%">
                                                                    {{Form::select('currency_'.$activity['id'],$currency_dropdown,($activity["currency"] != null && $activity["currency"] > 0 ? $activity["currency"] : $default_currency),['class'=>'form-control form-control-sm','style'=>'float: left;flex:unset;width:auto;'])}}
                                                                    {{Form::number($activity['id'],(isset($activity['value']) ? $activity['value'] : old($activity['id'])),['class'=>'form-control form-control-sm','placeholder'=>'Insert amount...','style'=>'float: left;width: initial','step'=>'any'])}}
                                                                </div>
                                                                {{--<div class="input-group mb-3 input-group-sm">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">R</span>
                                                                </div>
                                                                <input type="number" min="0" step="1" name="{{$activity['id']}}" value="{{(isset($activity['value']) ? $activity['value'] : (old($activity['id']) != null ? old($activity['id']) : $activity['mirror_value']))}}" class="form-control form-control-sm" spellcheck="true" />
                                                            </div>--}}

                                                        @endif

                                                        @if($activity['type']=='videoyoutube')
                                                            <div class="wrapper" style="position: relative;overflow: hidden;padding-top: 56.25%;">
                                                                {!! $activity['default_value'] !!}

                                                            </div>
                                                        @endif

                                                        @if($activity['type']=='videoupload')
                                                            <video width="100%" height="100%" controls>
                                                                <source src="{{'/storage/files/'.$activity['default_value']}}" type="video/mp4">
                                                                Your browser does not support the video tag.
                                                            </video>
                                                        @endif

                                                        @if($activity['type']=='imageupload')
                                                            <img source src="{{'/storage/files/images/'.strip_tags($activity['default_value'])}}" width="{{$activity['width']}}" height="{{$activity['height']}}" style="
                                                    {{($activity['alignment'] == 'left' ? 'display:block;float:left;' : '')}}
                                                            {{($activity['alignment'] == 'center' ? 'display:block;margin:0px auto;' : '')}}
                                                            {{($activity['alignment'] == 'right' ? 'display:block;float:right;' : '')}}
                                                            {{($activity['alignment'] != 'center' && $activity['alignment'] != 'right' && $activity['alignment'] != 'left' ? 'display:block;margin:0px auto;' : '')}}
                                                                    " />
                                                        @endif

                                                        @if($activity['type']=='textarea')
                                                            <div><textarea onkeyup="checkDependantActivities({{$activity["id"]}})" spellcheck="true" rows="5" name="{{$activity['id']}}" class="form-control form-control-sm text-area">{{(isset($activity['value']) ? $activity['value'] : (isset($activity['default_value']) && $activity['default_value'] != '' ? $activity['default_value'] : (isset($activity['mirror_value']) ? $activity['mirror_value'] : '')))}}</textarea></div>
                                                        @endif

                                                        @if($activity['type']=='boolean')
                                                            <div role="radiogroup">
                                                                <input type="radio" value="1" name="{{$activity["id"]}}" data-id="{{$activity["id"]}}" id="{{$activity["id"]}}-enabled" {{((isset($activity["value"]) && $activity["value"] == 1) || (isset($activity["mirror_value"]) && $activity["mirror_value"] == 1) ? 'checked' : '')}}>
                                                                    <label for="{{$activity["id"]}}-enabled">Yes</label><!-- remove whitespace
                                                                    --><input type="radio" value="0" name="{{$activity["id"]}}" data-id="{{$activity["id"]}}" id="{{$activity["id"]}}-disabled" {{((isset($activity["value"]) && $activity["value"] == 0) || (isset($activity["mirror_value"]) && $activity["mirror_value"] == 1) ? 'checked' : '')}}><!-- remove whitespace
                                                                    --><label for="{{$activity["id"]}}-disabled">No</label>

                                                                    <span class="selection-indicator selection-indicator-{{$activity["id"]}}" @if(!isset($activity["value"]) || $activity["value"] == '') style="opacity:0;" @endif></span>
                                                            </div>
                                                        @endif

                                                        @if($activity['type']=='template_email')
                                                            <div class="row m-0 p-0">
                                                                <div class="col-md-12 input-group">
                                                                    {{Form::select($activity['id'],$templates,(isset($activity['value']) ? $activity['value'] : ''),['class'=>'form-control form-control-sm','placeholder'=>'Please select...','id'=>'template_id_'.$activity['id']])}}
                                                                    <div class="input-group-append">
                                                                        <button type="button" class="btn btn-multiple btn-sm btn-info" title="View Template" data-toggle="tooltip" onclick="viewTemplate({{$activity['id']}})"><i class="fas fa-eye"></i></button>
                                                                        <button type="button" onclick="uploadFile({{$client->id}},{{$activity['id']}},'template')" class="btn btn-multiple btn-sm"><i class="fas fa-paperclip"></i></button>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12 input-group mt-1">
                                                                    @if(auth()->user()->is('admin') || auth()->user()->is('manager'))
                                                                        {{Form::text($activity['id'],(isset($activity["default_value"]) && $activity["default_value"] != null ? $activity["default_value"] : (isset($client->email) ? $client->email : $config->global_send_to_email)),['class'=>'form-control form-control-sm','placeholder'=>'Insert email...','id'=>'template_email_address_'.$activity['id']])}}
                                                                    @else
                                                                        {{Form::hidden($activity['id'],(isset($activity["default_value"]) && $activity["default_value"] != null ? $activity["default_value"] : (isset($client->email) ? $client->email : $config->global_send_to_email)),['class'=>'form-control form-control-sm','placeholder'=>'Insert email...','id'=>'template_email_address_'.$activity['id']])}}
                                                                        {{Form::text($activity['id'],(isset($activity["default_value"]) && $activity["default_value"] != null ? $activity["default_value"] : (isset($client->email) ? $client->email : $config->global_send_to_email)),['class'=>'form-control form-control-sm','placeholder'=>'Insert email...','disabled'])}}
                                                                    @endif
                                                                    <div class="input-group-append" onclick="sendTemplate({{$activity['id']}},{{$client->id}})">
                                                                        <button type="button" class="btn btn-multiple btn-sm btn-success"><i class="fas fa-paper-plane"></i> Send</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div id="message_{{$activity['id']}}"></div>
                                                        @endif

                                                        @if($activity['type']=='document_email')
                                                            <div class="row p-0 m-0">
                                                                <div class="col-md-12 input-group">
                                                                    {{Form::select($activity['id'],$documents,(isset($activity['value']) ? $activity['value'] : ''),['class'=>'form-control form-control-sm','placeholder'=>'Please select...','id'=>'document_id_'.$activity['id']])}}
                                                                    <div class="input-group-append">
                                                                        <button type="button" class="btn btn-multiple btn-sm btn-info" title="View Document" data-toggle="tooltip" onclick="viewDocument({{$activity['id']}})"><i class="fas fa-eye"></i> </button>
                                                                        <button type="button" onclick="uploadFile({{$client->id}},{{$activity['id']}},'document')" class="btn btn-multiple btn-sm" title="Upload Document" data-toggle="tooltip"><i class="fas fa-paperclip"></i> </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12 input-group mt-1">
                                                                @if(auth()->user()->is('admin') || auth()->user()->is('manager'))
                                                                    {{Form::text($activity['id'],(isset($activity["default_value"]) && $activity["default_value"] != null ? $activity["default_value"] : (isset($client->email) && $client->email != '' ? $client->email : $config->global_send_to_email)),['class'=>'form-control form-control-sm','placeholder'=>'Insert email...','id'=>'document_email_address_'.$activity['id']])}}
                                                                @else
                                                                    {{Form::hidden($activity['id'],(isset($activity["default_value"]) && $activity["default_value"] != null ? $activity["default_value"] : (isset($client->email) && $client->email != '' ? $client->email : $config->global_send_to_email)),['class'=>'form-control form-control-sm','placeholder'=>'Insert email...','id'=>'document_email_address_'.$activity['id']])}}
                                                                    {{Form::text($activity['id'],(isset($activity["default_value"]) && $activity["default_value"] != null ? $activity["default_value"] : (isset($client->email) && $client->email != '' ? $client->email : $config->global_send_to_email)),['class'=>'form-control form-control-sm','placeholder'=>'Insert email...','disabled'])}}
                                                                @endif
                                                                <div class="input-group-append" onclick="sendDocument({{$activity['id']}},{{$client->id}})">
                                                                    <button type="button" class="btn btn-multiple btn-sm btn-success" title="Send Document" data-toggle="tooltip"><i class="fas fa-paper-plane"></i> Send</button>
                                                                </div>
                                                            </div>
                                                            <div id="message_{{$activity['id']}}"></div>
                                                        @endif

                                                        @if($activity['type']=='multiple_attachment')
                                                            <div class="row m-0 p-0">
                                                                <div class="col-md-12 p-0">
                                                                    <small class="form-text text-muted">
                                                                        Search and select multiple entries
                                                                    </small>
                                                                </div>
                                                                <div class="col-md-6 p-0 input-group form-group" style="margin-bottom: 0px !important;">
                                                                    {{Form::select('templates_'.$activity['id'],$templates,null,['id'=>'template_id_'.$activity['id'],'class'=>'form-control form-control-sm chosen-select'. ($errors->has('templates_'.$activity['id']) ? ' is-invalid' : ''),'multiple'])}}
                                                                    <div class="input-group-append">
                                                                        <button type="button" onclick="uploadFile({{$client->id}},{{$activity['id']}},'template')" class="btn btn-multiple btn-sm" title="Upload Template" data-toggle="tooltip"><i class="fas fa-paperclip"></i> </button>
                                                                    </div>
                                                                    @foreach($errors->get('templates_'.$activity['id']) as $error)
                                                                        <div class="invalid-feedback">
                                                                            {{ $error }}
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                                <div class="col-md-6 p-0 input-group form-group" style="margin-bottom: 0px !important;">
                                                                    {{Form::select('documents_'.$activity['id'],$document_options,null,['id'=>'document_id_'.$activity['id'],'class'=>'form-control form-control-sm chosen-select'. ($errors->has('documents_'.$activity['id']) ? ' is-invalid' : ''),'multiple'])}}
                                                                    <div class="input-group-append">
                                                                        <button type="button" onclick="uploadFile({{$client->id}},{{$activity['id']}},'document')" class="btn btn-multiple btn-sm" title="Upload Document" data-toggle="tooltip"><i class="fas fa-paperclip"></i> </button>
                                                                    </div>
                                                                    @foreach($errors->get('documents_'.$activity['id']) as $error)
                                                                        <div class="invalid-feedback">
                                                                            {{ $error }}
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                                <div class="col-md-6 p-0">
                                                                    <small id="templates_help" class="form-text text-muted">Templates</small>
                                                                </div>
                                                                <div class="col-md-6 p-0">
                                                                    <small id="documents_help" class="form-text text-muted">Documents</small>
                                                                </div>
                                                                <div class="col-md-12 p-0 input-group mt-1">
                                                                    @if(auth()->user()->is('admin') || auth()->user()->is('manager'))
                                                                        {{Form::text($activity['id'],(isset($activity["default_value"]) && $activity["default_value"] != null ? $activity["default_value"] : (isset($client->email) && $client->email != '' ? $client->email : '')),['class'=>'form-control form-control-sm','placeholder'=>'Insert email...','id'=>'ma_email_address_'.$activity['id']])}}
                                                                    @else
                                                                        {{Form::hidden($activity['id'],(isset($activity["default_value"]) && $activity["default_value"] != null ? $activity["default_value"] : (isset($client->email) && $client->email != '' ? $client->email : '')),['class'=>'form-control form-control-sm','placeholder'=>'Insert email...','id'=>'ma_email_address_'.$activity['id']])}}
                                                                        {{Form::text($activity['id'],(isset($activity["default_value"]) && $activity["default_value"] != null ? $activity["default_value"] : (isset($client->email) && $client->email != '' ? $client->email : '')),['class'=>'form-control form-control-sm','placeholder'=>'Insert email...','disabled'])}}
                                                                    @endif
                                                                    <div class="input-group-append" onclick="sendMultipleDocuments({{$activity['id']}},{{$client->id}})">
                                                                        <button type="button" class="btn btn-multiple btn-sm btn-success"><i class="fas fa-paper-plane"></i> Send</button>
                                                                    </div>

                                                                </div>
                                                                <div class="col-md-12 p-0">
                                                                    <small id="documents_help" class="form-text text-muted"><i class="fa fa-info-circle"></i> Use a comma to seperate multiple email addresses.</small>
                                                                </div>
                                                            </div>
                                                            <div id="message_{{$activity['id']}}"></div>
                                                        @endif

                                                        @if($activity['type']=='document')
                                                            {{Form::file($activity['id'],['class'=>'form-control form-control-sm'. ($errors->has($activity['id']) ? ' is-invalid' : ''),'placeholder'=>'File'])}}
                                                            @foreach($errors->get($activity['id']) as $error)
                                                                <div class="invalid-feedback">
                                                                    {{ $error }}
                                                                </div>
                                                            @endforeach
                                                        @endif

                                                        @if($activity['type']=='dropdown')
{{-- @php var_dump($arr); @endphp --}}
                                                            <select {{($activity["multiple_selection"] == 1 ? 'multiple="multiple"' : '')}} id="{{$activity['id']}}" name="{{$activity["id"]}}[]" class="form-control form-control-sm chosen-select">
                                                                @php
                                                                    echo '<option value="">Please select</option>';
                                                                    foreach((array) $arr as $key => $value){
                                                                        if($activity["multiple_selection"] == 1){
                                                                            echo '<option value="'.$key.'" '.(in_array($key,$arr2) || in_array($value,$arr3) ? 'selected' : '').'>'.$value.'</option>';
                                                                        } else {
                                                                            echo '<option value="'.$key.'" '.((isset($activity["value"]) && $key == $activity["value"]) || in_array($value,$arr3) ? 'selected' : '').'>'.$value.'</option>';
                                                                        }
                                                                        
                                                                    }
                                                                @endphp
                                                            </select>
                                                            <div>
                                                                <small class="form-text text-muted">
                                                                    Search and select multiple entries
                                                                </small>
                                                            </div>
                                                            @foreach($errors->get($activity['id']) as $error)
                                                                <div class="invalid-feedback">
                                                                    {{ $error }}
                                                                </div>
                                                            @endforeach

                                                        @endif

                                                        @if($activity['type']=='notification')
                                                            <div class="row p-0 m-0">
                                                                <div class="col-sm-12">
                                                                    {{Form::select('notification_user_name_'.$activity['id'],$users,isset($activity['user_id'])?$activity['user_id']:null,['id'=>'notification_user_name_'.$activity['id'], 'class'=>'form-control form-control-sm chosen-select'.($errors->has('notification_user_name_'.$activity['id']) ? ' is-invalid' : ''),'multiple'])}}
                                                                    @foreach($errors->get('notification_user_name_'.$activity['id']) as $error)
                                                                        <div class="invalid-feedback">
                                                                            {{ $error }}
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <small class="form-text text-muted">
                                                                        Search and select multiple entries
                                                                    </small>
                                                                </div>
                                                                <div class="col-sm-12" style="margin-top: 10px !important;">
                                                                    <button type="button" class="btn btn-primary btn-sm" onclick="sendNotification({{$activity['id']}})"><i class="fa fa-paper-plane"></i> Send notification</button>
                                                                </div>
                                                            </div>
                                                            <div id="message_{{$activity['id']}}"></div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    @endforeach
                                </div>

                                @if((($i == 0 || $i == 2 || $i == 4 || $i == 5))) </div> @endif
                        @endfor
                        @if($step["group"] != null && $step["group"] > 0)
                            <div style="margin-top:10px;padding-bottom: 40px;">
                                <input type="button" class="btn btn-sm btn-secondary float-right" id="addGroup" value="Add More" style="margin-top:15px;">
                            </div>
                        @endif
                    @endforeach

                    @if(count($process_progress)>0)
                        {{-- @if($step->process_id =='1') --}}
                            
                        {{-- @endif --}}

                        <div class="blackboard-fab  mb-3" style="right:23px;position: absolute;padding-top:15px;">
                            <div class="blackboard-complete-btn" style="display:inline-block;">
                                @if($is_form == 0)
                                    @if(isset($client->completed_at) && $client->completed_at != null)
                                        <a href="javascript:void(0)" onclick="uncomplete({{ $client->id }},{{ $active->id }})" class="uncomplete-btn btn btn-info btn-lg" {{($active->id == $max_step ? 'style=display:block' : 'style=display:none')}}><i class="fa fa-hourglass-end"></i><span style="padding-left:10px;float: right;display:block;text-align:left;">Un-convert</span></a>
                                    @else
                                        <a href="javascript:void(0)" onclick="complete({{ $client->id }},{{ $active->id }})" class="complete-btn btn btn-info btn-lg" {{($active->id == $max_step ? 'style=display:block' : 'style=display:none')}}><i class="fa fa-hourglass-end"></i><span style="padding-left:10px;float: right;display:block;text-align:left;">Convert</span></a>
                                    @endif
                                @else
                                <a href="javascript:void(0)" onclick="generateForm({{ $client->id }},{{ $active->process_id }})" class="complete-btn btn btn-info btn-lg"><i class="far fa-file-alt"></i><span style="padding-left:10px;float: right;display:block;text-align:left;">Generate</span></a>
                                @if($case->signed == 0)
                                <a href="javascript:void(0)" onclick="signForm({{ $case->id }},{{ $client->id }})" class="sign-btn btn btn-info btn-lg" {{($active->id == $max_step ? 'style=display:inline-block' : 'style=display:none')}}><i class="fas fa-pen-alt"></i><span style="padding-left:10px;float: right;display:block;text-align:left;">Sign</span></a>
                                @endif
                                @endif
                            </div>
                            <button type="submit" style="font-size: 18px;line-height: 20px;" class="btn btn-info btn-lg process-submit"><i class="fa fa-save"></i><span style="padding-left:10px;float: right;display:block;text-align:left;">Save</span></button>
                        </div>
                    @endif

                    {{Form::close()}}

                    @include('client.modals.stepprogress')

                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('extra-css')
    <style>
        a:focus{
            outline:none !important;
            border:0px !important;
        }

        .activity a{
            color: rgba(0,0,0,0.5) !important;
        }

        .activity a.dropdown-item {
            color:#212529 !important;
        }

        .btn-comment{
            padding: .25rem .25rem;
            font-size: .575rem;
            line-height: 1;
            border-radius: .2rem;
        }

        .modal-dialog {
            max-width: 700px;
            margin: 1.75rem auto;
            min-width: 500px;
        }

        .modal .chosen-container, .modal .chosen-container-multi{
            width:98% !important;
        }

        .chosen-container, .chosen-container-multi{
            line-height: 30px;
            width:98% !important;
        }

        .modal-open .modal{
            padding-right: 0px !important;
        }

        .progress { position:relative; width:100%; border: 1px solid #7F98B2; padding: 1px; border-radius: 3px; display:none; }
        .bar { background-color: #B4F5B4; width:0%; height:25px; border-radius: 3px; }
        .percent { position:absolute; display:inline-block; top:3px; left:48%; color: #7F98B2;}
    </style>
@endsection
@section('extra-js')

    <script>

        $(document).ready(function (){
            $('#set-mirror-value').on('click',function(){
                let activity_id = $('#modalChangeMirrorValue').find('#mirror-activity').val();
                let mirror_value = $('#modalChangeMirrorValue').find('input[name="mirror-value"]:checked').val();
console.log(mirror_value);
                $('body').find('input[name="'+activity_id+'"]').val(mirror_value);
                $('#modalChangeMirrorValue').modal('hide');

            })

            $('#rulemovetoprocess').on('click',function(){

                $.ajax({
                    type: "POST",
                    url: '/getrule',
                    data: {id:$('#modalRules').find('input[name="ruleid"]:checked').val()},
                    success: function( data ) {
                        if(data.result === 'success') {
                            $(document).find('#rule_process_id').val(data.process_id);
                            $(document).find('#rule_step_id').val(data.step_id);
                            $('#stepprogress').submit();
                        }
                    }
                });
            })

            $('#stayonprocess').on('click',function(){
                $('#modalRules').modal('hide');
                $('#stepprogress').submit();
            })

            $('.process-submit').on('click',function (e) {
                e.preventDefault();

                var formData = new FormData();
                var formDataArray = $('#stepprogress').serializeArray();
                for(var i = 0; i < formDataArray.length; i++){
                    var formDataItem = formDataArray[i];
                    if(formDataItem.name) {
                        formData.append(formDataItem.name, formDataItem.value);
                    }
                }

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: "POST",
                    url: '/rules',
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function( data ) {
                        if(data.result === 'success') {
                            let rules = '';
                            $('#modalRules').modal('show');

                            $.each(data.message, function (index, value) {
                                $.each(value, function (key, val) {
                                    rules = rules + '<div class="col-md-12 form-check"><input type="radio" class="form-check-label mr-3" name="ruleid" id="'+key+'" value="'+key+'"><label class="form-check-label" for="'+key+'">'+val+'</label></div>'
                                });
                            });

                            $('#modalRules').find('.rule-div').html(rules);
                        } else {
                            $('#stepprogress').submit();
                        }
                    }
                });

            })

            $('#modalSendTemplate').on('hidden.bs.modal',function () {
                $('#modalSendTemplate').find('#sendtemplate_step1').show();
                $('#modalSendTemplate').find('#sendtemplate_step2').hide();
                $('#modalSendTemplate').find('#sendtemplate_step3').hide();
                $('#modalSendTemplate').find('#sendtemplateclientid').val('');
                $('#modalSendTemplate').find('#sendtemplateactivityid').val('');
                $('#modalSendTemplate').find('#sendtemplateprocessid').val('');
                $('#modalSendTemplate').find('#sendtemplatestepid').val('');
                $('#modalSendTemplate').find('#template_email').val('');
                $('#modalSendTemplate').find('#email_subject').val('');
                $('#modalSendTemplate').find('#email_address').val('');
                $('#modalSendTemplate').find("#sendtemplatetemplateid").val('');
                $('#modalSendTemplate').find("#sendtemplateemailaddress").val('');
                $('#modalSendDocument').find("#compose_template_email_subject").val('');
                $('#modalSendDocument').find('#compose_template_email_content').val('');
                $('#modalSendTemplate').find('#sendtemplatemessage').html('');
                $('#modalSendTemplate').find('#sendcomposemessage').html('');
                $('#modalSendTemplate').find('#sendtemplatecomposeemailsend').show();
                $('#modalSendTemplate').find('#sendtemplatetemplateemailsend').show();
                $('#modalSendTemplate').find('.sendtemplatecancel').show();
                $('#modalSendTemplate').find('.sendtemplateclose').hide();
                $('#modalSendTemplate').find('#sendtemplatetemplateemailsend').attr("disabled", false);
                $('#modalSendTemplate').find('#sendtemplatecomposeemailsend').attr("disabled", false);
                $('#modalSendTemplate').find('.sendtemplatecancel').attr("disabled", false);
                tinymce.get('compose_template_email_content').setContent('');
            })

            $('#sendtemplatecomposeemail').on('click',function(){
                $('#modalSendTemplate').find('#sendtemplate_step1').hide();
                $('#modalSendTemplate').find('#sendtemplate_step2').show();
                tinymce.init(editor_config);
            })

            $('#sendtemplatetemplateemail').on('click',function(){
                $('#modalSendTemplate').find('#sendtemplate_step1').hide();
                $('#modalSendTemplate').find('#sendtemplate_step3').show();
            })

            $('.sendtemplatecancel').on('click',function(){
                $('#modalSendTemplate').modal('hide');
            })

            $('.sendtemplateclose').on('click',function(){
                $('#modalSendTemplate').modal('hide');
            })

            $('#sendtemplatetemplateemailsend').on('click',function(e){
                e.preventDefault();
                let YOUR_MESSAGE_STRING_CONST = "Are you sure you want to send?";

                $('#modalSendTemplate').find('#sendtemplatetemplateemailsend').attr("disabled", true);
                $('#modalSendTemplate').find('.sendtemplatecancel').attr("disabled", true);
                $('#modalSendTemplate').find('#sendtemplatemessage').html('<span style="color: red;">Sending, please wait ...</span>');
                var data = {
                    email: $('#modalSendTemplate').find("#sendtemplateemailaddress").val(),
                    template_file: $('#modalSendTemplate').find("#sendtemplatetemplateid").val(),
                    subject: $('#modalSendTemplate').find("#email_subject").val(),
                    template_email: $('#modalSendTemplate').find('#template_email').val()
                };

                confirmDialog(YOUR_MESSAGE_STRING_CONST, function() {
                    axios.post('{{route('clients.sendtemplate',$client)}}/' + $('#modalSendTemplate').find('#sendtemplateactivityid').val(), data)
                        .then(function (data) {
                            $('#modalSendTemplate').find('#sendtemplatetemplateemailsend').hide();
                            $('#modalSendTemplate').find('.sendtemplatecancel').hide();
                            $('#modalSendTemplate').find('.sendtemplateclose').show();
                            $('#modalSendTemplate').find('#sendtemplatemessage').html('<span style="color: green">Template sent successfully.</span>');
                        })
                        .catch(function () {
                            $('#modalSendTemplate').find('#sendtemplatetemplateemailsend').attr("disabled", false);
                            $('#modalSendTemplate').find('.sendtemplatecancel').attr("disabled", false);
                            $('#modalSendTemplate').find('#sendtemplatemessage').html('<span style="color: red">There was a problem with this request.</span>');
                        });
                });
            })

            $('#sendtemplatecomposeemailsend').on('click',function(e){
                e.preventDefault();
                let YOUR_MESSAGE_STRING_CONST = "Are you sure you want to send?";

                $('#modalSendTemplate').find('#sendtemplatecomposeemailsend').attr("disabled", true);
                $('#modalSendTemplate').find('.sendtemplatecancel').attr("disabled", true);
                $('#modalSendTemplate').find('#sendcomposemessage').html('<span style="color: red;">Sending, please wait ...</span>');
                var data = {
                    email: $('#modalSendTemplate').find("#sendtemplateemailaddress").val(),
                    template_file: $('#modalSendTemplate').find("#sendtemplatetemplateid").val(),
                    subject: $('#modalSendTemplate').find("#compose_template_email_subject").val(),
                    email_content: $('#modalSendTemplate').find('#compose_template_email_content').val()
                };

                confirmDialog(YOUR_MESSAGE_STRING_CONST, function() {
                    axios.post('{{route('clients.sendtemplate',$client)}}/' + $('#modalSendTemplate').find('#sendtemplateactivityid').val(), data)
                        .then(function (data) {
                            $('#modalSendTemplate').find('#sendtemplatecomposeemailsend').hide();
                            $('#modalSendTemplate').find('.sendtemplatecancel').hide();
                            $('#modalSendTemplate').find('.sendtemplateclose').show();
                            $('#modalSendTemplate').find('#sendcomposemessage').html('<span style="color: green">Template sent successfully.</span>');
                        })
                        .catch(function () {
                            $('#modalSendTemplate').find('#sendtemplatecomposeemailsend').attr("disabled", false);
                            $('#modalSendTemplate').find('.sendtemplatecancel').attr("disabled", false);
                            $('#modalSendTemplate').find('#sendcomposemessage').html('<span style="color: red">There was a problem with this request.</span>');
                        });
                });
            })
        })

        function getMirrorValues(clientid,activity,type){
            $('#modalChangeMirrorValue').modal('show');

            let client_id = clientid;
            let activity_id = activity;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '/clients/'+client_id+'/getactivitymirrorvalues/'+activity_id,
                type:"GET",
                dataType:"json",
                success:function(data){
                    $("#modalChangeMirrorValue").modal('show');
                    $('#modalChangeMirrorValue').find('.overlay').fadeOut();
                    $('#modalChangeMirrorValue').find('#mirror_values').html('');
                    $('#modalChangeMirrorValue').find('#mirror-activity').val(activity_id);
                    $.each(data.mirror_values, function(key1,value1) {
                        console.log(value1);
                        /*$.each(value1.val, function(value) {*/
                        /*$.each(key2, function(value,key) {*/
                            $('#modalChangeMirrorValue').find('#mirror_values').append('<li><input type="radio" value="' + value1 + '" class="mirror-value mr-3" name="mirror-value">' + value1 + '</li>');
                        /*});*/
                        /*});*/
                    });

                }
            });
        }

        function viewEmailTemplate(){

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var id = $('#modalSendTemplate').find('#template_email').val();
            var activity_id = $('#modalSendTemplate').find('#sendtemplateactivityid').val();
            $.ajax({
                url: '/editemail/'+id,
                type:"GET",
                dataType:"json",
                success:function(data){
                    /*$('#modalSendTemplate').modal('hide');*/
                    $("#edit_email_template").modal('show');
                    $("#edit_email_template").find("#email_id").val(data.id);
                    $("#edit_email_template").find("#activity_id").val(activity_id);
                    $("#edit_email_template").find("#email_title").val(data.name);
                    $("#edit_email_template").find("#email_content").val(data.email_content);

                    tinymce.init(editor_config);
                    tinymce.get('email_content').setContent(data.email_content);
                }
            });

        };

        function saveEmailTemplate(){
            var id = $("#edit_email_template").find("#email_id").val();
            var name = $("#edit_email_template").find("#email_title").val();
            var email_content = $("#edit_email_template").find("#email_content").val();
            $.ajax({
                url: '/updateemail/'+id,
                type:"POST",
                data:{name:name, email_content:email_content},
                success:function(data){
                    $("#edit_email_template").modal('hide');
                    $("#edit_email_template").find("#email_id").val('');
                    $("#edit_email_template").find("#activity_id").val('');
                    $("#edit_email_template").find("#email_title").val('');
                    $("#edit_email_template").find("#email_content").val('');
                    tinymce.get('email_content').setContent('');
                }
            });
        }

        function getSubject(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var id = $('#modalSendTemplate').find('#template_email').val();
            $.ajax({
                url: '/getsubject/'+id,
                type:"POST",
                data:{id:id},
                success:function(data){
                    $('#modalSendTemplate').find('#email_subject').val(data.email_subject);
                }
            });
        }

        function sendTemplate(activity,clientid){
            $('#message_' + activity).html('');

            if ($("select[name=" + activity + "]").val() == "") {
                $('#message_' + activity).html('<span style="color: red;">Please select template</span>');
                return;
            }

            $('#modalSendTemplate').modal('show');
            $('#modalSendTemplate').find('#sendtemplateactivityid').val(activity);
            $('#modalSendTemplate').find('#sendtemplateclientid').val(clientid);
            $('#modalSendTemplate').find('#sendtemplateprocessid').val($('#process_id').val());
            $('#modalSendTemplate').find('#sendtemplatestepid').val($('#step_id').val());
            $('#modalSendTemplate').find('#sendtemplateemailaddress').val($('#template_email_address_'+activity).val());
            $('#modalSendTemplate').find('#sendtemplatetemplateid').val($('#template_id_'+activity).val());
        }

        function viewTemplate(activity) {

            $('#message_' + activity).html('');

            if ($("select[name=" + activity + "]").val() == "") {
                $('#message_' + activity).html('<span style="color: red;">Please select template</span>');
                return;
            }

            var template = $("select[name=" + activity + "]").val();

            window.location.href = "{{ route('clients.viewtemplate', $client)}}/" + template;

        }
    </script>
    <script>
        $(document).ready(function (){
            $('#modalSendDocument').on('hidden.bs.modal',function () {
                $('#modalSendDocument').find('#senddocument_step1').show();
                $('#modalSendDocument').find('#senddocument_step2').hide();
                $('#modalSendDocument').find('#senddocument_step3').hide();
                $('#modalSendDocument').find('#senddocumentclientid').val('');
                $('#modalSendDocument').find('#senddocumentactivityid').val('');
                $('#modalSendDocument').find('#senddocumentprocessid').val('');
                $('#modalSendDocument').find('#senddocumentstepid').val('');
                $('#modalSendDocument').find('#document_email').val('');
                $('#modalSendDocument').find('#document_email_subject').val('');
                $('#modalSendDocument').find('#document_email_address').val('');
                $('#modalSendDocument').find("#senddocumenttemplateid").val('');
                $('#modalSendDocument').find("#senddocumentemailaddress").val('');
                $('#modalSendDocument').find("#compose_document_email_subject").val('');
                $('#modalSendDocument').find('#compose_document_email_content').val('');
                $('#modalSendDocument').find('#senddocumentmessage').html('');
                $('#modalSendDocument').find('#sendcomposemessaged').html('');
                $('#modalSendDocument').find('#senddocumentcomposeemailsend').show();
                $('#modalSendDocument').find('#senddocumenttemplateemailsend').show();
                $('#modalSendDocument').find('.senddocumentcancel').show();
                $('#modalSendDocument').find('.senddocumentclose').hide();
                $('#modalSendDocument').find('#senddocumenttemplateemailsend').attr("disabled", false);
                $('#modalSendDocument').find('#senddocumentcomposeemailsend').attr("disabled", false);
                $('#modalSendDocument').find('.senddocumentcancel').attr("disabled", false);
                tinymce.get('compose_document_email_content').setContent('');
            })

            $('#senddocumentcomposeemail').on('click',function(){
                $('#modalSendDocument').find('#senddocument_step1').hide();
                $('#modalSendDocument').find('#senddocument_step2').show();
                tinymce.init(editor_config);
            })

            $('#senddocumenttemplateemail').on('click',function(){
                $('#modalSendDocument').find('#senddocument_step1').hide();
                $('#modalSendDocument').find('#senddocument_step3').show();
            })

            $('.senddocumentcancel').on('click',function(){
                $('#modalSendDocument').modal('hide');
            })

            $('.senddocumentclose').on('click',function(){
                $('#modalSendDocument').modal('hide');
            })

            $('#senddocumenttemplateemailsend').on('click',function(e){
                e.preventDefault();
                let YOUR_MESSAGE_STRING_CONST = "Are you sure you want to send?";

                $('#modalSendDocument').find('#senddocumenttemplateemailsend').attr("disabled", true);
                $('#modalSendDocument').find('.senddocumentcancel').attr("disabled", true);
                $('#modalSendDocument').find('#senddocumentmessage').html('<span style="color: red;">Sending, please wait ...</span>');
                var data = {
                    email: $('#modalSendDocument').find("#senddocumentemailaddress").val(),
                    document_file: $('#modalSendDocument').find("#senddocumenttemplateid").val(),
                    subject: $('#modalSendDocument').find("#document_email_subject").val(),
                    template_email: $('#modalSendDocument').find('#document_email').val()
                };

                confirmDialog(YOUR_MESSAGE_STRING_CONST, function() {
                    axios.post('{{route('clients.senddocument',$client)}}/' + $('#modalSendDocument').find('#senddocumentactivityid').val(), data)
                        .then(function (data) {
                            $('#modalSendDocument').find('#senddocumenttemplateemailsend').hide();
                            $('#modalSendDocument').find('.senddocumentcancel').hide();
                            $('#modalSendDocument').find('.senddocumentclose').show();
                            $('#modalSendDocument').find('#senddocumentmessage').html('<span style="color: green">Template sent successfully.</span>');
                        })
                        .catch(function () {
                            $('#modalSendDocument').find('#senddocumenttemplateemailsend').attr("disabled", false);
                            $('#modalSendDocument').find('.senddocumentcancel').attr("disabled", false);
                            $('#modalSendDocument').find('#senddocumentmessage').html('<span style="color: red">There was a problem with this request.</span>');
                        });
                });
            })

            $('#senddocumentcomposeemailsend').on('click',function(e){
                e.preventDefault();
                let YOUR_MESSAGE_STRING_CONST = "Are you sure you want to send?";

                $('#modalSendDocument').find('#senddocumentcomposeemailsend').attr("disabled", true);
                $('#modalSendDocument').find('.senddocumentcancel').attr("disabled", true);
                $('#modalSendDocument').find('#sendcomposemessaged').html('<span style="color: red;">Sending, please wait ...</span>');
                var data = {
                    email: $('#modalSendDocument').find("#senddocumentemailaddress").val(),
                    document_file: $('#modalSendDocument').find("#senddocumenttemplateid").val(),
                    subject: $('#modalSendDocument').find("#compose_document_email_subject").val(),
                    email_content: $('#modalSendDocument').find('#compose_document_email_content').val()
                };

                confirmDialog(YOUR_MESSAGE_STRING_CONST, function() {
                    axios.post('{{route('clients.senddocument',$client)}}/' + $('#modalSendDocument').find('#senddocumentactivityid').val(), data)
                        .then(function (data) {
                            $('#modalSendDocument').find('#senddocumentcomposeemailsend').hide();
                            $('#modalSendDocument').find('.senddocumentcancel').hide();
                            $('#modalSendDocument').find('.senddocumentclose').show();
                            $('#modalSendDocument').find('#sendcomposemessaged').html('<span style="color: green">Template sent successfully.</span>');
                        })
                        .catch(function () {
                            $('#modalSendDocument').find('#senddocumentcomposeemailsend').attr("disabled", false);
                            $('#modalSendDocument').find('.senddocumentcancel').attr("disabled", false);
                            $('#modalSendDocument').find('#sendcomposemessaged').html('<span style="color: red">There was a problem with this request.</span>');
                        });
                });
            })
        })

        function viewEmailDocument(){

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var id = $('#modalSendDocument').find('#document_email').val();
            var activity_id = $('#modalSendDocument').find('#senddocumentactivityid').val();
            $.ajax({
                url: '/editemail/'+id,
                type:"GET",
                dataType:"json",
                success:function(data){
                    /*$('#modalSendTemplate').modal('hide');*/
                    $("#edit_email_template").modal('show');
                    $("#edit_email_template").find("#email_id").val(data.id);
                    $("#edit_email_template").find("#activity_id").val(activity_id);
                    $("#edit_email_template").find("#email_title").val(data.name);
                    $("#edit_email_template").find("#email_content").val(data.email_content);

                    tinymce.init(editor_config);
                    tinymce.get('email_content').setContent(data.email_content);
                }
            });

        };

        function getDocumentSubject(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var id = $('#modalSendDocument').find('#document_email').val();
            $.ajax({
                url: '/getsubject/'+id,
                type:"POST",
                data:{id:id},
                success:function(data){
                    $('#modalSendDocument').find('#document_email_subject').val(data.email_subject);
                }
            });
        }

        function sendDocument(activity,clientid){
            $('#message_' + activity).html('');

            if ($("select[name=" + activity + "]").val() == "") {
                $('#message_' + activity).html('<span style="color: red;">Please select template</span>');
                return;
            }

            $('#modalSendDocument').modal('show');
            $('#modalSendDocument').find('#senddocumentactivityid').val(activity);
            $('#modalSendDocument').find('#senddocumentclientid').val(clientid);
            $('#modalSendDocument').find('#senddocumentprocessid').val($('#process_id').val());
            $('#modalSendDocument').find('#senddocumentstepid').val($('#step_id').val());
            $('#modalSendDocument').find('#senddocumentemailaddress').val($('#document_email_address_'+activity).val());
            $('#modalSendDocument').find('#senddocumenttemplateid').val($('#document_id_'+activity).val());
        }

        function viewDocument(activity) {

            $('#message_' + activity).html('');

            if ($("select[name=" + activity + "]").val() == "") {
                $('#message_' + activity).html('<span style="color: red;">Please select document</span>');
                return;
            }

            var document = $("select[name=" + activity + "]").val();

            window.location.href = "{{ route('clients.viewdocument', $client)}}/" + document;

        }
    </script>
    <script>
        $(document).ready(function (){
            $('#modalSendMA').on('hidden.bs.modal',function () {
                $('#modalSendMA').find('#sendma_step1').show();
                $('#modalSendMA').find('#sendma_step2').hide();
                $('#modalSendMA').find('#sendma_step3').hide();
                $('#modalSendMA').find('#sendmaclientid').val('');
                $('#modalSendMA').find('#sendmaactivityid').val('');
                $('#modalSendMA').find('#sendmaprocessid').val('');
                $('#modalSendMA').find('#sendmastepid').val('');
                $('#modalSendMA').find('#ma_email').val('');
                $('#modalSendMA').find('#ma_email_subject').val('');
                $('#modalSendMA').find('#ma_email_address').val('');
                $('#modalSendMA').find("#sendmatemplateid").val('');
                $('#modalSendMA').find("#sendmadocumentid").val('');
                $('#modalSendMA').find("#sendmaemailaddress").val('');
                $('#modalSendMA').find("#compose_ma_email_subject").val('');
                $('#modalSendMA').find('#compose_ma_email_content').val('');
                $('#modalSendMA').find('#sendmamessage').html('');
                $('#modalSendMA').find('#sendcomposemessagema').html('');
                $('#modalSendMA').find('#sendmacomposeemailsend').show();
                $('#modalSendMA').find('#sendmatemplateemailsend').show();
                $('#modalSendMA').find('.sendmacancel').show();
                $('#modalSendMA').find('.sendmaclose').hide();
                $('#modalSendMA').find('#sendmatemplateemailsend').attr("disabled", false);
                $('#modalSendMA').find('#sendmacomposeemailsend').attr("disabled", false);
                $('#modalSendMA').find('.sendmacancel').attr("disabled", false);
                tinymce.get('compose_ma_email_content').setContent('');
            })

            $('#sendmacomposeemail').on('click',function(){
                $('#modalSendMA').find('#sendma_step1').hide();
                $('#modalSendMA').find('#sendma_step2').show();
                tinymce.init(editor_config);
            })

            $('#sendmatemplateemail').on('click',function(){
                $('#modalSendMA').find('#sendma_step1').hide();
                $('#modalSendMA').find('#sendma_step3').show();
            })

            $('.sendmacancel').on('click',function(){
                $('#modalSendMA').modal('hide');
            })

            $('.sendmaclose').on('click',function(){
                $('#modalSendMA').modal('hide');
            })

            $('#sendmatemplateemailsend').on('click',function(e){
                e.preventDefault();
                let YOUR_MESSAGE_STRING_CONST = "Are you sure you want to send?";

                $('#modalSendMA').find('#sendmatemplateemailsend').attr("disabled", true);
                $('#modalSendMA').find('.sendmacancel').attr("disabled", true);
                $('#modalSendMA').find('#sendmamessage').html('<span style="color: red;">Sending, please wait ...</span>');
                var data = {
                    email: $('#modalSendMA').find("#sendmaemailaddress").val(),
                    templates: $('#modalSendMA').find("#sendmatemplateid").val(),
                    documents: $('#modalSendMA').find("#sendmadocumentid").val(),
                    subject: $('#modalSendMA').find("#ma_email_subject").val(),
                    template_email: $('#modalSendMA').find('#ma_email').val()
                };

                confirmDialog(YOUR_MESSAGE_STRING_CONST, function(){
                    axios.post('{{route('clients.senddocuments',$client)}}/' + $('#modalSendMA').find('#sendmaactivityid').val(), data)
                        .then(function (data) {
                            $('#modalSendMA').find('#sendmatemplateemailsend').hide();
                            $('#modalSendMA').find('.sendmacancel').hide();
                            $('#modalSendMA').find('.sendmaclose').show();
                            $('#modalSendMA').find('#sendmamessage').html('<span style="color: green">Template sent successfully.</span>');
                        })
                        .catch(function () {
                            $('#modalSendMA').find('#sendmatemplateemailsend').attr("disabled", false);
                            $('#modalSendMA').find('.sendmacancel').attr("disabled", false);
                            $('#modalSendMA').find('#sendmamessage').html('<span style="color: red">There was a problem with this request.</span>');
                        });
                });
            })

            $('#sendmacomposeemailsend').on('click',function(e){
                e.preventDefault();
                let YOUR_MESSAGE_STRING_CONST = "Are you sure you want to send?";

                $('#modalSendMA').find('#sendmacomposeemailsend').attr("disabled", true);
                $('#modalSendMA').find('.sendmacancel').attr("disabled", true);
                $('#modalSendMA').find('#sendcomposemessagema').html('<span style="color: red;">Sending, please wait ...</span>');
                var data = {
                    email: $('#modalSendMA').find("#sendmaemailaddress").val(),
                    templates: $('#modalSendMA').find("#sendmatemplateid").val(),
                    documents: $('#modalSendMA').find("#sendmadocumentid").val(),
                    subject: $('#modalSendMA').find("#compose_ma_email_subject").val(),
                    email_content: $('#modalSendMA').find('#compose_ma_email_content').val()
                };

                confirmDialog(YOUR_MESSAGE_STRING_CONST, function(){
                    axios.post('{{route('clients.senddocuments',$client)}}/' + $('#modalSendMA').find('#sendmaactivityid').val(), data)
                        .then(function (data) {
                            $('#modalSendMA').find('#sendmacomposeemailsend').hide();
                            $('#modalSendMA').find('.sendmacancel').hide();
                            $('#modalSendMA').find('.sendmaclose').show();
                            $('#modalSendMA').find('#sendcomposemessagema').html('<span style="color: green">Template sent successfully.</span>');
                        })
                        .catch(function () {
                            $('#modalSendMA').find('#sendmacomposeemailsend').attr("disabled", false);
                            $('#modalSendMA').find('.sendmacancel').attr("disabled", false);
                            $('#modalSendMA').find('#sendcomposemessagema').html('<span style="color: red">There was a problem with this request.</span>');
                        });
                });
            })
        })

        function viewEmailMA(){

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var id = $('#modalSendMA').find('#ma_email').val();
            var activity_id = $('#modalSendMA').find('#sendmaactivityid').val();
            $.ajax({
                url: '/editemail/'+id,
                type:"GET",
                dataType:"json",
                success:function(data){
                    /*$('#modalSendTemplate').modal('hide');*/
                    $("#edit_email_template").modal('show');
                    $("#edit_email_template").find("#email_id").val(data.id);
                    $("#edit_email_template").find("#activity_id").val(activity_id);
                    $("#edit_email_template").find("#email_title").val(data.name);
                    $("#edit_email_template").find("#email_content").val(data.email_content);

                    tinymce.init(editor_config);
                    tinymce.get('email_content').setContent(data.email_content);
                }
            });

        };

        function getMASubject(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var id = $('#modalSendMA').find('#ma_email').val();
            $.ajax({
                url: '/getsubject/'+id,
                type:"POST",
                data:{id:id},
                success:function(data){
                    $('#modalSendMA').find('#ma_email_subject').val(data.email_subject);
                }
            });
        }

        function sendMultipleDocuments(activity,clientid) {
            if ($('#ma_templates_id_'+activity).val() == "" && $('#ma_documents_id_'+activity).val() == "") {
                $('#message_' + activity).html('<span style="color: red;">Please select at least one template or document</span>');
                return;
            } else {
                $('#message_' + activity).html('');
            }

            $('#modalSendMA').modal('show');
            $('#modalSendMA').find('#sendmaactivityid').val(activity);
            $('#modalSendMA').find('#sendmaclientid').val(clientid);
            $('#modalSendMA').find('#sendmaprocessid').val($('#process_id').val());
            $('#modalSendMA').find('#sendmastepid').val($('#step_id').val());
            $('#modalSendMA').find('#sendmaemailaddress').val($('#ma_email_address_'+activity).val());
            $('#modalSendMA').find('#sendmatemplateid').val($('#template_id_'+activity).val());
            $('#modalSendMA').find('#sendmadocumentid').val($('#document_id_'+activity).val());
        }
    </script>
    <script>
        function uploadFile(clientid,activityid,activitytype){
            $('#modalFileUpload').modal('show');
            $('#modalFileUpload').find('#fileuploadclientid').val(clientid);
            $('#modalFileUpload').find('#fileuploadactivityid').val(activityid);
            $('#modalFileUpload').find('#fileuploadactivitytype').val(activitytype);
        }

        $(document).ready(function(){

            $('#modalFileUpload').on('hidden.bs.modal',function () {
                $('#modalFileUpload').find('#fileuploadclientid').val('');
                $('#modalFileUpload').find('#fileuploadactivityid').val('');
                $('#modalFileUpload').find('#filename').val('');
                $('#modalFileUpload').find('#fileupload').val('');
            })

            $('#fileuploadcancel').on('click',function(){
                $('#modalFileUpload').modal('hide');
            })

            $('#upload_form').on('submit', function(event){

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                event.preventDefault();
                $.ajax({
                    url:"{{ route('ajaxupload.action') }}",
                    method:"POST",
                    data:new FormData(this),
                    dataType:'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success:function(data)
                    {
                        $('#message').css('display', 'block');
                        $('#message').html(data.message);
                        $('#message').addClass('alert');
                        $('#message').addClass(data.class_name);
                        if(data.class_name === 'alert-success' && $('#modalFileUpload').find('#fileuploadactivitytype').val() === 'document') {
                            $('body').find('#document_id_' + $('#modalFileUpload').find('#fileuploadactivityid').val()).append('<option value="' + data.template_id + '">' + data.template_name + '</option>');
                            $('body').find('#document_id_' + $('#modalFileUpload').find('#fileuploadactivityid').val() + ' option[value="' + data.template_id + '"]').prop('selected',true);
                            $('body').find('#document_id_' + $('#modalFileUpload').find('#fileuploadactivityid').val()).trigger('chosen:updated');
                            $('#modalFileUpload').modal('hide');
                        }
                        if(data.class_name === 'alert-success' && $('#modalFileUpload').find('#fileuploadactivitytype').val() === 'template') {
                            $('body').find('#template_id_' + $('#modalFileUpload').find('#fileuploadactivityid').val()).append('<option value="' + data.template_id + '">' + data.template_name + '</option>');
                            $('body').find('#template_id_' + $('#modalFileUpload').find('#fileuploadactivityid').val() + ' option[value="' + data.template_id + '"]').prop('selected',true);
                            $('body').find('#template_id_' + $('#modalFileUpload').find('#fileuploadactivityid').val()).trigger('chosen:updated');
                            $('#modalFileUpload').modal('hide');
                        }

                    }
                })
            });

        });
    </script>
    <script>
        $(document).ready(function (){

            $('#addGroup').on('click', function() {
                //var cur = $(this).attr('class').match(/\d+$/)[0];
                let cur = parseInt($("#max_group").val());
                let next = cur+1;
                $('.group-'+next).css('display','table');
                $("#max_group").val(next)
            });

            $('#modalAddAction').on('hidden.bs.modal', function () {

                $('#modalAddAction').find('#addactionuserids').val('').trigger('chosen:updated');
                $('#modalAddAction').find('#addactionduedate').val('');
            })

            var spinner = '<svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"\n' +
                '     width="40px" height="40px" preserveAspectRatio="xMaxYMax meet" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;">\n' +
                '  <path fill="#fff" d="M43.935,25.145c0-10.318-8.364-18.683-18.683-18.683c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615c8.072,0,14.615,6.543,14.615,14.615H43.935z">\n' +
                '    <animateTransform attributeType="xml"\n' +
                '      attributeName="transform"\n' +
                '      type="rotate"\n' +
                '      from="0 25 25"\n' +
                '      to="360 25 25"\n' +
                '      dur="1s"\n' +
                '      repeatCount="indefinite"/>\n' +
                '    </path>\n' +
                '  </svg>';

            //open move to process modal
            $('.move-to-process-btn').on('click',function(){
                $('#modalChangeProcess').modal('show');
            })

            $('#modalChangeProcess').on('hidden.bs.modal', function () {
                $('#modalChangeProcess').find('#changeprocessradio_msg').html('');
                $('#modalChangeProcess').find('#move_to_process_new_msg').html('');
                $('#modalChangeProcess').find('#move_to_process_new').removeClass('is-invalid');
            })
            //close move to process modal
            $('#changeprocesscancel').on('click',function(){
                $('#modalChangeProcess').modal('hide');
            })

            //move to process depending on radio selection
            /*$('#changeprocesssave').on('click',function(){
                let err = 0;

                if($('#modalChangeProcess').find('#move_to_process_new').val() === '0'){
                    err++;
                    $('#modalChangeProcess').find('#move_to_process_new').addClass('is-invalid');
                    $('#modalChangeProcess').find('#move_to_process_new_msg').html('<span style="color: red;">Please select a process.</span>');
                }


                let process_action = 'keep';
                //get value of radio button in modal
                if(err === 0) {
                    $('#modalChangeProcess').find('#changeprocessradio_msg').html('');
                    $('#modalChangeProcess').find('#move_to_process_new_msg').html('');

                    if (process_action === 'keep') {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });

                        let client_id = {{$client->id}};
                        let process_id = {{$client->process_id}};
                        let new_process_id = $('#modalChangeProcess').find('#move_to_process_new').val();

                        $.ajax({
                            url: '/clients/' + client_id + '/keep_process/' + process_id + '/' + new_process_id,
                            type: "GET",
                            dataType: "json",
                            success: function (data) {
                                $("#modalChangeProcess").modal('hide');

                                window.location.href = '/clients/' + client_id + '/progress/' + new_process_id + '/' + data.new_step_id + '/0';

                            }
                        });
                    }
                }
            })*/

            $('#changeconvertdate').on('click',function(){
                $("#modalUnconvert .step1").css('display','none');
                $("#modalUnconvert .step2").css('display','block');
            });

            $('#changeconvertdatecancel').on('click',function(){
                $("#modalUnconvert .step2").css('display','none');
                $("#modalUnconvert .step1").css('display','block');
                $("#modalUnconvert").modal('hide');
            });

            $('#modalConvert #convertdatecancel').on('click',function(){
                $("#modalConvert").modal('hide');
            });

            $('#modalUnconvert #unconvert').on('click',function(){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var clientid = $("#modalUnconvert").find("#unconvertclientid").val();
                var activeid = $("#modalUnconvert").find("#unconvertactiveid").val();
                $.ajax({
                    url: '/clients/' + clientid + '/uncompleted/'+ activeid,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $("#modalUnconvert .step2").css('display', 'none');
                        $("#modalUnconvert .step1").css('display', 'block');
                        $("#modalUnconvert").modal('hide');

                        $(".uncomplete-btn").css('display','none');
                        $(".complete-btn").css('display','block');
                        $('.flash_msg').html('<div class="alert alert-success alert-dismissible blackboard-alert">\n' +
                            '                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>\n' +
                            '                    <strong>Success!</strong> Client successfully unconverted.\n' +
                            '                </div>');
                    }
                });


            });

            $('#changeconvertdatesave').on('click',function(){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var clientid = $("#modalUnconvert").find("#unconvertclientid").val();
                var activeid = $("#modalUnconvert").find("#unconvertactiveid").val();
                var newdate = $("#modalUnconvert").find("#newconvertdate").val();

                if(newdate === ''){
                    $("#modalUnconvert").find("#newconvertdate").addClass('is-invalid');
                } else {
                    $("#modalUnconvert").find("#newconvertdate").removeClass('is-invalid');

                    $.ajax({
                        url: '/clients/' + clientid + '/changecompleted/' + activeid + '/' + newdate,
                        type: "GET",
                        dataType: "json",
                        success: function (data) {
                            $("#modalUnconvert .step2").css('display', 'none');
                            $("#modalUnconvert .step1").css('display', 'block');
                            $("#modalUnconvert").modal('hide');

                            $(".uncomplete-btn").css('display','block');
                            $(".complete-btn").css('display','none');
                            $('.flash_msg').html('<div class="alert alert-success alert-dismissible blackboard-alert">\n' +
                                '                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>\n' +
                                '                    <strong>Success!</strong> Converted date successfully changed.\n' +
                                '                </div>');
                        }
                    });
                }


            });

            $('#convertdatesave').on('click',function(){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var clientid = $("#modalConvert").find("#convertclientid").val();
                var activeid = $("#modalConvert").find("#convertactiveid").val();
                var newdate = $("#modalConvert").find("#convertdate").val();

                if(newdate === ''){
                    $("#modalConvert").find("#convertdate").addClass('is-invalid');
                } else {
                    $("#modalConvert").find("#convertdate").removeClass('is-invalid');

                    $.ajax({
                        url: '/clients/' + clientid + '/completed/' + activeid + '/' + newdate,
                        type: "GET",
                        dataType: "json",
                        success: function (data) {
                            $("#modalConvert").modal('hide');

                            $(".uncomplete-btn").css('display','block');
                            $(".complete-btn").css('display','none');
                            $('.flash_msg').html('<div class="alert alert-success alert-dismissible blackboard-alert">\n' +
                                '                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>\n' +
                                '                    <strong>Success!</strong> Client successfully converted\n' +
                                '                </div>');

                        }
                    });
                }


            });

            $('#addcommentsave').on('click',function(){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var clientid = $("#modalAddComment").find("#addcommentclientid").val();
                var activityid = $("#modalAddComment").find("#addcommentactivityid").val();
                var comment = $("#modalAddComment").find("#addcommentcomment").val();
                var privatec = 0;

                if($("#modalAddComment").find("#addcommentprivatec").is(':checked')){
                    privatec = 1;
                }

                $.ajax({
                    type: "POST",
                    url: '/clients/'+clientid+'/addcomment/'+activityid,
                    data: {clientid: clientid, activityid: activityid, comment: comment,privatec:privatec},
                    success: function( data ) {

                        $("#modalAddComment").modal('hide');
                        tinyMCE.activeEditor.setContent('');
                        $("#modalAddComment").find("#addcommentcomment").val();
                        $("#modalAddComment").find("#addcommentprivatec").prop('checked',false);
                        $("#modalAddComment").find("#addcommentclientid").val(clientid);
                        $("#modalAddComment").find("#addcommentactivityid").val(activityid);

                        let count = parseInt($("#old_comment_count_"+activityid).val());

                        if(count === 0){
                            $("#comment_count_"+activityid).removeClass('badge-dark');
                            $("#comment_count_"+activityid).addClass('badge-success');
                        }
                        count++;
                        $("#comment_count_"+activityid).html(count);
                        $("#old_comment_count_"+activityid).val(count);
                        $("#comment_count_fa_"+activityid).css('color','rgba(50, 193, 75, 0.7)');


                        //window.location.reload();
                    }
                });
            });

            $('#addcommentcancel').on('click',function(){
                $("#modalAddComment").modal('hide');
            });

            $('#showcommentcancel').on('click',function(){
                $("#modalShowComment").find("#showcommentcomment").empty();
                $("#modalShowComment").modal('hide');
            });

            $('#editcommentcancel').on('click',function(){
                $("#modalEditComment").find("#editcommentcomment").empty();
                $("#modalEditComment").modal('hide');
            });

            $('#addactionsave').on('click',function () {

                let process_id = $("#modalAddAction").find("#addactionprocessid").val();
                let step_id = $("#modalAddAction").find("#addactionstepid").val();
                let client_id = $("#modalAddAction").find("#addactionclientid").val();
                let activity_id = $("#modalAddAction").find("#addactionactivityid").val();
                let user_ids = $("#modalAddAction").find("#addactionuserids").val();
                let due_date = $("#modalAddAction").find("#addactionduedate").val();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: "POST",
                    url: '/clients/' + client_id + '/assignactivity',
                    data: {client_id: client_id, activity_id: activity_id, process_id: process_id, step_id: step_id, user_ids: user_ids, due_date: due_date},
                    success: function (data) {
                        $("#modalAddAction").find("#addactionprocessid").val('');
                        $("#modalAddAction").find("#addactionstepid").val('');
                        $("#modalAddAction").find("#addactionclientid").val('');
                        $("#modalAddAction").find("#addactionactivityid").val('');
                        $("#modalAddAction").find("#addactionuserids").val('');
                        $("#modalAddAction").find("#addactionduedate").val('');
                        $('#modalAddAction').modal('hide');

                        $('.flash_msg').html('<div class="alert alert-success alert-dismissible blackboard-alert">\n' +
                            '                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>\n' +
                            '                    <strong>Success!</strong> Activity successfully assigned.\n' +
                            '                </div>');
                    }
                });

            })

            $('#addactioncancel').on('click',function () {
                $('#modalAddAction').modal('hide');
            })

            $('#modalEditComment #editcommentsave').on('click',function(){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                let clientid = $("#modalEditComment").find("#editcommentclientid").val();
                let activityid = $("#modalEditComment").find("#editcommentactivityid").val();
                let commentid = $("#modalEditComment").find("#editcommentcommentid").val();
                let comment = $("#modalEditComment").find("#editcommentcomment").val();
                let privatec = 0;
                if($("#modalEditComment").find("#editcommentprivatec").is(':checked')) {
                    privatec = 1;
                } else {
                    privatec = 0;
                }
                $.ajax({
                    type: "POST",
                    url: '/clients/updatecomment/'+commentid,
                    data: {clientid:clientid,activityid:activityid,commentid: commentid,comment:comment,privatec:privatec},
                    success: function( data ) {
                        $("#modalEditComment").modal('hide');
                        showComment(clientid,activityid);
                    }
                });


            });

            $('.change-step').on("change",function(){
                completeStep2($('.change-step').val());
            });

        });

        function addAction(clientid,activityid,processid,stepid) {
            $("#modalAddAction").modal('show');
            $("#modalAddAction").find("#addactionclientid").val(clientid);
            $("#modalAddAction").find("#addactionactivityid").val(activityid);
            $("#modalAddAction").find("#addactionprocessid").val(processid);
            $("#modalAddAction").find("#addactionstepid").val(stepid);
        }

        function addComment(clientid,activityid) {

            $("#modalAddComment").modal('show');
            $("#modalAddComment").find("#addcommentclientid").val(clientid);
            $("#modalAddComment").find("#addcommentactivityid").val(activityid);

            tinymce.init(editor_config2);
        }

        function showComment(clientid,activityid) {

            $("#modalShowComment").modal('show');
            $("#modalShowComment").find("#showcommentcomment").empty();
            $("#modalShowComment").find("#showcommentclientid").val(clientid);
            $("#modalShowComment").find("#showcommentactivityid").val(activityid);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: '/clients/'+clientid+'/showcomment/'+activityid,
                data: {clientid: clientid, activityid: activityid},
                success: function( data ) {
                    let user = {{auth()->id()}};
                    let rows = '';

                    rows = rows + '<table cellpadding="10" cellspacing="0" border="0" width="100%">';
                    if(Object.keys(data).length === 0){
                        rows = rows + '<tr><td align="center">There are no comments for this activity.</td></tr>';
                    }
                    $.each( data, function( key, value ) {

                        if(user == value.user) {
                            rows = rows + '<tr class="comment_id_' + value.id + '"><td style="width: 30px;border-bottom:1px solid #ced4da;"><a href="/profile/' + value.user + '"><p style="margin-bottom: "><img src="/storage/avatar/?q=' + value.avatar + '" class="blackboard-avatar blackboard-avatar-inline blackboard-avatar-navbar-img" alt="Avatar"/></p></a></td><td style="border-bottom:1px solid #ced4da;">' + value.comment + '</td><td style="text-align:right;width:200px;border-bottom:1px solid #ced4da;"><p>' + value.date + '</p></td><td style="border-bottom:1px solid #ced4da;width:30px;"><p><a href="javascript:void(0)" onclick="editComment(' + value.client_id + ',' + value.activity_id + ',' + value.id + ',' + value.privatec + ')"><i class="fa fa-edit" style="color:#28a745"></i></a> </p></td><td style="border-bottom:1px solid #ced4da;width:30px;"><p><a href="javascript:void(0)" onclick="deleteComment(' + value.id + ',' + value.activity_id + ')"><i class="fa fa-trash-alt" style="color:#dc3545"></i> </a></p></td></tr>';
                        } else {
                            rows = rows + '<tr><td style="width: 30px;border-bottom:1px solid #ced4da;"><a href="/profile/' + value.user + '"><p style="margin-bottom: "><img src="/storage/avatar/?q=' + value.avatar + '" class="blackboard-avatar blackboard-avatar-inline blackboard-avatar-navbar-img" alt="Avatar"/></p></a></td><td style="border-bottom:1px solid #ced4da;">' + value.comment + '</td><td style="text-align:right;width:200px;border-bottom:1px solid #ced4da;"><p>' + value.date + '</p></td><td style="border-bottom:1px solid #ced4da;width:30px;"><p><a href="javascript:void(0)" style="cursor:not-allowed"><i class="fa fa-edit" style="color:#ddd"></i></a> </p></td><td style="border-bottom:1px solid #ced4da;width:30px;"><p><a href="javascript:void(0)" style="cursor: not-allowed"><i class="fa fa-trash-alt" style="color:#ddd"></i> </a></p></td></tr>';
                        }

                    });
                    rows = rows + '</table>';
                    $("#modalShowComment").find("#showcommentcomment").html(rows);
                    $("#showcommentcomment p").css('margin-bottom','0px');
                }
            });
        }

        function editComment(clientid,activityid,commentid,privatec) {
            $("#modalShowComment").modal('hide');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: '/clients/editcomment/'+commentid,
                data: {commentid: commentid},
                success: function( data ) {
                    $("#modalEditComment").modal('show');
                    $.each( data, function( key, value ) {
                        $("#modalEditComment").find("#editcommentcomment").val('');
                        $("#modalEditComment").find("#editcommentclientid").val(clientid);
                        $("#modalEditComment").find("#editcommentactivityid").val(activityid);
                        $("#modalEditComment").find("#editcommentcommentid").val(commentid);
                        $("#modalEditComment").find("#editcommentcomment").val(value.comment);

                        tinymce.init(editor_config2);

                        tinymce.get('editcommentcomment').setContent(value.comment);

                        if (privatec === 1) {
                            $("#modalEditComment").find("#editcommentprivatec").prop('checked', true);
                        } else {
                            $("#modalEditComment").find("#editcommentprivatec").prop('checked', false);
                        }
                    });
                }
            });

        }

        function deleteComment(commentid,activityid) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            if (!confirm("Are you sure you want to delete this record?")){
                return false;
            } else {
                $.ajax({
                    type: "POST",
                    url: '/clients/deletecomment/' + commentid,
                    data: {commentid: commentid},
                    success: function (data) {
                        $("#modalShowComment").find(".comment_id_" + commentid).remove();

                        let count = parseInt($("#old_comment_count_" + activityid).val());

                        count--;
                        if (count === 0) {
                            $("#modalShowComment").find("#showcommentcomment").html('<table cellpadding="10" cellspacing="0" border="0" width="100%"><tr><td align="center">There are no comments for this activity.</td></tr></table>');
                            $("#comment_count_" + activityid).addClass('badge-dark');
                            $("#comment_count_" + activityid).removeClass('badge-success');
                            $("#comment_count_fa_" + activityid).css('color', 'rgba(0,0,0,0.5)');
                        }
                        $("#comment_count_" + activityid).html(count);
                        $("#old_comment_count_" + activityid).val(count);

                    }
                });
            }
        }

        function uncomplete(clientid,activeid){
            $("#modalUnconvert").modal('show');
            $("#modalUnconvert").find("#unconvertclientid").val(clientid);
            $("#modalUnconvert").find("#unconvertactiveid").val(activeid);
            $("#modalUnconvert").find("#newconvertdate").val(activeid);
        }

        function complete(clientid,activeid){
            $("#modalConvert").modal('show');
            $("#modalConvert").find("#convertclientid").val(clientid);
            $("#modalConvert").find("#convertactiveid").val(activeid);
            $("#modalConvert").find("#convertdate").val();
        }

        function notifyDialog(message){
            var fClose = function(){
                modal.modal("hide");
            };

            var modal = $("#notifyModal");
            modal.modal("show");
            $("#notifyMessage").empty().append(message);
            $("#notifyOk").unbind().one('click', fClose);
        }

        function generateForm(clientid,process_id){
            $('#overlay').fadeIn();
            $.ajax({
                    type: "GET",
                    url: '/clients/' + clientid + '/generate_form/' + process_id,
                    success: function (data) {
                        if(data.http_code === 200){
                            $('#overlay').fadeOut();
                            
                            toastr.success('<strong>Success!</strong> Form successfully generated.');

                            toastr.options.timeOut = 1000;
                            
                        }
                    }
                });
        }

        function signForm(id,client_id){
            $('#modalSignForm').modal('show');

            $("#modalSignForm").find(".form_form_id").val(id);
            $("#modalSignForm").find(".form_client_id").val(client_id);
        }

        $("#cancelsignform").on('click',function (e){
            e.preventDefault();
            $("#modalSignForm").modal('hide');

        })

        $('#signform').on('click',function (e) {
            var id = $("#modalSignForm").find(".form_form_id").val();
            var pwd = $("#modalSignForm").find("#defaultForm-password").val();
            var clientid = $('body').find('#client_id').val();
            var process_id = $('body').find('#process_id').val();

            if(pwd === '') {
                $("#modalSignForm").find(".sign_error_message").html('The password you entered is incorrect.');
                $("#modalSignForm").find(".invalid-feedback").css('display','block');
            } else {

                $.ajax({
                    url: '/check_password/' + pwd,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        if (data.result !== 1) {
                            $("#modalSignForm").find(".sign_error_message").html('The password you entered is incorrect.');
                            $("#modalSignForm").find(".invalid-feedback").css('display','block');
                            return false

                        } else {
                            $("#modalSignForm").find(".invalid-feedback").css('display','none');
                            $("#modalSignForm").modal('hide');
                            $('#overlay').fadeIn();
                            $.ajax({
                                    type: "GET",
                                    url: '/clients/' + clientid + '/sign_form/' + process_id,
                                    success: function (data) {
                                        if(data.http_code === 200){
                                            $('#overlay').fadeOut();
                                            $('.sign-btn').hide();
                                            toastr.success('<strong>Success!</strong> Form successfully signed.');

                                            toastr.options.timeOut = 1000;
                                        }
                                    }
                                });

                        }
                    }
                });
            }


        })
        // function signForm(clientid,process_id){
        //     $('#overlay').fadeIn();
        //     $.ajax({
        //             type: "GET",
        //             url: '/clients/' + clientid + '/sign_form/' + process_id,
        //             success: function (data) {
        //                 if(data.http_code === 200){
        //                     $('#overlay').fadeOut();
        //                     $('.sign-btn').hide();
        //                     toastr.success('<strong>Success!</strong> Form successfully signed.');

        //                     toastr.options.timeOut = 1000;
        //                 }
        //             }
        //         });
        // }

        function submitTemplate(activity) {

            if ($("select[name=" + activity + "]").val() == "") {
                $('#message_' + activity).html('<span style="color: red;">Please select template</span>');
                return;
            }

            if ($("input[name=" + activity + "]").val() == "") {
                $('#message_' + activity).html('<span style="color: red;">Please enter email comma separated to send to more than one recipient</span>');
                return;
            }

            if ($('#template_email_'+activity).val() == "") {
                $('#message_' + activity).html('<span style="color: red;">Please select an email template.</span>');
                return;
            }

            if ($("input[name=subject_" + activity + "]").val() == "") {
                $('#message_' + activity).html('<span style="color: red;">Please enter a subject.</span>');
                return;
            }

            $('#message_' + activity).html('<span style="color: red;">Sending, please wait ...</span>');
            var data = {
                email: $("input[name=" + activity + "]").val(),
                template_file: $("select[name=" + activity + "]").val(),
                subject: $("input[name=subject_" + activity + "]").val(),
                template_email: $('#template_email_'+activity).val()
            };

            axios.post('{{route('clients.sendtemplate',$client)}}/' + activity, data)
                .then(function (data) {
                    $('#message_' + activity).html('<span style="color: green">Template sent successfully.</span>');
                })
                .catch(function () {
                    $('#message_' + activity).html('<span style="color: red">There was a problem with this request.</span>');
                });
        }

        function submitDocument(activity) {

            if ($("select[name=" + activity + "]").val() == "") {
                $('#message_' + activity).html('<span style="color: red;">Please select document</span>');
                return;
            }

            if ($("input[name=" + activity + "]").val() == "") {
                $('#message_' + activity).html('<span style="color: red;">Please enter email comma separated to send to more than one recipient</span>');
                return;
            }

            if ($('#template_email_'+activity).val() == "") {
                $('#message_' + activity).html('<span style="color: red;">Please select an email template.</span>');
                return;
            }

            if ($("input[name=subject_" + activity + "]").val() == "") {
                $('#message_' + activity).html('<span style="color: red;">Please enter a subject.</span>');
                return;
            }

            $('#message_' + activity).html('<span style="color: red;">Sending, please wait ...</span>');
            var data = {
                email: $("input[name=" + activity + "]").val(),
                document_file: $("select[name=" + activity + "]").val(),
                subject: $("input[name=subject_" + activity + "]").val(),
                template_email: $('#template_email_'+activity).val()
            };

            axios.post('{{route('clients.senddocument',$client)}}/' + activity, data)
                .then(function (data) {
                    $('#message_' + activity).html('<span style="color: green">Document sent successfully.</span>');
                })
                .catch(function () {
                    $('#message_' + activity).html('<span style="color: red">There was a problem with this request.</span>');
                });
        }

        function sendNotification(activity) {
            $('#message_' + activity).html('<span style="color: red;">Sending ...</span>');

            var data = {
                notification_user: $('#notification_user_name_'+activity).val()
            }

            axios.post('{{route('clients.sendnotification',$client)}}/' + activity, data)
                .then(function (data) {
                    $('#message_' + activity).html('<span style="color: green">Notifications sent successfully.</span>');
                })
                .catch(function () {
                    $('#message_' + activity).html('<span style="color: red">There was a problem with this request.</span>');
                });
        }

        function completeStep(step) {
            $('#step' + step).html('<span style="color: red;">Updating ...</span>');

            axios.post('{{route('clients.completestep',$client).'/'}}' + $('#process_id').val() + '/' + step)
                .then(function (data) {
                    var auto_completed_values = data["data"].activities_auto_completed;
                    for(i = 0; i< auto_completed_values.length; i++){
                        $("#step_header_"+step).css("background-color", '#3CFF463f');
                        $("#list_"+auto_completed_values[i]).css("background-color", '#3CFF463f');
                    }
                    $('#step_' + step).html('<span style="color: green">Done &nbsp;</span>');
                    //location.reload();
                    window.location.href = '{{ route('clients.progress', $client).'/'}}' + $('#process_id').val() + '{{'/'.$next_step}}' + '/' + $('#is_form').val();
                })
                .catch(function () {
                    $('#step_' + step).html('<span style="color: red">Error &nbsp;</span>');
                });
        }

        function completeStep2(step) {
            var r = confirm("Are you sure you want to move this client?");
            if (r == true) {
                axios.post('{{route('clients.completestep2',$client).'/'}}' + $('#process_id').val() + '/' + step)
                    .then(function (data) {
                        window.location.href = '{{ route('clients.progress', $client).'/'}}' + $('#process_id').val() + '/' + step + '/' + $('#is_form').val();
                    })
                    .catch(function () {
                        $('#step_' + step).html('<span style="color: red">Error &nbsp;</span>');
                    });
            }

        }

        function submitMultipleDocuments(activity){


            if ($('#templates_'+activity).val() == "" && $('#documents_'+activity).val() == "") {
                $('#message_' + activity).html('<span style="color: red;">Please select at least one template or document</span>');
                return;
            } else {
                $('#message_' + activity).html('');
            }

            if ($("input[name=" + activity + "]").val() == "") {
                $('#message_' + activity).html('<span style="color: red;">Please enter email comma separated to send to more than one recipient</span>');
                return;
            } else {
                $('#message_' + activity).html('');
            }

            if ($('#template_email_'+activity).val() == "") {
                $('#etemplate_message_' + activity).html('<span style="color: red;">Please select an email template.</span>');
                return;
            } else {
                $('#etemplate_message_' + activity).html('');
            }

            if ($("input[name=subject_" + activity + "]").val() == "") {
                $('#subject_message_' + activity).html('<span style="color: red;">Please enter a subject.</span>');
                return;
            } else {
                $('#subject_message_' + activity).html('');
            }

            $('#message_' + activity).html('<span style="color: red;">Sending ...</span>');

            var data = {
                templates: $('#templates_'+activity).val(),
                documents: $('#documents_'+activity).val(),
                email: $("input[name=" + activity + "]").val(),
                subject: $("input[name=subject_" + activity + "]").val(),
                template_email: $('#template_email_'+activity).val()
            }


            axios.post('{{route('clients.senddocuments',$client)}}/' + activity, data)
                .then(function (data) {
                    console.log(data);
                    $('#message_' + activity).html('<span style="color: green">Documents sent successfully.</span>');
                })
                .catch(function () {
                    $('#message_' + activity).html('<span style="color: red">There was a problem with this request.</span>');
                });
        }

        function startNewApplication(client_id,process_id) {
            let clientid = client_id;
            let processid = process_id;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '/clients/getnewprocesses/'+clientid,
                type:"GET",
                dataType:"json",
                success:function(data){
                    /*$('#modalSendTemplate').modal('hide');*/
                    $("#modalChangeProcess").modal('show');
                    $("#modalChangeProcess").find('.client_id').val(client_id);
                    $("#modalChangeProcess").find('.process_id').val(process_id);

                    $.each(data, function(key, value) {
                        $("#modalChangeProcess").find('#move_to_process_new').append($("<optgroup></optgroup>").attr("label",key).attr("id",key.replace(/ /gi,'').toLowerCase()));
                        $.each(value, function(k, v) {
                            $("#modalChangeProcess").find('#'+key.replace(/ /gi,'').toLowerCase()).append($("<option></option>").attr("value",v.id).text(v.name));
                        });
                    });

                    $("body").find('#move_to_process_new').trigger('chosen:updated');

                }
            });
        }
    </script>
    <script>

        var editor_config2 = {
            path_absolute : "/",
            branding: false,
            relative_urls: false,
            convert_urls : false,
            paste_data_images: true,
            browser_spellcheck: true,
            menubar: false,
            selector: "textarea.my-editor2",
            setup: function (editor) {
                editor.on('change', function () {
                    tinymce.triggerSave();
                });
            },
            plugins:["wordcount advlist lists paste table"],
            paste_as_text: true,
            toolbar: "undo redo | fontselect fontsizeselect formatselect | bold italic underline strikethrough | table | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist checklist | forecolor backcolor casechange permanentpen formatpainter removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media pageembed template link anchor codesample | a11ycheck ltr rtl | showcomments addcomment",

            external_filemanager_path:"{{url('tinymce/filemanager')}}/",
            filemanager_title:"Responsive Filemanager" ,
            external_plugins: { "filemanager" : "{{url('tinymce')}}/filemanager/plugin.min.js"}
        };

        $(document).find('iframe').each(function () {
            $(this).css('position','absolute');
            $(this).css('top','0px');
            $(this).css('width','100%');
            $(this).css('height','100%');
        })

        $(document).find('textarea').each(function () {
            var offset = this.offsetHeight - this.clientHeight;

            $(this).on('keyup input focus', function () {
                $(this).css('height', 'auto').css('height', this.scrollHeight + offset);
            });

            $(this).trigger("input");

            tinymce.init({
                formats : {
                    underline : {inline : 'u', exact : true}
                },
                selector: '.text-area',
                themes: 'modern',
                content_style: 'body { font-family: Arial; font-size: 10pt; }',
                height: 200,
                branding: false,
                menubar:false,
                plugins:["wordcount advlist lists paste"],
                paste_as_text: true,
                toolbar: "undo redo | bold italic underline"
            });

        });


    </script>
    <script>
        $(document).ready(function(){
            $("input[type=radio]:checked").each(function(){
                var activity_id = $(this).attr('name');
                var activity_value = $(this).val();

                if($('.selection-indicator-' + activity_id).length > 0){
                    $('.selection-indicator-' + activity_id).css('opacity','1');
                }

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });


                $.ajax({
                    url: '/getdependantactivities/?activity_id='+activity_id+'&activity_value='+activity_value,
                    type:"GET",
                    dataType:"json",
                    success:function(data){
                        $.each(data.activities, function(key, value) {
                            if($('#list_'+value).length) {
                                $('#list_' + value).css('display', 'table');
                                $('#' + value).trigger('chosen:updated');
                            }
                            if($('.list_'+value).length) {
                                $('.list_' + value).css('display', 'table');
                                $('.' + value).trigger('chosen:updated');
                            }
                        });
                        $.each(data.nonactivities, function(key, value) {
                            if($('#list_'+value).length) {
                                $('#list_' + value).css('display', 'none');
                                $('#' + value).trigger('chosen:updated');
                            }
                            if($('.list_'+value).length) {
                                $('.list_' + value).css('display', 'none');
                                $('.' + value).trigger('chosen:updated');
                            }
                        });
                    }
                });

                $.ajax({
                    url: '/getdependantsteps/?activity_id='+activity_id+'&activity_value='+activity_value,
                    type:"GET",
                    dataType:"json",
                    success:function(data){
                        $.each(data.steps, function(key, value) {
                            if($('#step_' + value).length) {

                                $('#step_' + value).css('display', 'table');
                                let cnt = $('.step-cnt-' + value).val();
                                $('.step-cnt-' + value).val(1);
                                var step_invisibil = $('#step_invisibil').val().split(',');
                                if(step_invisibil.includes(value)){
                                    step_invisibil.splice(step_invisibil.indexOf(value), 1);
                                } else {
                                }

                                $('#step_invisibil').val(step_invisibil.join(','));
                            }
                        });
                        $.each(data.nonsteps, function(key, value) {
                            if($('#step_'+value).length) {
                                if($('.step-cnt-'+value).val() >= 1) {
                                    let cnt = $('.step-cnt-' + value).val();
                                    $('.step-cnt-' + value).val(cnt);
                                    $('#step_' + value).css('display', 'none');
                                    var step_invisibil = $('#step_invisibil').val().split(',');
                                    if(step_invisibil.includes(value)){
                                    } else {
                                        step_invisibil.push(value)
                                    }

                                    $('#step_invisibil').val(step_invisibil.join(','));

                                    if(i === 1){

                                    }

                                }
                            }
                        });
                    }
                });
            });


        })

        $(".chosen-select").chosen().change(function(e, params){
            /*if(params.deselected) alert("deselected: " + params.deselected);
            else alert("selected: " + params.selected);*/
            var step_id = $('#step_id').val();
            var activity_id = $(this).attr('id');
            var activity_value = $(this).val();


            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


            $.ajax({
                url: '/getdependantactivities/?activity_id='+activity_id+'&activity_value='+activity_value,
                type:"GET",
                dataType:"json",
                success:function(data){
                    $.each(data.activities, function(key, value) {
                        if($('#list_'+value).length) {
                            $('#list_' + value).css('display', 'table');
                            $('#' + value).trigger('chosen:updated');
                        }
                        if($('.list_'+value).length) {
                            $('.list_' + value).css('display', 'table');
                            $('.' + value).trigger('chosen:updated');
                        }
                    });
                    $.each(data.nonactivities, function(key, value) {
                        if($('#list_'+value).length) {
                            $('#list_' + value).css('display', 'none');
                            $('#' + value).trigger('chosen:updated');
                        }
                        if($('.list_'+value).length) {
                            $('.list_' + value).css('display', 'none');
                            $('.' + value).trigger('chosen:updated');
                        }
                    });
                }
            });

            var inputValues = $('form').find('select').find('option:selected').map(function() {

                if($(this).length === 1) {
                    return $(this).text();
                } else {
                    $(this).each(function(i,n){
                        return $(n).text();
                    });
                }
            }).toArray();

            console.log(inputValues);

            $.ajax({
                url: '/getdependantsteps/?activity_id='+activity_id+'&activity_value='+activity_value,
                type:"GET",
                dataType:"json",
                success:function(data){
                    $.each(data.steps, function(key, value) {
                        if($('#step_' + value).length) {
                            $('#step_' + value).css('display', 'table');
                            let cnt = $('.step-cnt-' + value).val();
                            $('.step-cnt-' + value).val(1);
                            var step_invisibil = $('#step_invisibil').val().split(',');
                            if(step_invisibil.includes(value)){
                                step_invisibil.splice(step_invisibil.indexOf(value), 1);
                            } else {
                            }

                            $('#step_invisibil').val(step_invisibil.join(','));

                        }
                    });
                    $.each(data.nonsteps, function(key, value) {
                        if($('#step_'+value).length) {
                            if($('.step-cnt-'+value).val() >= 1) {

                                let i = 0;
                                $.ajax({
                                    url: '/getdropdowntext/?option=' + params.deselected,
                                    type: "GET",
                                    dataType: "json",
                                    success: function (data) {
                                        if(data) {
                                            if (inputValues.includes(data)) {
                                            } else {
                                                console.log(data);
                                                i = 1;
                                                cnt--;
                                                $('.step-cnt-' + value).val(cnt);
                                                $('#step_' + value).css('display', 'none');
                                                var step_invisibil = $('#step_invisibil').val().split(',');
                                                if (step_invisibil.includes(value)) {
                                                } else {
                                                    step_invisibil.push(value)
                                                }

                                                $('#step_invisibil').val(step_invisibil.join(','));
                                            }
                                        }
                                    },
                                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                                    }
                                });
                                let cnt = $('.step-cnt-' + value).val();
                                if(i === 1){

                                }

                            }
                        }
                    });
                }
            });
        });

        $( "input[type=radio]" ).change(function () {
            var step_id = $('#step_id').val();
            var activity_id = $(this).attr('name');
            var activity_value = $(this).val();

            if($('.selection-indicator-' + activity_id).length > 0){
                    $('.selection-indicator-' + activity_id).css('opacity','1');
                }
                
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


            $.ajax({
                url: '/getdependantactivities/?activity_id='+activity_id+'&activity_value='+activity_value,
                type:"GET",
                dataType:"json",
                success:function(data){
                    $.each(data.activities, function(key, value) {
                        if($('#list_'+value).length) {
                            $('#list_' + value).css('display', 'table');
                            $('#' + value).trigger('chosen:updated');
                        }
                        if($('.list_'+value).length) {
                            $('.list_' + value).css('display', 'table');
                            $('.' + value).trigger('chosen:updated');
                        }
                    });
                    $.each(data.nonactivities, function(key, value) {
                        if($('#list_'+value).length) {
                            $('#list_' + value).css('display', 'none');
                            $('#' + value).trigger('chosen:updated');
                        }
                        if($('.list_'+value).length) {
                            $('.list_' + value).css('display', 'none');
                            $('.' + value).trigger('chosen:updated');
                        }
                    });
                }
            });

            $.ajax({
                url: '/getdependantsteps/?activity_id='+activity_id+'&activity_value='+activity_value,
                type:"GET",
                dataType:"json",
                success:function(data){
                    $.each(data.steps, function(key, value) {
                        if($('#step_' + value).length) {

                            $('#step_' + value).css('display', 'table');
                            let cnt = $('.step-cnt-' + value).val();
                            $('.step-cnt-' + value).val(1);
                            var step_invisibil = $('#step_invisibil').val().split(',');
                            if(step_invisibil.includes(value)){
                                step_invisibil.splice(step_invisibil.indexOf(value), 1);
                            } else {
                            }

                            $('#step_invisibil').val(step_invisibil.join(','));
                        }
                    });
                    $.each(data.nonsteps, function(key, value) {
                        if($('#step_'+value).length) {
                            if($('.step-cnt-'+value).val() >= 1) {
                                let cnt = $('.step-cnt-' + value).val();
                                $('.step-cnt-' + value).val(cnt);
                                $('#step_' + value).css('display', 'none');
                                var step_invisibil = $('#step_invisibil').val().split(',');
                                if(step_invisibil.includes(value)){
                                } else {
                                    step_invisibil.push(value)
                                }

                                $('#step_invisibil').val(step_invisibil.join(','));

                                if(i === 1){

                                }

                            }
                        }
                    });
                }
            });
        });

        $( "input" ).keyup(function () {
            var step_id = $('#step_id').val();
            var activity_id = $(this).attr('id');
            var activity_value = $(this).val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


            $.ajax({
                url: '/getdependantactivities/?activity_id='+activity_id+'&activity_value='+activity_value,
                type:"GET",
                dataType:"json",
                success:function(data){
                    $.each(data.activities, function(key, value) {
                        if($('#list_'+value).length) {
                            $('#list_' + value).css('display', 'table');
                            $('#' + value).trigger('chosen:updated');
                        }
                        if($('.list_'+value).length) {
                            $('.list_' + value).css('display', 'table');
                            $('.' + value).trigger('chosen:updated');
                        }
                    });
                    $.each(data.nonactivities, function(key, value) {
                        if($('#list_'+value).length) {
                            $('#list_' + value).css('display', 'none');
                            $('#' + value).trigger('chosen:updated');
                        }
                        if($('.list_'+value).length) {
                            $('.list_' + value).css('display', 'none');
                            $('.' + value).trigger('chosen:updated');
                        }
                    });
                }
            });

            $.ajax({
                url: '/getdependantsteps/?activity_id='+activity_id+'&activity_value='+activity_value,
                type:"GET",
                dataType:"json",
                success:function(data){
                    $.each(data.steps, function(key, value) {
                        if($('#step_' + value).length) {
                            $('#step_' + value).css('display', 'table');
                        }
                    });
                    $.each(data.nonsteps, function(key, value) {
                        if($('#step_'+value).length) {
                            $('#step_' + value).css('display', 'none');
                        }
                    });
                }
            });
        });

        $( "textarea" ).keyup(function () {
            var step_id = $('#step_id').val();
            var activity_id = $(this).attr('id');
            var activity_value = $(this).val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


            $.ajax({
                url: '/getdependantactivities/?activity_id='+activity_id+'&activity_value='+activity_value,
                type:"GET",
                dataType:"json",
                success:function(data){
                    $.each(data.activities, function(key, value) {
                        if($('#list_'+value).length) {
                            $('#list_' + value).css('display', 'table');
                            $('#' + value).trigger('chosen:updated');
                        }
                        if($('.list_'+value).length) {
                            $('.list_' + value).css('display', 'table');
                            $('.' + value).trigger('chosen:updated');
                        }
                    });
                    $.each(data.nonactivities, function(key, value) {
                        if($('#list_'+value).length) {
                            $('#list_' + value).css('display', 'none');
                            $('#' + value).trigger('chosen:updated');
                        }
                        if($('.list_'+value).length) {
                            $('.list_' + value).css('display', 'none');
                            $('.' + value).trigger('chosen:updated');
                        }
                    });
                }
            });

            $.ajax({
                url: '/getdependantsteps/?activity_id='+activity_id+'&activity_value='+activity_value,
                type:"GET",
                dataType:"json",
                success:function(data){
                    $.each(data.steps, function(key, value) {
                        if($('#step_' + value).length) {
                            $('#step_' + value).css('display', 'table');
                        }
                    });
                    $.each(data.nonsteps, function(key, value) {
                        if($('#step_'+value).length) {
                            $('#step_' + value).css('display', 'none');
                        }
                    });
                }
            });
        });

        /*$('input, select, textarea').each(
            function(){
                if($(this).val()) {
                    var val = $(this).val().trim();
                    if (val.length) {
                        console.log(val);
                    }
                }
            });*/

        $(function() {
            let total = $('.select-this').length;
            let total_selected = $('.select-this:checked').length;

            if(total === total_selected){
                $("#select-all").prop('checked',true);
            } else {
                $("#select-all").prop('checked',false);
            }

            $("#select-all").on('change',function(){  //"select all" change
                var status = this.checked; // "select all" checked status
                var cnt = 1;

                $('.select-this').each(function(){ //iterate all listed checkbox items
                    this.checked = status; //change ".checkbox" checked status
                    var status_id = $("#select-all").prop('checked') == true ? 1 : 0;;
                    var activity = $(this).val();
                    var process_id = $('#process_id').val();
                    var client_id = $(this).data('client');


                    $.ajax({
                        type: "GET",
                        dataType: "json",
                        url: '/activity/include-in-basket',
                        data: {'status': status_id, 'activity_id':activity, 'client_id': client_id,'process_id':process_id,'all':'1'},
                        success: function(data) {
                            if (status_id) {
                                cnt = cnt + 1;
                                if (cnt === $(".select-this:checked").length) {
                                    toastr.success(data.success);
                                }
                            }

                            if (!status_id && cnt === 1) {
                                cnt = cnt + 1;
                                toastr.warning(data.success);
                            }
                            toastr.options.timeOut = 500;

                        }
                    });
                });
            });

            $('.select-this').change(function(){ //".checkbox" change
                //uncheck "select all", if one of the listed checkbox item is unchecked
                if(this.checked == false){ //if this item is unchecked
                    $("#select-all")[0].checked = false; //change "select all" checked status to false
                }

                //check "select all" if all checkbox items are checked
                if ($('.select-this:checked').length == $('.select-this').length ){
                    $("#select-all")[0].checked = true; //change "select all" checked status to true
                }
            });

            $('.select-this').change(function() {
                let total = $('.select-this').length;
                let total_selected = $('.select-this:checked').length;

                if(total === total_selected){
                    $("#select-all").prop('checked',true);
                } else {
                    $("#select-all").prop('checked',false);
                }

                var status = $(this).prop('checked') == true ? 1 : 0;
                var activity = $(this).val();
                var process_id = $('#process_id').val();
                var client_id = $(this).data('client');

                $.ajax({
                    type: "GET",
                    dataType: "json",
                    url: '/activity/include-in-basket',
                    data: {'status': status, 'activity_id':activity, 'client_id': client_id,'process_id':process_id},
                    success: function(data){
                        if(status){
                            toastr.success(data.success);
                        }else{
                            toastr.warning(data.success);
                        }
                        toastr.options.timeOut = 500;
                    }
                });
            })
        })
    </script>
@endsection