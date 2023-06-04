@extends('client.show')
@section('tab-content')
    <div class="col">
        <b>
            <div class="row mb-3 text-center">
                <div class="col"><i class="fa fa-circle" style="color: {{$client->process->getStageHex(0)}}"></i> Not-started</div>
                <div class="col"><i class="fa fa-circle" style="color: {{$client->process->getStageHex(1)}}"></i> Started</div>
                <div class="col"><i class="fa fa-circle" style="color: {{$client->process->getStageHex(2)}}"></i> Completed</div>
            </div>
        </b>

        <div id="step_header_{{$step->id}}" class="p-2">
            <h3 id="{{$step->order}}" class="d-inline">
                {{$step->name}}
            </h3>
            <div class="float-right form-inline">
                <button type="button" class="btn btn-sm btn-secondary float-right form-inline" onclick="completeStep({{$step->id}})"><i class="fa fa-check"></i> Auto-complete</button>
            </div>
        </div>

        {{Form::open(['url' => route('clients.storeprogress',$client->id), 'method' => 'post','files'=>true])}}
        <input type="hidden" value="{{$step->process_id}}" name="process_id" id="process_id">
        <input type="hidden" value="{{$step->id}}" name="step_id" id="step_id">

        {{--@foreach($step->activities as $activity)--}}
        @foreach($activities as $activity)

            <div id="list_{{$activity['id']}}" class="list-group-item activity" style="display:table;width:100%;">
                <div style="display:table-cell;width:20px;"><i class="fa fa-circle" style="color: {{$client->process->getStageHex($activity['stage'])}}"></i> </div>
                <div style="display:table-cell">
                    @if($activity['type'] == 'dropdown')
                        @php

                            $arr = (array)$activity['dropdown_items'];
                            $arr2 = (array)$activity['dropdown_values'];

                        @endphp
                        <input type="hidden" id="old_{{$activity['id']}}" name="old_{{$activity['id']}}" value="{{(!empty($arr2) ? implode(',',$arr2) : old($activity['id']))}}">
                    @else
                        <input type="hidden" id="old_{{$activity['id']}}" name="old_{{$activity['id']}}" value="{{(isset($activity['value']) ? $activity['value'] : old($activity['id']))}}">
                    @endif
                    {{$activity['name']}}

                    <small class="text-muted"> [{{$activity['type_display']}}] @if($activity['kpi']==1) <i class="fa fa-asterisk" title="Activity is required for step completion" style="color:#FF0000"></i> @endif</small>
                    <div style="float: right;margin-right:5px; display: inline-block;margin-top: -3px;padding-bottom: 3px;text-align: right;" class="form-inline">
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item dropdown">
                                <a href="javascript:void(0)" class="btn btn-sm" style="display: inline-block;line-height:1rem;color:rgba(0,0,0,1) !important;margin-top:-10px;background-color:buttonface" data-toggle="tooltip" data-html="true" title="Add Action" onclick="addAction({{$client->id}},{{$activity['id']}},{{$client->process_id}},{{$step['id']}})">Assign to a user</a>
                            </li>

                        </ul>

                    </div>
                    <div style="float: right; display: inline-block;margin-top: -3px;padding-bottom: 3px;text-align: right;">

                        @if(isset($activity['comment']) && $activity['comment'] == 1)
                            <div style="float: right; display: inline-block;margin-top: -3px;margin-right:5px;padding-bottom: 3px;text-align: right;" class="form-inline">
                                {{--<span style="display: none;padding-right: 10px;" class="comment_block_{{$activity['id']}}">
                                <small class="text-muted">Comment:</small> <input type="text" name="{{$activity['id']}}_comment" class="form-control form-control-sm">&nbsp;&nbsp;
                                <small class="text-muted">Private</small> <input type="checkbox">
                                </span>--}}
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

                    </div>
                    <div class="clearfix"></div>

                    @if($activity['type']=='date')
                        <input name="{{$activity['id']}}" type="date" min="1900-01-01" max="9999-12-31" value="{{(isset($activity['value']) ? $activity['value'] : old($activity['id']))}}" class="form-control form-control-sm" placeholder="Insert date..."/>
                    @endif

                    @if($activity['type']=='text')
                        {{Form::text($activity['id'],(isset($activity['value']) ? $activity['value'] : old($activity['id'])),['class'=>'form-control form-control-sm','placeholder'=>'Insert text...'])}}
                    @endif

                    @if($activity['type']=='boolean')
                        {{Form::select($activity['id'],[1=>'Yes',0=>'No'],(isset($activity['value']) ? $activity['value'] : ''),['class'=>'form-control form-control-sm','placeholder'=>'Please select...'])}}
                    @endif

                    @if($activity['type']=='template_email')
                        <div class="row">
                            <div class="col-md-12 input-group">
                                {{Form::select($activity['id'],$templates,(isset($activity['value']) ? $activity['value'] : ''),['class'=>'form-control form-control-sm','placeholder'=>'Please select...'])}}
                                <div class="input-group-append" onclick="viewTemplate({{$activity['id']}})">
                                    <button type="button" class="btn btn-multiple btn-sm">View Template</button>
                                </div>
                            </div>
                            <div class="col-md-12 input-group form-group" style="margin-top: 10px !important; margin-bottom: 10px !important;">
                                {{Form::select('template_email_'.$activity['id'],$template_email_options,null,['id'=>'template_email_'.$activity['id'],'onChange'=>'getSubject('.$activity['id'].')','class'=>'form-control form-control-sm'. ($errors->has('template_email_'.$activity['id']) ? ' is-invalid' : ''), 'placeholder'=>'Select Tempate Email...'])}}
                                <div class="input-group-append" onclick="viewEmailTemplate({{$activity['id']}})">
                                    <button type="button" class="btn btn-multiple btn-sm" data-toggle="modal" data-target="edit_email_template">View Email Template</button>
                                </div>
                                <div id="etemplate_message_{{$activity['id']}}"></div>
                            </div>
                            <div class="col-md-12 input-group" style="margin-bottom: 10px !important;">
                                {{Form::text('subject_'.$activity['id'],old('subject_'.$activity['id']),['class'=>'form-control form-control-sm','style'=>'width:100%','placeholder'=>'Insert email subject...'])}}
                                <div id="subject_message_{{$activity['id']}}"></div>
                            </div>
                            <div class="col-md-12 input-group">
                                {{Form::text($activity['id'],(isset($client->email) ? $client->email : old($client->email)),['class'=>'form-control form-control-sm','placeholder'=>'Insert email...'])}}
                                <div class="input-group-append" onclick="submitTemplate({{$activity['id']}})">
                                    <button type="button" class="btn btn-multiple btn-sm">Send Template</button>
                                </div>
                            </div>
                        </div>
                        <div id="message_{{$activity['id']}}"></div>
                    @endif

                    @if($activity['type']=='document_email')
                        <div class="row">
                            <div class="col-md-12 input-group form-group">
                                {{Form::select($activity['id'],$documents,(isset($activity['value']) ? $activity['value'] : ''),['class'=>'form-control form-control-sm','placeholder'=>'Please select...'])}}
                                <div class="input-group-append" onclick="viewDocument({{$activity['id']}})">
                                    <button type="button" class="btn btn-multiple btn-sm">View Document</button>
                                </div>
                            </div>
                            <div class="col-md-12 input-group form-group" style="margin-bottom: 10px !important;">
                                {{Form::select('template_email_'.$activity['id'],$template_email_options,null,['id'=>'template_email_'.$activity['id'],'onChange'=>'getSubject('.$activity['id'].')','class'=>'form-control form-control-sm'. ($errors->has('documents_'.$activity['id']) ? ' is-invalid' : ''), 'placeholder'=>'Select Tempate Email...'])}}
                                <div class="input-group-append" onclick="viewEmailTemplate({{$activity['id']}})">
                                    <button type="button" class="btn btn-multiple btn-sm" data-toggle="modal" data-target="edit_email_template">View Email Template</button>
                                </div>
                                <div id="etemplate_message_{{$activity['id']}}"></div>
                            </div>
                            <div class="col-md-12 input-group" style="margin-bottom: 10px !important;">
                                {{Form::text('subject_'.$activity['id'],old('subject_'.$activity['id']),['class'=>'form-control form-control-sm','style'=>'width:100%','placeholder'=>'Insert email subject...'])}}
                                <div id="subject_message_{{$activity['id']}}"></div>
                            </div>
                            <div class="col-md-12 input-group">
                                {{Form::text($activity['id'],(isset($client->email) ? $client->email : old($client->email)),['class'=>'form-control form-control-sm','placeholder'=>'Insert email...'])}}
                                <div class="input-group-append" onclick="submitDocument({{$activity['id']}})">
                                    <button type="button" class="btn btn-multiple btn-sm">Send Document</button>
                                </div>
                            </div>
                        </div>
                        <div id="message_{{$activity['id']}}"></div>
                    @endif

                    @if($activity['type']=='multiple_attachment')
                        <div class="row">
                            <div class="col-md-12">
                                <small class="form-text text-muted">
                                    Search and select multiple entries
                                </small>
                            </div>
                            <div class="col-md-6 input-group form-group" style="margin-bottom: 0px !important;">
                                {{Form::select('templates_'.$activity['id'],$templates,null,['id'=>'templates_'.$activity['id'],'class'=>'form-control form-control-sm chosen-select'. ($errors->has('templates_'.$activity['id']) ? ' is-invalid' : ''),'multiple'])}}
                                @foreach($errors->get('templates_'.$activity['id']) as $error)
                                    <div class="invalid-feedback">
                                        {{ $error }}
                                    </div>
                                @endforeach
                            </div>
                            <div class="col-md-6 input-group form-group" style="margin-bottom: 0px !important;">
                                {{Form::select('documents_'.$activity['id'],$document_options,null,['id'=>'documents_'.$activity['id'],'class'=>'form-control form-control-sm chosen-select'. ($errors->has('documents_'.$activity['id']) ? ' is-invalid' : ''),'multiple'])}}
                                @foreach($errors->get('documents_'.$activity['id']) as $error)
                                    <div class="invalid-feedback">
                                        {{ $error }}
                                    </div>
                                @endforeach
                            </div>
                            <div class="col-md-6">
                                <small id="templates_help" class="form-text text-muted">Templates</small>
                            </div>
                            <div class="col-md-6">
                                <small id="documents_help" class="form-text text-muted">Documents</small>
                            </div>

                            {{-- Todo: Add functionality for EmailTemplate(Pop up)--}}
                            <div class="col-md-12 input-group form-group" style="margin-top: 10px !important; margin-bottom: 10px !important;">
                                {{Form::select('template_email_'.$activity['id'],$template_email_options,null,['id'=>'template_email_'.$activity['id'],'onChange'=>'getSubject('.$activity['id'].')','class'=>'form-control form-control-sm'. ($errors->has('documents_'.$activity['id']) ? ' is-invalid' : ''), 'placeholder'=>'Select Template Email...'])}}
                                <div class="input-group-append" onclick="viewEmailTemplate({{$activity['id']}})">
                                    <button type="button" class="btn btn-multiple btn-sm" data-toggle="modal" data-target="edit_email_template">View Email Template</button>
                                </div>
                                <div id="etemplate_message_{{$activity['id']}}" class="col-md-12"></div>
                            </div>
                            <div class="col-md-12 input-group" style="margin-bottom: 10px !important;">
                                {{Form::text('subject_'.$activity['id'],old('subject_'.$activity['id']),['class'=>'col-md-12 form-control form-control-sm','style'=>'width:100%','placeholder'=>'Insert email subject...'])}}
                                <div id="subject_message_{{$activity['id']}}"></div>
                            </div>
                            <div class="col-md-12 input-group">
                                {{Form::text($activity['id'],(isset($client->email) ? $client->email : old($client->email)),['class'=>'form-control form-control-sm','placeholder'=>'Insert email...'])}}
                                <div class="input-group-append" onclick="sendMultipleDocuments({{$activity['id']}})">
                                    <button type="button" class="btn btn-multiple btn-sm">Send Template</button>
                                </div>

                            </div>
                            <div class="col-md-12">
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

                        {{-- Form::select($activity['id'],$activity['dropdown_items'],(isset($activity['value']) ? $activity['value'] : ''),['class'=>'form-control','placeholder'=>'Please select...']) --}}
                        <select multiple="multiple" id="{{$activity['id']}}" name="{{$activity["id"]}}[]" class="form-control form-control-sm chosen-select">
                            @php
                                foreach((array) $arr as $key => $value){
                                    echo '<option value="'.$key.'" '.(in_array($key,$arr2) ? 'selected' : '').'>'.$value.'</option>';
                                }
                            @endphp
                        </select>
                        <div>
                            <small class="form-text text-muted">
                                Search and select multiple entries
                            </small>
                        </div>
                        {{--{{Form::select($activity['id'],$activity['dropdown_items'],null,['id'=>$activity['id'],'class'=>'form-control'. ($errors->has($activity['id']) ? ' is-invalid' : ''),'multiple'])}}--}}
                        @foreach($errors->get($activity['id']) as $error)
                            <div class="invalid-feedback">
                                {{ $error }}
                            </div>
                        @endforeach

                    @endif

                    @if($activity['type']=='notification')
                        <div class="row">
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
        @endforeach

        {{Form::close()}}
    </div>
@endsection
@section('extra-css')
    <link rel="stylesheet" href="{{asset('chosen/chosen.min.css')}}">
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

        .chosen-container, .chosen-container-multi{
            width:100% !important;
        }

        .modal-open .modal{
            padding-right: 0px !important;
        }
    </style>
@endsection