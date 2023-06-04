@extends('flow.default')

@section('content')
    @if($is_valid == 1)
        <div class="col">

            {{Form::open(['url' => route('client.storeprogress',[$client->id,$step->process_id,$step->id]), 'method' => 'post','files'=>true])}}
            <input type="hidden" value="{{$step->process_id}}" name="process_id" id="process_id">
            <input type="hidden" value="{{$step->id}}" name="step_id" id="step_id">
            <div class="alert alert-info"><p><strong>Dear Client,</strong></p>
                <p>Please complete all the questions below, then click <strong>"Save"</strong> to submit the information.</p>
                <p>
                <ul class="ml-3">
                    <li>Select <strong>"Details"</strong> to update any Personal Information sent to you for completion.</li>
                    <li>Select <strong>"Activities"</strong> to update any Policy Application details sent to you for completion.</li>
                </ul>
                </p>
            </div>

            <div class="container-fluid client-detail" style="background:#FFFFFF;">
                @if(count($details) > 0)
                    <nav class="tabbable">
                        <div class="nav nav-tabs">

                            <a class="nav-link {{(count($details) > 0 ? 'active show' : '')}}" id="details-tab" data-toggle="tab" href="#details" role="tab" aria-controls="default" aria-selected="false">Details</a>

                            <a class="nav-link {{(count($details) > 0 ? '' : 'active show')}}" id="activities-tab" data-toggle="tab" href="#activities" role="tab" aria-controls="default" aria-selected="false">Activities</a>
                        </div>
                    </nav>
                @endif
                <div class="client-tab-content" id="myTabContent">
                    @if(count($details) > 0)
                        <div class="tab-pane fade {{(count($details) > 0 ? 'active show' : '')}} p-3" id="details" role="tabpanel" aria-labelledby="details-tab">
                            @foreach($details as $key =>$value)

                                @foreach($value as $k =>$v1)
                                    @if(in_array($k, $section_names_in_basket))
                                        <h5 class="d-inline">{{$k}}</h5>
                                        <input type="hidden" name="details" value="1">
                                    @endif
                                    @foreach($v1 as $k1=>$v)
                                        @php
                                            $i = 0;
                                        @endphp
                                        @foreach($v['inputs'] as $activity)
                                            @if(in_array($activity['id'],$client_basket_details) || $activity['client_bucket'] == 1)
                                                @if($activity['type'] == 'dropdown')
                                                    @php

                                                        $arr = (array)$activity['dropdown_items'];
                                                        $arr2 = (array)$activity['dropdown_values'];

                                                    @endphp
                                                    <input type="hidden" id="old_{{$activity['id']}}" name="old_{{$activity['id']}}" value="{{(!empty($arr2) ? implode(',',$arr2) : old($activity['id']))}}">
                                                @elseif($activity['type'] == 'amount')
                                                    <input type="hidden" id="old_currency_{{$activity['id']}}" name="old_currency_{{$activity['id']}}" value="{{(isset($activity['currency']) ? $activity['currency'] : old($activity['id']))}}">
                                                    <input type="hidden" id="old_{{$activity['id']}}" name="old_{{$activity['id']}}" value="{{(isset($activity['value']) ? $activity['value'] : old($activity['id']))}}">
                                                @else
                                                    <input type="hidden" id="old_{{$activity['id']}}" name="old_{{$activity['id']}}" value="{{(isset($activity['value']) ? $activity['value'] : old($activity['id']))}}">
                                                @endif
                                                @if($activity['type']=='heading')
                                                    <h4 style="width:{{$activity['level']}}%;margin-left: calc(100% - {{$activity['level']}}%);background-color:{{$activity['color'] != 'hsl(0,0%,0%)' ? $activity['color'] : ''}};padding:5px;">{{$activity['name']}}</h4>
                                                @elseif($activity['type']=='subheading')
                                                    <h5 style="width:{{$activity['level']}}%;margin-left: calc(100% - {{$activity['level']}}%);background-color:{{$activity['color'] != 'hsl(0,0%,0%)' ? $activity['color'] : ''}};padding:5px;">{{$activity['name']}}</h5>
                                                @else
                                                    <div id="list_{{$activity['id']}}" class="list-group-item" style="display:inline-block;width:{{$activity['level']}}%;margin-left: calc(100% - {{$activity['level']}}%);background-color:{{$activity['color'] != 'hsl(0,0%,0%)' && $activity['color'] != null ? $activity['color'] : '#f5f5f5'}};border:1px solid rgba(0,0,0,.125);">
                                                        <div style="display:inline-block;width:20px;vertical-align:top;"><i class="fa fa-circle" style="color: {{isset($activity['value']) && $activity['value'] != null ? 'rgba(50, 193, 75, 0.7)' : 'rgba(242, 99, 91, 0.7)'}}"></i> </div>
                                                        <div style="display: inline-block;width: calc(100% - 25px)">
                                                <span style="width:100%;float: left;display:block;">
                                                    {{$activity['name']}}
                                                    {{--<small class="text-muted"> [{{$activity['type']}}] </small>--}}
                                                    @if($activity['tooltip'] != null)

                                                        <a href="javascript:void(0)" style="display: inline-block;" class="has-tooltip"><i class="fa fa-info-circle"></i><span class="tooltip2 tooltip-top">{!! $activity['tooltip'] !!}</span></a>

                                                    @endif
                                                </span>
                                                            <div class="clearfix"></div>


                                                            @if($activity['type']=='date')
                                                                <input name="{{$activity['id']}}" type="date" min="1900-01-01" max="9999-01-01" value="{{(isset($activity['value']) ? $activity['value'] : old($activity['id']))}}" pattern="(?:19|20)[0-9]{2}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-9])|(?:(?!02)(?:0[1-9]|1[0-2])-(?:30))|(?:(?:0[13578]|1[02])-31))" class="form-control form-control-sm" placeholder="YYYY-MM-DD" {{$qa_complete != '' ? 'disabled' : ''}}/>
                                                            @endif

                                                            @if($activity['type']=='text')
                                                                {{Form::text($activity['id'],(isset($activity['value']) ? $activity['value'] : old($activity['id'])),['class'=>'form-control form-control-sm','placeholder'=>'Insert text...',$qa_complete])}}
                                                            @endif

                                                            @if($activity['type']=='percentage')
                                                                <input type="number" min="0" step="1" max="100" name="{{$activity['id']}}" value="{{(isset($activity['value']) ? $activity['value'] : old($activity['id']))}}" class="form-control form-control-sm" spellcheck="true" />
                                                            @endif

                                                            @if($activity['type']=='integer')
                                                                <input type="number" min="0" step="1" name="{{$activity['id']}}" value="{{(isset($activity['value']) ? $activity['value'] : old($activity['id']))}}" class="form-control form-control-sm" spellcheck="true" />
                                                            @endif

                                                            @if($activity['type']=='amount')
                                                                <input type="number" min="0" step="1" name="{{$activity['id']}}" value="{{(isset($activity['value']) ? $activity['value'] : old($activity['id']))}}" class="form-control form-control-sm" spellcheck="true" />
                                                            @endif

                                                            @if($activity['type']=='videoyoutube')
                                                                <div class="wrapper" style="position: relative;overflow: hidden;padding-top: 56.25%;">
                                                                    {!! $activity['default_value'] !!}

                                                                </div>
                                                            @endif

                                                            @if($activity['type']=='videoupload')
                                                                <video width="100%" height="100%" autoplay controls>
                                                                    <source src="{{'/storage/files/'.$activity['default_value']}}" type="video/mp4">
                                                                    Your browser does not support the video tag.
                                                                </video>



                                                            @endif

                                                            @if($activity['type']=='textarea')
                                                                <textarea rows="5" name="{{$activity['id']}}" class="form-control form-control-sm text-area" {{$qa_complete != '' ? 'disabled' : ''}}>{{(isset($activity['value']) ? $activity['value'] : old($activity['id']))}}</textarea>
                                                            @endif

                                                            @if($activity['type']=='boolean')
                                                                <label class="radio-inline"><input type="radio" name="{{$activity["id"]}}" class="{{$qa_complete != '' ? 'disabled' : ''}}" value="1" {{(isset($activity["value"]) && $activity["value"] == 1 ? 'checked' : '')}}><span class="ml-2">Yes</span></label>
                                                                <label class="radio-inline ml-3"><input type="radio" name="{{$activity["id"]}}" class="{{$qa_complete != '' ? 'disabled' : ''}}" value="0" {{(isset($activity["value"]) && $activity["value"] == 0 ? 'checked' : '')}}><span class="ml-2">No</span></label>
                                                                {{--{{Form::select($activity['id'],[1=>'Yes',0=>'No'],(isset($activity['value']) ? $activity['value'] : ''),['class'=>'form-control form-control-sm','placeholder'=>'Please select...'])}}--}}
                                                            @endif

                                                            @if($activity['type']=='template_email')
                                                                <div class="row">
                                                                    <div class="col-md-12 input-group">
                                                                        {{Form::select($activity['id'],$templates,(isset($activity['value']) ? $activity['value'] : ''),['class'=>'form-control form-control-sm','placeholder'=>'Please select...','id'=>'template_id_'.$activity['id'],$qa_complete])}}
                                                                        <div class="input-group-append">
                                                                            <button type="button" class="btn btn-multiple btn-sm btn-info" onclick="viewTemplate({{$activity['id']}})" {{$qa_complete != '' ? 'disabled' : ''}}><i class="fas fa-eye"></i></button>
                                                                            <button type="button" onclick="uploadFile({{$client->id}},{{$activity['id']}},'template')" class="btn btn-multiple btn-sm" {{$qa_complete != '' ? 'disabled' : ''}}><i class="fas fa-paperclip"></i></button>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12 input-group mt-1">
                                                                        @if(auth()->user() != null && (auth()->user()->is('admin') || auth()->user()->is('manager')))
                                                                            {{Form::text($activity['id'],(isset($activity["default_value"]) && $activity["default_value"] != null ? $activity["default_value"] : (isset($client->email) ? $client->email : $config->global_send_to_email)),['class'=>'form-control form-control-sm','placeholder'=>'Insert email...','id'=>'template_email_address_'.$activity['id'],$qa_complete])}}
                                                                        @else
                                                                            {{Form::hidden($activity['id'],(isset($activity["default_value"]) && $activity["default_value"] != null ? $activity["default_value"] : (isset($client->email) ? $client->email : $config->global_send_to_email)),['class'=>'form-control form-control-sm','placeholder'=>'Insert email...','id'=>'template_email_address_'.$activity['id']])}}
                                                                            {{Form::text($activity['id'],(isset($activity["default_value"]) && $activity["default_value"] != null ? $activity["default_value"] : (isset($client->email) ? $client->email : $config->global_send_to_email)),['class'=>'form-control form-control-sm','placeholder'=>'Insert email...','disabled'])}}
                                                                        @endif
                                                                        <div class="input-group-append" onclick="sendTemplate({{$activity['id']}},{{$client->id}})">
                                                                            <button type="button" class="btn btn-multiple btn-sm btn-success" {{$qa_complete != '' ? 'disabled' : ''}}><i class="fas fa-paper-plane"></i> Send</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div id="message_{{$activity['id']}}"></div>
                                                            @endif

                                                            @if($activity['type']=='document_email')
                                                                <div class="row">
                                                                    <div class="col-md-12 input-group">
                                                                        {{Form::select($activity['id'],$documents,(isset($activity['value']) ? $activity['value'] : ''),['class'=>'form-control form-control-sm','placeholder'=>'Please select...','id'=>'document_id_'.$activity['id'],$qa_complete])}}
                                                                        <div class="input-group-append">
                                                                            <button type="button" class="btn btn-multiple btn-sm btn-info" title="View Document" data-toggle="tooltip" onclick="viewDocument({{$activity['id']}})" {{$qa_complete != '' ? 'disabled' : ''}}><i class="fas fa-eye"></i> </button>
                                                                            <button type="button" onclick="uploadFile({{$client->id}},{{$activity['id']}},'document')" class="btn btn-multiple btn-sm" title="Upload Document" data-toggle="tooltip" {{$qa_complete != '' ? 'disabled' : ''}}><i class="fas fa-paperclip"></i> </button>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12 input-group mt-1">
                                                                        @if(auth()->user()->is('admin') || auth()->user()->is('manager'))
                                                                            {{Form::text($activity['id'],(isset($activity["default_value"]) && $activity["default_value"] != null ? $activity["default_value"] : (isset($client->email) ? $client->email : $config->global_send_to_email)),['class'=>'form-control form-control-sm','placeholder'=>'Insert email...','id'=>'template_email_address_'.$activity['id'],$qa_complete])}}
                                                                        @else
                                                                            {{Form::hidden($activity['id'],(isset($activity["default_value"]) && $activity["default_value"] != null ? $activity["default_value"] : (isset($client->email) ? $client->email : $config->global_send_to_email)),['class'=>'form-control form-control-sm','placeholder'=>'Insert email...','id'=>'template_email_address_'.$activity['id']])}}
                                                                            {{Form::text($activity['id'],(isset($activity["default_value"]) && $activity["default_value"] != null ? $activity["default_value"] : (isset($client->email) ? $client->email : $config->global_send_to_email)),['class'=>'form-control form-control-sm','placeholder'=>'Insert email...','disabled'])}}
                                                                        @endif
                                                                        <div class="input-group-append" onclick="sendDocument({{$activity['id']}},{{$client->id}})">
                                                                            <button type="button" class="btn btn-multiple btn-sm btn-success" title="Send Document" data-toggle="tooltip" {{$qa_complete != '' ? 'disabled' : ''}}><i class="fas fa-paper-plane"></i> Send</button>
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
                                                                        {{Form::select('templates_'.$activity['id'],$templates,null,['id'=>'template_id_'.$activity['id'],'class'=>'form-control form-control-sm chosen-select'. ($errors->has('templates_'.$activity['id']) ? ' is-invalid' : ''),'multiple',$qa_complete])}}
                                                                        <div class="input-group-append">
                                                                            <button type="button" onclick="uploadFile({{$client->id}},{{$activity['id']}},'template')" class="btn btn-multiple btn-sm" title="Upload Template" data-toggle="tooltip" {{$qa_complete != '' ? 'disabled' : ''}}><i class="fas fa-paperclip"></i> </button>
                                                                        </div>
                                                                        @foreach($errors->get('templates_'.$activity['id']) as $error)
                                                                            <div class="invalid-feedback">
                                                                                {{ $error }}
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                    <div class="col-md-6 input-group form-group" style="margin-bottom: 0px !important;">
                                                                        {{Form::select('documents_'.$activity['id'],$document_options,null,['id'=>'document_id_'.$activity['id'],'class'=>'form-control form-control-sm chosen-select'. ($errors->has('documents_'.$activity['id']) ? ' is-invalid' : ''),'multiple',$qa_complete])}}
                                                                        <div class="input-group-append">
                                                                            <button type="button" onclick="uploadFile({{$client->id}},{{$activity['id']}},'document')" class="btn btn-multiple btn-sm" title="Upload Document" data-toggle="tooltip" {{$qa_complete != '' ? 'disabled' : ''}}><i class="fas fa-paperclip"></i> </button>
                                                                        </div>
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

                                                                    <div class="col-md-12 input-group mt-1">
                                                                        @if(auth()->user()->is('admin') || auth()->user()->is('manager'))
                                                                            {{Form::text($activity['id'],(isset($activity["default_value"]) && $activity["default_value"] != null ? $activity["default_value"] : (isset($client->email) ? $client->email : $config->global_send_to_email)),['class'=>'form-control form-control-sm','placeholder'=>'Insert email...','id'=>'template_email_address_'.$activity['id'],$qa_complete])}}
                                                                        @else
                                                                            {{Form::hidden($activity['id'],(isset($activity["default_value"]) && $activity["default_value"] != null ? $activity["default_value"] : (isset($client->email) ? $client->email : $config->global_send_to_email)),['class'=>'form-control form-control-sm','placeholder'=>'Insert email...','id'=>'template_email_address_'.$activity['id']])}}
                                                                            {{Form::text($activity['id'],(isset($activity["default_value"]) && $activity["default_value"] != null ? $activity["default_value"] : (isset($client->email) ? $client->email : $config->global_send_to_email)),['class'=>'form-control form-control-sm','placeholder'=>'Insert email...','disabled'])}}
                                                                        @endif
                                                                        <div class="input-group-append" onclick="sendMultipleDocuments({{$activity['id']}},{{$client->id}})">
                                                                            <button type="button" class="btn btn-multiple btn-sm btn-success" {{$qa_complete != '' ? 'disabled' : ''}}><i class="fas fa-paper-plane"></i> Send</button>
                                                                        </div>

                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <small id="documents_help" class="form-text text-muted"><i class="fa fa-info-circle"></i> Use a comma to seperate multiple email addresses.</small>
                                                                    </div>
                                                                </div>
                                                                <div id="message_{{$activity['id']}}"></div>
                                                            @endif

                                                            @if($activity['type']=='document')
                                                                {{Form::file($activity['id'],['class'=>'form-control form-control-sm'. ($errors->has($activity['id']) ? ' is-invalid' : ''),'placeholder'=>'File',$qa_complete])}}
                                                                @foreach($errors->get($activity['id']) as $error)
                                                                    <div class="invalid-feedback">
                                                                        {{ $error }}
                                                                    </div>
                                                                @endforeach
                                                            @endif

                                                            @if($activity['type']=='dropdown')

                                                                {{-- Form::select($activity['id'],$activity['dropdown_items'],(isset($activity['value']) ? $activity['value'] : ''),['class'=>'form-control','placeholder'=>'Please select...']) --}}
                                                                <select multiple="multiple" id="{{$activity['id']}}" name="{{$activity["id"]}}[]" class="form-control form-control-sm chosen-select" {{$qa_complete != '' ? 'disabled' : ''}}>
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
                                                                        {{Form::select('notification_user_name_'.$activity['id'],$users,isset($activity['user_id'])?$activity['user_id']:null,['id'=>'notification_user_name_'.$activity['id'], 'class'=>'form-control form-control-sm chosen-select'.($errors->has('notification_user_name_'.$activity['id']) ? ' is-invalid' : ''),'multiple',$qa_complete])}}
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
                                                                        <button type="button" class="btn btn-primary btn-sm" onclick="sendNotification({{$activity['id']}})" {{$qa_complete != '' ? 'disabled' : ''}}><i class="fa fa-paper-plane"></i> Send notification</button>
                                                                    </div>
                                                                </div>
                                                                <div id="message_{{$activity['id']}}"></div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                                @php
                                                    $i++;
                                                @endphp
                                            @else
                                                @php
                                                    $i++;
                                                @endphp
                                            @endif
                                        @endforeach

                                    @endforeach
                                @endforeach
                            @endforeach
                        </div>
                    @endif
                    <div class="tab-pane fade {{(count($details) > 0 ? '' : 'active show')}} p-3" id="activities" role="tabpanel" aria-labelledby="activities-tab">
                        @foreach($activities as $key =>$value)

                            @foreach($value as $k =>$v1)
                                @if(in_array($k, $steps_names_in_basket))
                                    <h5 class="d-inline">{{$k}}</h5>
                                    <input type="hidden" name="activities" value="1">
                                @endif
                                @foreach($v1 as $k1=>$v)
                                    @php
                                        $i = 0;
                                    @endphp
                                    @foreach($v['activities'] as $activity)
                                        @if(in_array($activity['id'],$client_basket_activities) || $activity['client_bucket'] == 1)
                                            @if($activity['type'] == 'dropdown')
                                                @php

                                                    $arr = (array)$activity['dropdown_items'];
                                                    $arr2 = (array)$activity['dropdown_values'];

                                                @endphp
                                                <input type="hidden" id="old_{{$activity['id']}}" name="old_{{$activity['id']}}" value="{{(!empty($arr2) ? implode(',',$arr2) : old($activity['id']))}}">
                                            @else
                                                <input type="hidden" id="old_{{$activity['id']}}" name="old_{{$activity['id']}}" value="{{(isset($activity['value']) ? $activity['value'] : old($activity['id']))}}">
                                            @endif
                                            @if($activity['type']=='heading')
                                                <span class="list_{{$activity['id']}}" style="{{(in_array($activity['id'],$activity_invisibil) ? 'display:none;' : 'display:table;')}}width:{{$activity['level']}}%;margin-left: calc(100% - {{$activity['level']}}%);background-color:{{$activity['color'] != 'hsl(0,0%,0%)' ? $activity['color'] : ''}};padding:5px;
                                                {{(isset($activity['styles']['font_size']) ? 'font-size:'.$activity['styles']['font_size'].';' : '')}}
                                                {{(isset($activity['styles']['font_family']) ? 'font-family:'.$activity['styles']['font_family'].';' : '')}}
                                                {{(isset($activity['styles']['bold']) && $activity['styles']['bold'] == 1 ? 'font-weight:bold;' : '')}}
                                                {{(isset($activity['styles']['underline']) && $activity['styles']['underline'] == 1 ? 'text-decoration:underline;' : '')}}
                                                {{(isset($activity['styles']['italic']) && $activity['styles']['italic'] == 1 ? 'font-style:italic;' : '')}}
                                                        ">{{$activity['name']}}</span>
                                                <span class="list_{{$activity['id']}}" style="{{(in_array($activity['id'],$activity_invisibil) ? 'display:none;' : 'display:table;')}}width:{{$activity['level']}}%;margin-left: calc(100% - {{$activity['level']}}%);padding:5px;">{!! $activity['text_content'] !!}</span>
                                            @elseif($activity['type']=='subheading')
                                                <span class="list_{{$activity['id']}}" style="{{(in_array($activity['id'],$activity_invisibil) ? 'display:none;' : 'display:table;')}}width:{{$activity['level']}}%;margin-left: calc(100% - {{$activity['level']}}%);background-color:{{$activity['color'] != 'hsl(0,0%,0%)' ? $activity['color'] : ''}};padding:5px;
                                                {{(isset($activity['styles']['font_size']) ? 'font-size:'.$activity['styles']['font_size'].';' : '')}}
                                                {{(isset($activity['styles']['font_family']) ? 'font-family:'.$activity['styles']['font_family'].';' : '')}}
                                                {{(isset($activity['styles']['bold']) && $activity['styles']['bold'] == 1 ? 'font-weight:bold;' : '')}}
                                                {{(isset($activity['styles']['underline']) && $activity['styles']['underline'] == 1 ? 'text-decoration:underline;' : '')}}
                                                {{(isset($activity['styles']['italic']) && $activity['styles']['italic'] == 1 ? 'font-style:italic;' : '')}}
                                                        ">{{$activity['name']}}</span>
                                                <span class="list_{{$activity['id']}}" style="{{(in_array($activity['id'],$activity_invisibil) ? 'display:none;' : 'display:table;')}}padding:5px;width:{{$activity['level']}}%;margin-left: calc(100% - {{$activity['level']}}%);">{!! $activity['text_content'] !!}</span>
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
                                                <div id="list_{{$activity['id']}}" class="list-group-item" style="{{(in_array($activity['id'],$activity_invisibil) ? 'display:none;' : 'display:inline-block;')}}width:{{$activity['level']}}%;margin-left: calc(100% - {{$activity['level']}}%);background-color:{{$activity['color'] != 'hsl(0,0%,0%)' && $activity['color'] != null ? $activity['color'] : '#f5f5f5'}};border:1px solid rgba(0,0,0,.125);">
                                                    <div style="display:inline-block;width:20px;vertical-align:top;"><i class="fa fa-circle" style="color: {{isset($activity['value']) && $activity['value'] != null ? 'rgba(50, 193, 75, 0.7)' : 'rgba(242, 99, 91, 0.7)'}}"></i> </div>
                                                    <div style="display: inline-block;width: calc(100% - 25px)">
                                                <span style="width:100%;float: left;display:block;">
                                                    {{$activity['name']}}<small class="text-muted">{{-- [{{$activity['type']}}] --}}@if($activity['kpi']==1) <i class="fa fa-asterisk" title="Activity is required for step completion" style="color:#FF0000"></i> @endif</small>

                                                    @if($activity['tooltip'] != null)

                                                        <a href="javascript:void(0)" style="display: inline-block;" class="has-tooltip"><i class="fa fa-info-circle"></i><span class="tooltip2 tooltip-top">{!! $activity['tooltip'] !!}</span></a>

                                                    @endif
                                                </span>
                                                        <div class="clearfix"></div>

                                                        @if($activity['type']=='date')
                                                            <input name="{{$activity['id']}}" type="date" min="1900-01-01" max="{{Carbon\Carbon::parse(now())->format('Y-m-d')}}" value="{{(isset($activity['value']) ? $activity['value'] : old($activity['id']))}}" class="form-control form-control-sm" placeholder="Insert date..." {{$qa_complete != '' ? 'disabled' : ''}}/>
                                                        @endif

                                                        @if($activity['type']=='text')
                                                            {{Form::text($activity['id'],(isset($activity['value']) ? $activity['value'] : old($activity['id'])),['class'=>'form-control form-control-sm','placeholder'=>'Insert text...',$qa_complete])}}
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
                                                            <input type="number" min="0" step="1" name="{{$activity['id']}}" value="{{(isset($activity['value']) ? $activity['value'] : old($activity['id']))}}" class="form-control form-control-sm" spellcheck="true" />
                                                        @endif

                                                        @if($activity['type']=='amount')
                                                            <div class="input-group mb-3 input-group-sm">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">R</span>
                                                                </div>
                                                                <input type="number" min="0" step="1" name="{{$activity['id']}}" value="{{(isset($activity['value']) ? $activity['value'] : old($activity['id']))}}" class="form-control form-control-sm" spellcheck="true" />
                                                            </div>

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

                                                        @if($activity['type']=='textarea')
                                                            <textarea rows="5" name="{{$activity['id']}}" class="form-control form-control-sm text-area" {{$qa_complete != '' ? 'disabled' : ''}}>{{(isset($activity['value']) ? $activity['value'] : old($activity['id']))}}</textarea>
                                                        @endif

                                                        @if($activity['type']=='boolean')
                                                            <label class="radio-inline"><input type="radio" name="{{$activity["id"]}}" class="{{$qa_complete != '' ? 'disabled' : ''}}" value="1" {{(isset($activity["value"]) && $activity["value"] == 1 ? 'checked' : '')}}><span class="ml-2">Yes</span></label>
                                                            <label class="radio-inline ml-3"><input type="radio" name="{{$activity["id"]}}" class="{{$qa_complete != '' ? 'disabled' : ''}}" value="0" {{(isset($activity["value"]) && $activity["value"] == 0 ? 'checked' : '')}}><span class="ml-2">No</span></label>
                                                            {{--{{Form::select($activity['id'],[1=>'Yes',0=>'No'],(isset($activity['value']) ? $activity['value'] : ''),['class'=>'form-control form-control-sm','placeholder'=>'Please select...'])}}--}}
                                                        @endif

                                                        @if($activity['type']=='template_email')
                                                            <div class="row">
                                                                <div class="col-md-12 input-group">
                                                                    {{Form::select($activity['id'],$templates,(isset($activity['value']) ? $activity['value'] : ''),['class'=>'form-control form-control-sm','placeholder'=>'Please select...','id'=>'template_id_'.$activity['id'],$qa_complete])}}
                                                                    <div class="input-group-append">
                                                                        <button type="button" class="btn btn-multiple btn-sm btn-info" onclick="viewTemplate({{$activity['id']}})" {{$qa_complete != '' ? 'disabled' : ''}}><i class="fas fa-eye"></i></button>
                                                                        <button type="button" onclick="uploadFile({{$client->id}},{{$activity['id']}},'template')" class="btn btn-multiple btn-sm" {{$qa_complete != '' ? 'disabled' : ''}}><i class="fas fa-paperclip"></i></button>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12 input-group mt-1">
                                                                    {{--@if(auth()->check() && auth()->user() != null && (auth()->user()->is('admin') || auth()->user()->is('manager')))
                                                                        {{Form::text($activity['id'],(isset($activity["default_value"]) && $activity["default_value"] != null ? $activity["default_value"] : (isset($client->email) ? $client->email : $config->global_send_to_email)),['class'=>'form-control form-control-sm','placeholder'=>'Insert email...','id'=>'template_email_address_'.$activity['id'],$qa_complete])}}
                                                                    @else--}}
                                                                    {{Form::hidden($activity['id'],(isset($activity["default_value"]) && $activity["default_value"] != null ? $activity["default_value"] : (isset($client->email) ? $client->email : $config->global_send_to_email)),['class'=>'form-control form-control-sm','placeholder'=>'Insert email...','id'=>'template_email_address_'.$activity['id']])}}
                                                                    {{Form::text($activity['id'],(isset($activity["default_value"]) && $activity["default_value"] != null ? $activity["default_value"] : (isset($client->email) ? $client->email : $config->global_send_to_email)),['class'=>'form-control form-control-sm','placeholder'=>'Insert email...','disabled'])}}
                                                                    {{--@endif--}}
                                                                    <div class="input-group-append" onclick="sendTemplate({{$activity['id']}},{{$client->id}})">
                                                                        <button type="button" class="btn btn-multiple btn-sm btn-success" {{$qa_complete != '' ? 'disabled' : ''}}><i class="fas fa-paper-plane"></i> Send</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div id="message_{{$activity['id']}}"></div>
                                                        @endif

                                                        @if($activity['type']=='document_email')
                                                            <div class="row">
                                                                <div class="col-md-12 input-group">
                                                                    {{Form::select($activity['id'],$documents,(isset($activity['value']) ? $activity['value'] : ''),['class'=>'form-control form-control-sm','placeholder'=>'Please select...','id'=>'document_id_'.$activity['id'],$qa_complete])}}
                                                                    <div class="input-group-append">
                                                                        <button type="button" class="btn btn-multiple btn-sm btn-info" title="View Document" data-toggle="tooltip" onclick="viewDocument({{$activity['id']}})" {{$qa_complete != '' ? 'disabled' : ''}}><i class="fas fa-eye"></i> </button>
                                                                        <button type="button" onclick="uploadFile({{$client->id}},{{$activity['id']}},'document')" class="btn btn-multiple btn-sm" title="Upload Document" data-toggle="tooltip" {{$qa_complete != '' ? 'disabled' : ''}}><i class="fas fa-paperclip"></i> </button>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12 input-group mt-1">

                                                                    {{--@if(auth()->user()) && auth()->user()->is('admin') || auth()->user()->is('manager'))
                                                                        {{Form::text($activity['id'],(isset($activity["default_value"]) && $activity["default_value"] != null ? $activity["default_value"] : (isset($client->email) ? $client->email : $config->global_send_to_email)),['class'=>'form-control form-control-sm','placeholder'=>'Insert email...','id'=>'template_email_address_'.$activity['id'],$qa_complete])}}
                                                                    @else--}}
                                                                    {{Form::hidden($activity['id'],(isset($activity["default_value"]) && $activity["default_value"] != null ? $activity["default_value"] : (isset($client->email) ? $client->email : $config->global_send_to_email)),['class'=>'form-control form-control-sm','placeholder'=>'Insert email...','id'=>'template_email_address_'.$activity['id']])}}
                                                                    {{Form::text($activity['id'],(isset($activity["default_value"]) && $activity["default_value"] != null ? $activity["default_value"] : (isset($client->email) ? $client->email : $config->global_send_to_email)),['class'=>'form-control form-control-sm','placeholder'=>'Insert email...','disabled'])}}
                                                                    {{--@endif--}}
                                                                    <div class="input-group-append" onclick="sendDocument({{$activity['id']}},{{$client->id}})">
                                                                        <button type="button" class="btn btn-multiple btn-sm btn-success" title="Send Document" data-toggle="tooltip" {{$qa_complete != '' ? 'disabled' : ''}}><i class="fas fa-paper-plane"></i> Send</button>
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
                                                                    {{Form::select('templates_'.$activity['id'],$templates,null,['id'=>'template_id_'.$activity['id'],'class'=>'form-control form-control-sm chosen-select'. ($errors->has('templates_'.$activity['id']) ? ' is-invalid' : ''),'multiple',$qa_complete])}}
                                                                    <div class="input-group-append">
                                                                        <button type="button" onclick="uploadFile({{$client->id}},{{$activity['id']}},'template')" class="btn btn-multiple btn-sm" title="Upload Template" data-toggle="tooltip" {{$qa_complete != '' ? 'disabled' : ''}}><i class="fas fa-paperclip"></i> </button>
                                                                    </div>
                                                                    @foreach($errors->get('templates_'.$activity['id']) as $error)
                                                                        <div class="invalid-feedback">
                                                                            {{ $error }}
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                                <div class="col-md-6 input-group form-group" style="margin-bottom: 0px !important;">
                                                                    {{Form::select('documents_'.$activity['id'],$document_options,null,['id'=>'document_id_'.$activity['id'],'class'=>'form-control form-control-sm chosen-select'. ($errors->has('documents_'.$activity['id']) ? ' is-invalid' : ''),'multiple',$qa_complete])}}
                                                                    <div class="input-group-append">
                                                                        <button type="button" onclick="uploadFile({{$client->id}},{{$activity['id']}},'document')" class="btn btn-multiple btn-sm" title="Upload Document" data-toggle="tooltip" {{$qa_complete != '' ? 'disabled' : ''}}><i class="fas fa-paperclip"></i> </button>
                                                                    </div>
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
                                                                {{--<div class="col-md-12 input-group form-group" style="margin-top: 10px !important; margin-bottom: 10px !important;">
                                                                    {{Form::select('template_email_'.$activity['id'],$template_email_options,null,['id'=>'template_email_'.$activity['id'],'onChange'=>'getSubject('.$activity['id'].')','class'=>'form-control form-control-sm'. ($errors->has('documents_'.$activity['id']) ? ' is-invalid' : ''), 'placeholder'=>'Select Template Email...'])}}
                                                                    <div class="input-group-append" onclick="viewEmailTemplate({{$activity['id']}})">
                                                                        <button type="button" class="btn btn-multiple btn-sm" data-toggle="modal" data-target="edit_email_template">View Email Template</button>
                                                                    </div>
                                                                    <div id="etemplate_message_{{$activity['id']}}" class="col-md-12"></div>
                                                                </div>
                                                                <div class="col-md-12 input-group" style="margin-bottom: 10px !important;">
                                                                    {{Form::text('subject_'.$activity['id'],old('subject_'.$activity['id']),['class'=>'col-md-12 form-control form-control-sm','style'=>'width:100%','placeholder'=>'Insert email subject...'])}}
                                                                    <div id="subject_message_{{$activity['id']}}"></div>
                                                                </div>--}}
                                                                <div class="col-md-12 input-group mt-1">
                                                                    {{--@if(auth()->check() && auth()->user()->is('admin') || auth()->user()->is('manager'))
                                                                        {{Form::text($activity['id'],(isset($activity["default_value"]) && $activity["default_value"] != null ? $activity["default_value"] : (isset($client->email) ? $client->email : $config->global_send_to_email)),['class'=>'form-control form-control-sm','placeholder'=>'Insert email...','id'=>'template_email_address_'.$activity['id'],$qa_complete])}}
                                                                    @else--}}
                                                                    {{Form::hidden($activity['id'],(isset($activity["default_value"]) && $activity["default_value"] != null ? $activity["default_value"] : (isset($client->email) ? $client->email : $config->global_send_to_email)),['class'=>'form-control form-control-sm','placeholder'=>'Insert email...','id'=>'template_email_address_'.$activity['id']])}}
                                                                    {{Form::text($activity['id'],(isset($activity["default_value"]) && $activity["default_value"] != null ? $activity["default_value"] : (isset($client->email) ? $client->email : $config->global_send_to_email)),['class'=>'form-control form-control-sm','placeholder'=>'Insert email...','disabled'])}}
                                                                    {{--@endif--}}
                                                                    <div class="input-group-append" onclick="sendMultipleDocuments({{$activity['id']}},{{$client->id}})">
                                                                        <button type="button" class="btn btn-multiple btn-sm btn-success" {{$qa_complete != '' ? 'disabled' : ''}}><i class="fas fa-paper-plane"></i> Send</button>
                                                                    </div>

                                                                </div>
                                                                <div class="col-md-12">
                                                                    <small id="documents_help" class="form-text text-muted"><i class="fa fa-info-circle"></i> Use a comma to seperate multiple email addresses.</small>
                                                                </div>
                                                            </div>
                                                            <div id="message_{{$activity['id']}}"></div>
                                                        @endif

                                                        @if($activity['type']=='document')
                                                            {{Form::file($activity['id'],['class'=>'form-control form-control-sm'. ($errors->has($activity['id']) ? ' is-invalid' : ''),'placeholder'=>'File',$qa_complete])}}
                                                            @foreach($errors->get($activity['id']) as $error)
                                                                <div class="invalid-feedback">
                                                                    {{ $error }}
                                                                </div>
                                                            @endforeach
                                                        @endif

                                                        @if($activity['type']=='dropdown')

                                                            {{-- Form::select($activity['id'],$activity['dropdown_items'],(isset($activity['value']) ? $activity['value'] : ''),['class'=>'form-control','placeholder'=>'Please select...']) --}}
                                                            <select multiple="multiple" id="{{$activity['id']}}" name="{{$activity["id"]}}[]" class="form-control form-control-sm chosen-select" {{$qa_complete != '' ? 'disabled' : ''}}>
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
                                                                    {{Form::select('notification_user_name_'.$activity['id'],$users,isset($activity['user_id'])?$activity['user_id']:null,['id'=>'notification_user_name_'.$activity['id'], 'class'=>'form-control form-control-sm chosen-select'.($errors->has('notification_user_name_'.$activity['id']) ? ' is-invalid' : ''),'multiple',$qa_complete])}}
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
                                                                    <button type="button" class="btn btn-primary btn-sm" onclick="sendNotification({{$activity['id']}})" {{$qa_complete != '' ? 'disabled' : ''}}><i class="fa fa-paper-plane"></i> Send notification</button>
                                                                </div>
                                                            </div>
                                                            <div id="message_{{$activity['id']}}"></div>
                                                        @endif
                                                    </div>

                                                </div>
                                            @endif
                                            @php
                                                $i++;
                                            @endphp
                                        @else
                                            @php
                                                $i++;
                                            @endphp
                                        @endif
                                    @endforeach

                                @endforeach
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="blackboard-fab mr-3 mb-3">
                <button type="submit" class="btn btn-info btn-lg"><i class="fa fa-save" {{$qa_complete != '' ? 'disabled' : ''}}></i> Save</button>
            </div>

            {{Form::close()}}
            <div class="modal fade" id="modalUnconvert" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header text-center" style="border-bottom: 0px;padding:.5rem;">
                            <h5 class="modal-title">Uncomplete Client</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body mx-3">
                            <input type="hidden" name="clientid" id="unconvertclientid" />
                            <input type="hidden" name="activeid" id="unconvertactiveid" />
                            <div class="row step1">
                                <div class="md-form col-sm-12 pb-3 text-center">
                                    <label data-error="wrong" data-success="right" for="defaultForm-pass">Please select an action you would like to perform.</label>
                                </div>
                                <div class="md-form mb-4 col-sm-12 text-center">
                                    <button class="btn btn-sm btn-default" id="changeconvertdate">Change completion date</button>&nbsp;
                                    <button class="btn btn-sm btn-default" id="unconvert">Uncomplete</button>
                                </div>
                            </div>
                            <div class="row step2" style="display: none">
                                <div class="md-form col-sm-12 mb-3">
                                    <label data-error="wrong" data-success="right" for="defaultForm-pass">Please enter the new completion date.</label>
                                    <input type="date" min="1900-01-01" max="9999-12-31" id="newconvertdate" class="form-control form-control-sm validate">
                                </div>
                                <div class="md-form mb-4 col-sm-12">
                                    <button class="btn btn-sm btn-default" id="changeconvertdatesave">Save</button>&nbsp;
                                    <button class="btn btn-sm btn-default" id="changeconvertdatecancel">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modalConvert" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header text-center" style="border-bottom: 0px;padding:.5rem;">
                            <h5 class="modal-title">Complete Client</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body mx-3">
                            <input type="hidden" name="clientid" id="convertclientid" />
                            <input type="hidden" name="activeid" id="convertactiveid" />
                            <div class="row">
                                <div class="md-form col-sm-12 mb-3 text-center">
                                    <label data-error="wrong" data-success="right" for="defaultForm-pass">Please enter the complete date.</label>
                                    <input type="date" min="1900-01-01" max="9999-12-31" id="convertdate" class="form-control form-control-sm validate">
                                </div>
                                <div class="md-form mb-4 col-sm-12 text-center">
                                    <button class="btn btn-sm btn-default" id="convertdatesave">Save</button>&nbsp;
                                    <button class="btn btn-sm btn-default" id="convertdatecancel">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modalAddComment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header text-center" style="border-bottom: 0px;padding:.5rem;">
                            <h5 class="modal-title">Add Comment</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body mx-3">
                            <input type="hidden" name="clientid" id="addcommentclientid" />
                            <input type="hidden" name="activityid" id="addcommentactivityid" />
                            <div class="row">
                                <div class="md-form col-sm-12 mb-3 text-left">
                                    <label data-error="wrong" data-success="right" for="defaultForm-pass">Add a Comment</label>
                                    <textarea id="addcommentcomment" class="form-control form-control-sm my-editor2"></textarea>
                                </div>
                                <div class="md-form mb-4 col-sm-12 text-left">
                                    <input type="checkbox" name="privatec" id="addcommentprivatec" /> Private Comment
                                </div>
                                <div class="md-form mb-4 col-sm-12 text-center">
                                    <button class="btn btn-sm btn-default" id="addcommentsave">Save</button>&nbsp;
                                    <button class="btn btn-sm btn-default" id="addcommentcancel">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modalEditComment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header text-center" style="border-bottom: 0px;padding:.5rem;">
                            <h5 class="modal-title">Edit Comment</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body mx-3">
                            <input type="hidden" name="clientid" id="editcommentclientid" />
                            <input type="hidden" name="activityid" id="editcommentactivityid" />
                            <input type="hidden" name="activityid" id="editcommentcommentid" />
                            <div class="row">
                                <div class="md-form col-sm-12 mb-3 text-left">
                                    <label data-error="wrong" data-success="right" for="defaultForm-pass">Edit Comment</label>
                                    <textarea id="editcommentcomment" class="form-control form-control-sm my-editor2"></textarea>
                                </div>
                                <div class="md-form mb-4 col-sm-12 text-left">
                                    <input type="checkbox" name="privatec" id="editcommentprivatec" /> Private Comment
                                </div>
                                <div class="md-form mb-4 col-sm-12 text-center">
                                    <button class="btn btn-sm btn-default" id="editcommentsave">Save</button>&nbsp;
                                    <button class="btn btn-sm btn-default" id="editcommentcancel">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modalShowComment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog" style="width:800px !important;max-width:800px;">
                    <div class="modal-content">
                        <div class="modal-header text-center" style="border-bottom: 0px;padding:.5rem;">
                            <h5 class="modal-title">View Comment</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body mx-3">
                            <input type="hidden" name="clientid" id="showcommentclientid" />
                            <input type="hidden" name="activityid" id="showcommentactivityid" />
                            <div class="row">
                                <div class="md-form col-sm-12 mb-3 text-left">
                                    <label data-error="wrong" data-success="right" for="defaultForm-pass">Comments:</label>
                                    <div id="showcommentcomment"></div>
                                </div>
                                <div class="md-form mb-4 col-sm-12 text-center">
                                    <button class="btn btn-sm btn-default" id="showcommentcancel">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modalAddAction" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document" style="width:200px;">
                    <div class="modal-content">
                        <div class="modal-header text-center" style="border-bottom: 0px;padding:.5rem;">
                            <h5 class="modal-title">Assign To A User</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body mx-3">
                            <input type="hidden" name="clientid" id="addactionclientid" />
                            <input type="hidden" name="activeid" id="addactionactivityid" />
                            <input type="hidden" name="processid" id="addactionprocessid" />
                            <input type="hidden" name="stepid" id="addactionstepid" />
                            <div class="md-form col-sm-12">
                                <label data-error="wrong" data-success="right" for="addactionrecipients">Users</label>
                                {{Form::select('addactionuserids', $users,null, ['class'=>'form-control form-control-sm chosen-select','multiple', 'id' => 'addactionuserids'])}}
                            </div>
                            <div class="md-form col-sm-12 mb-3">
                                <label data-error="wrong" data-success="right" for="actionduedate">Due Date</label>
                                <input type="date" id="addactionduedate" name="addactionduedate" class="form-control form-control-sm validate">
                            </div>
                            <div class="md-form mb-4 col-sm-12">
                                <button class="btn btn-sm btn-default" id="addactionsave">Send</button>&nbsp;
                                <button class="btn btn-sm btn-default" id="addactioncancel">Cancel</button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modalSendTemplate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header text-center" style="border-bottom: 0px;padding:.5rem;">
                            <h5 class="modal-title">Send Letter</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body mx-3">
                            <input type="hidden" name="clientid" id="sendtemplateclientid" />
                            <input type="hidden" name="activeid" id="sendtemplateactivityid" />
                            <input type="hidden" name="processid" id="sendtemplateprocessid" />
                            <input type="hidden" name="stepid" id="sendtemplatestepid" />
                            <input type="hidden" name="templateid" id="sendtemplatetemplateid" />
                            <input type="hidden" name="emailaddress" id="sendtemplateemailaddress" />

                            <div id="sendtemplate_step1">
                                <div class="md-form mb-4 col-sm-12 text-center">
                                    <button class="btn btn-sm btn-dark" id="sendtemplatecomposeemail">Compose Email</button>&nbsp;
                                    <button class="btn btn-sm btn-dark" id="sendtemplatetemplateemail">Use Email Template</button>&nbsp;
                                    <button class="btn btn-sm btn-default sendtemplatecancel">Cancel</button>
                                </div>
                            </div>
                            <div id="sendtemplate_step2" style="display: none;">
                                <div class="form-group mt-3">
                                    {{Form::label('subject', 'Subject')}}
                                    {{Form::text('compose_template_email_subject',null,['class'=>'form-control form-control-sm','placeholder'=>'Subject','id'=>'compose_template_email_subject'])}}

                                </div>

                                <div class="form-group">
                                    {{Form::label('Email Body')}}
                                    {{ Form::textarea('compose_template_email_content', null, ['class'=>'form-control my-editor','size' => '30x10','id'=>'compose_template_email_content']) }}

                                </div>
                                <div id="sendcomposemessage"></div>
                                <div class="md-form mb-4 col-sm-12 text-center">
                                    <button class="btn btn-sm btn-dark" id="sendtemplatecomposeemailsend">Send</button>&nbsp;
                                    <button class="btn btn-sm btn-default sendtemplatecancel">Cancel</button>
                                    <button class="btn btn-sm btn-dark sendtemplateclose" style="display: none;">Close</button>
                                </div>
                            </div>
                            <div id="sendtemplate_step3" style="display: none;">
                                <div class="md-form mb-4 col-sm-12 input-group form-group">
                                    {{Form::select('template_email',$template_email_options,null,['id'=>'template_email','onChange'=>'getSubject()','class'=>'form-control form-control-sm'. ($errors->has('template_email_'.(isset($activity) ? $activity['id'] : '')) ? ' is-invalid' : ''), 'placeholder'=>'Select Template Email...'])}}
                                    <div class="input-group-append" onclick="viewEmailTemplate()">
                                        <button type="button" class="btn btn-multiple btn-sm" data-toggle="modal" data-target="edit_email_template">View Email Template</button>
                                    </div>
                                    <div id="template_message_error"></div>
                                </div>
                                <div class="md-form mb-4 col-sm-12">
                                    {{Form::text('subject_'.(isset($activity) ? $activity['id'] : ''),old('subject_'.(isset($activity) ? $activity['id'] : '')),['class'=>'form-control form-control-sm','style'=>'width:100%','placeholder'=>'Insert email subject...','id'=>'email_subject'])}}
                                </div>
                                <div class="col-sm-12 mb-4" id="sendtemplatemessage"></div>
                                <div class="md-form mb-4 col-sm-12 text-center">
                                    <button class="btn btn-sm btn-dark" id="sendtemplatetemplateemailsend">Send</button>&nbsp;
                                    <button class="btn btn-sm btn-default sendtemplatecancel">Cancel</button>
                                    <button class="btn btn-sm btn-dark sendtemplateclose" style="display: none;">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modalSendDocument" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header text-center" style="border-bottom: 0px;padding:.5rem;">
                            <h5 class="modal-title">Send Document</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body mx-3">
                            <input type="hidden" name="clientid" id="senddocumentclientid" />
                            <input type="hidden" name="activeid" id="senddocumentactivityid" />
                            <input type="hidden" name="processid" id="senddocumentprocessid" />
                            <input type="hidden" name="stepid" id="senddocumentstepid" />
                            <input type="hidden" name="templateid" id="senddocumenttemplateid" />
                            <input type="hidden" name="emailaddress" id="senddocumentemailaddress" />

                            <div id="senddocument_step1">
                                <div class="md-form mb-4 col-sm-12 text-center">
                                    <button class="btn btn-sm btn-dark" id="senddocumentcomposeemail">Compose Email</button>&nbsp;
                                    <button class="btn btn-sm btn-dark" id="senddocumenttemplateemail">Use Email Template</button>&nbsp;
                                    <button class="btn btn-sm btn-default senddocumentcancel">Cancel</button>
                                </div>
                            </div>
                            <div id="senddocument_step2" style="display: none;">
                                <div class="form-group mt-3">
                                    {{Form::label('subject', 'Subject')}}
                                    {{Form::text('compose_document_email_subject',null,['class'=>'form-control form-control-sm','placeholder'=>'Subject','id'=>'compose_document_email_subject'])}}

                                </div>

                                <div class="form-group">
                                    {{Form::label('Email Body')}}
                                    {{ Form::textarea('compose_document_email_content', null, ['class'=>'form-control my-editor','size' => '30x10','id'=>'compose_document_email_content']) }}

                                </div>
                                <div id="sendcomposemessaged"></div>
                                <div class="md-form mb-4 col-sm-12 text-center">
                                    <button class="btn btn-sm btn-dark" id="senddocumentcomposeemailsend">Send</button>&nbsp;
                                    <button class="btn btn-sm btn-default senddocumentcancel">Cancel</button>
                                    <button class="btn btn-sm btn-dark senddocumentclose" style="display: none;">Close</button>
                                </div>
                            </div>
                            <div id="senddocument_step3" style="display: none;">
                                <div class="md-form mb-4 col-sm-12 input-group form-group">
                                    {{Form::select('document_email',$template_email_options,null,['id'=>'document_email','onChange'=>'getDocumentSubject()','class'=>'form-control form-control-sm'. ($errors->has('template_email_'.(isset($activity) ? $activity['id'] : '')) ? ' is-invalid' : ''), 'placeholder'=>'Select Template Email...'])}}
                                    <div class="input-group-append" onclick="viewEmailDocument()">
                                        <button type="button" class="btn btn-multiple btn-sm" data-toggle="modal" data-target="edit_email_template">View Email Template</button>
                                    </div>
                                    <div id="document_message_error"></div>
                                </div>
                                <div class="md-form mb-4 col-sm-12">
                                    {{Form::text('subject_'.(isset($activity) ? $activity['id'] : ''),old('subject_'.(isset($activity) ? $activity['id'] : '')),['class'=>'form-control form-control-sm','style'=>'width:100%','placeholder'=>'Insert email subject...','id'=>'document_email_subject'])}}
                                </div>
                                <div class="col-sm-12 mb-4" id="senddocumentmessage"></div>
                                <div class="md-form mb-4 col-sm-12 text-center">
                                    <button class="btn btn-sm btn-dark" id="senddocumenttemplateemailsend">Send</button>&nbsp;
                                    <button class="btn btn-sm btn-default senddocumentcancel">Cancel</button>
                                    <button class="btn btn-sm btn-dark senddocumentclose" style="display: none;">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modalSendMA" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header text-center" style="border-bottom: 0px;padding:.5rem;">
                            <h5 class="modal-title">Send Multiple Attachments</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body mx-3">
                            <input type="hidden" name="clientid" id="sendmaclientid" />
                            <input type="hidden" name="activeid" id="sendmaactivityid" />
                            <input type="hidden" name="processid" id="sendmaprocessid" />
                            <input type="hidden" name="stepid" id="sendmastepid" />
                            <input type="hidden" name="templateid" id="sendmatemplateid" />
                            <input type="hidden" name="documentid" id="sendmadocumentid" />
                            <input type="hidden" name="emailaddress" id="sendmaemailaddress" />

                            <div id="sendma_step1">
                                <div class="md-form mb-4 col-sm-12 text-center">
                                    <button class="btn btn-sm btn-dark" id="sendmacomposeemail">Compose Email</button>&nbsp;
                                    <button class="btn btn-sm btn-dark" id="sendmatemplateemail">Use Email Template</button>&nbsp;
                                    <button class="btn btn-sm btn-default sendmacancel">Cancel</button>
                                </div>
                            </div>
                            <div id="sendma_step2" style="display: none;">
                                <div class="form-group mt-3">
                                    {{Form::label('subject', 'Subject')}}
                                    {{Form::text('compose_ma_email_subject',null,['class'=>'form-control form-control-sm','placeholder'=>'Subject','id'=>'compose_ma_email_subject'])}}

                                </div>

                                <div class="form-group">
                                    {{Form::label('Email Body')}}
                                    {{ Form::textarea('compose_ma_email_content', null, ['class'=>'form-control my-editor','size' => '30x10','id'=>'compose_ma_email_content']) }}

                                </div>
                                <div id="sendcomposemessagema"></div>
                                <div class="md-form mb-4 col-sm-12 text-center">
                                    <button class="btn btn-sm btn-dark" id="sendmacomposeemailsend">Send</button>&nbsp;
                                    <button class="btn btn-sm btn-default sendmacancel">Cancel</button>
                                    <button class="btn btn-sm btn-dark sendmaclose" style="display: none;">Close</button>
                                </div>
                            </div>
                            <div id="sendma_step3" style="display: none;">
                                <div class="md-form mb-4 col-sm-12 input-group form-group">
                                    {{Form::select('ma_email',$template_email_options,null,['id'=>'ma_email','onChange'=>'getMASubject()','class'=>'form-control form-control-sm'. ($errors->has('template_email_'.(isset($activity) ? $activity['id'] : '')) ? ' is-invalid' : ''), 'placeholder'=>'Select Template Email...'])}}
                                    <div class="input-group-append" onclick="viewEmailMA()">
                                        <button type="button" class="btn btn-multiple btn-sm" data-toggle="modal" data-target="edit_email_template">View Email Template</button>
                                    </div>
                                    <div id="ma_message_error"></div>
                                </div>
                                <div class="md-form mb-4 col-sm-12">
                                    {{Form::text('subject_'.(isset($activity) ? $activity['id'] : ''),old('subject_'.(isset($activity) ? $activity['id'] : '')),['class'=>'form-control form-control-sm','style'=>'width:100%','placeholder'=>'Insert email subject...','id'=>'ma_email_subject'])}}
                                </div>
                                <div class="col-sm-12 mb-4" id="sendmamessage"></div>
                                <div class="md-form mb-4 col-sm-12 text-center">
                                    <button class="btn btn-sm btn-dark" id="sendmatemplateemailsend">Send</button>&nbsp;
                                    <button class="btn btn-sm btn-default sendmacancel">Cancel</button>
                                    <button class="btn btn-sm btn-dark sendmaclose" style="display: none;">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modalFileUpload" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content" style="width: auto !important;">
                        <div class="modal-header text-center" style="border-bottom: 0px;padding:.5rem;">
                            <h5 class="modal-title">Upload File</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body mx-3">


                            <div>
                                <form method="POST" id="upload_form" enctype="multipart/form-data">
                                    <input type="hidden" name="clientid" id="fileuploadclientid" />
                                    <input type="hidden" name="activityid" id="fileuploadactivityid" />
                                    <input type="hidden" name="activitytype" id="fileuploadactivitytype" />
                                    <div class="md-form col-md-12">
                                        <label>Name:</label>
                                        <input name="filename" id="filename" type="text" class="form-control form-control-sm">
                                    </div>
                                    <div class="md-form col-md-12">
                                        <label>File:</label>
                                        <input name="file" id="fileupload" accept=".pdf,.csv,.xlsx,.docx,.doc,.xls,.pptx,.ppt" type="file" class="form-control">
                                    </div>
                                    {{--<div class="md-form col-md-12">
                                        <small id="documents_help" class="form-text text-muted"><i class="fa fa-info-circle"></i> Powerpoint, PDF, Excel, Word.</small>
                                    </div>--}}
                                    <div class="md-form col-md-12">
                                        <div id="message">
                                        </div>
                                    </div>
                                    <div class="md-form mb-4 col-sm-12 text-center">
                                        <input type="submit"  value="Upload" class="btn btn-sm btn-dark">
                                        <input type="button" id="fileuploadcancel"  value="cancel" class="btn btn-sm btn-default">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    @else
        <div style="display: table;width:370px;height:100%;margin:auto;">
            {{Form::open(['url' => route('client.progress',[$client->id,$process_id,$step->id]), 'method' => 'post','files'=>true,'style'=>'margin:0 auto;width:370px;display:table-cell;vertical-align:middle;'])}}
            <div style="border: 1px solid rgba(0,0,0,.125);background: #FFF;padding:20px 20px 10px;border-radius:7px;"><input type="hidden" value="{{$client->id}}" name="client">
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend" style="margin-top: 0;">
                            <div class="input-group-text">
                                <i class="fa fa-user"></i>
                            </div>
                        </div>
                        {{Form::email('email',old('email'),['class'=>'form-control form-control-sm'. ($errors->has('email') ? ' is-invalid' : ''),'placeholder'=>'Email','required','autofocus','style'=>'margin:0px'])}}
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend" style="margin-top: 0;">
                            <div class="input-group-text">
                                <i class="fa fa-lock"></i>
                            </div>
                        </div>
                        {{Form::password('pwd',['class'=>'form-control form-control-sm'. ($errors->has('password') ? ' is-invalid' : ''),'placeholder'=>'Password','required'])}}
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        {!! app('captcha')->render() !!}
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-block btn-default">
                        Log In
                    </button>
                </div>
            </div>
            {{Form::close()}}
        </div>
    @endif
@endsection
@section('extra-css')
    <link rel="stylesheet" href="{{asset('chosen/chosen.min.css')}}">
    <style>
        .content-wrapper{
            margin-top: 0px !important;
        }
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

        .modal .chosen-container, .modal .chosen-container-multi,.chosen-container, .chosen-container-multi{
            width:98% !important;
        }

        .chosen-container, .chosen-container-multi{
            line-height: 30px;
        }

        .modal-open .modal{
            padding-right: 0px !important;
        }

        .sidebar .user-panel,.sidebar nav,.process-slider,ul.nav-tabs,.process-dropdown,.back-btn{
            display: none !important;
        }
        .main-header{
            display:none;
        }
        .activity-container{
            border-top:1px solid #dee2e6!important;
        }
        @if($is_valid == 0)
        .activity-container{
            height:100vh !important;
            align-items: center;
        }
        .container-title{
            display:none !important;
        }

        @endif
    </style>
@endsection
@section('extra-js')

    <script src="{{asset('chosen/docsupport/init.js')}}" type="text/javascript" charset="utf-8"></script>
    <script>
        $(document).ready(function (){
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

            $('#sendtemplatetemplateemailsend').on('click',function(){
                $('#modalSendTemplate').find('#sendtemplatetemplateemailsend').attr("disabled", true);
                $('#modalSendTemplate').find('.sendtemplatecancel').attr("disabled", true);
                $('#modalSendTemplate').find('#sendtemplatemessage').html('<span style="color: red;">Sending, please wait ...</span>');
                var data = {
                    email: $('#modalSendTemplate').find("#sendtemplateemailaddress").val(),
                    template_file: $('#modalSendTemplate').find("#sendtemplatetemplateid").val(),
                    subject: $('#modalSendTemplate').find("#email_subject").val(),
                    template_email: $('#modalSendTemplate').find('#template_email').val()
                };

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
            })

            $('#sendtemplatecomposeemailsend').on('click',function(){
                $('#modalSendTemplate').find('#sendtemplatecomposeemailsend').attr("disabled", true);
                $('#modalSendTemplate').find('.sendtemplatecancel').attr("disabled", true);
                $('#modalSendTemplate').find('#sendcomposemessage').html('<span style="color: red;">Sending, please wait ...</span>');
                var data = {
                    email: $('#modalSendTemplate').find("#sendtemplateemailaddress").val(),
                    template_file: $('#modalSendTemplate').find("#sendtemplatetemplateid").val(),
                    subject: $('#modalSendTemplate').find("#compose_template_email_subject").val(),
                    email_content: $('#modalSendTemplate').find('#compose_template_email_content').val()
                };

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
            })

            $("body").find(".main-wrapper").attr("style","height:100vh !important;");
        })

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

            $('#senddocumenttemplateemailsend').on('click',function(){
                $('#modalSendDocument').find('#senddocumenttemplateemailsend').attr("disabled", true);
                $('#modalSendDocument').find('.senddocumentcancel').attr("disabled", true);
                $('#modalSendDocument').find('#senddocumentmessage').html('<span style="color: red;">Sending, please wait ...</span>');
                var data = {
                    email: $('#modalSendDocument').find("#senddocumentemailaddress").val(),
                    document_file: $('#modalSendDocument').find("#senddocumenttemplateid").val(),
                    subject: $('#modalSendDocument').find("#document_email_subject").val(),
                    template_email: $('#modalSendDocument').find('#document_email').val()
                };

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
            })

            $('#senddocumentcomposeemailsend').on('click',function(){
                $('#modalSendDocument').find('#senddocumentcomposeemailsend').attr("disabled", true);
                $('#modalSendDocument').find('.senddocumentcancel').attr("disabled", true);
                $('#modalSendDocument').find('#sendcomposemessaged').html('<span style="color: red;">Sending, please wait ...</span>');
                var data = {
                    email: $('#modalSendDocument').find("#senddocumentemailaddress").val(),
                    document_file: $('#modalSendDocument').find("#senddocumenttemplateid").val(),
                    subject: $('#modalSendDocument').find("#compose_document_email_subject").val(),
                    email_content: $('#modalSendDocument').find('#compose_document_email_content').val()
                };

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
            })
        })
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

            $('#sendmatemplateemailsend').on('click',function(){
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
            })

            $('#sendmacomposeemailsend').on('click',function(){
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
            })
        })
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

            //close move to process modal
            $('#changeprocesscancel').on('click',function(){
                $('#modalChangeProcess').modal('hide');
            })

            //move to process depending on radio selection
            $('#changeprocesssave').on('click',function(){
                //get value of radio button in modal
                let process_action = $('#modalChangeProcess').find('input[name=changeprocessradio]:checked').val();

                //perform this action if selection is to complete previous process
                if(process_action === 'autocomplete'){
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    let client_id = {{$client->id}};
                    let process_id = {{$client->process_id}};
                    let new_process_id = $('#modalChangeProcess').find('#move_to_process_new').val();

                    $.ajax({
                        url: '/clients/' + client_id + '/autocomplete_process/' + process_id + '/' + new_process_id,
                        type: "GET",
                        dataType: "json",
                        success: function (data) {
                            $("#modalChangeProcess").modal('hide');

                            window.location.href = '/clients/'+client_id+'/show/'+new_process_id+'/'+data.new_step_id;
                            /*$('.flash_msg').html('<div class="alert alert-success alert-dismissible blackboard-alert">\n' +
                                '                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>\n' +
                                '                    <strong>Success!</strong> Client successfully unconverted.\n' +
                                '                </div>');*/
                        }
                    });
                }

                //perform this action if selection is to keep previous process as is
                if(process_action === 'keep'){
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

                            window.location.href = '/clients/'+client_id+'/show/'+new_process_id+'/'+data.new_step_id;
                            /*$('.flash_msg').html('<div class="alert alert-success alert-dismissible blackboard-alert">\n' +
                                '                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>\n' +
                                '                    <strong>Success!</strong> Client successfully unconverted.\n' +
                                '                </div>');*/
                        }
                    });
                }
            })

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

    </script>
    <script>
        var editor_config = {
            path_absolute : "/",
            branding: false,
            relative_urls: false,
            convert_urls : false,
            paste_data_images: true,
            selector: "textarea.my-editor",
            setup: function (editor) {
                editor.on('change', function () {
                    tinymce.triggerSave();
                });
            },
            plugins:["wordcount advlist lists paste"],
            paste_as_text: true,
            toolbar: "undo redo | fontselect fontsizeselect formatselect | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist checklist | forecolor backcolor casechange permanentpen formatpainter removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media pageembed template link anchor codesample | a11ycheck ltr rtl | showcomments addcomment",

            external_filemanager_path:"{{url('tinymce/filemanager')}}/",
            filemanager_title:"Responsive Filemanager" ,
            external_plugins: { "filemanager" : "{{url('tinymce')}}/filemanager/plugin.min.js"}
        };

        var editor_config2 = {
            path_absolute : "/",
            branding: false,
            relative_urls: false,
            convert_urls : false,
            paste_data_images: true,
            menubar: false,
            selector: "textarea.my-editor2",
            setup: function (editor) {
                editor.on('change', function () {
                    tinymce.triggerSave();
                });
            },
            plugins:["wordcount advlist lists paste"],
            paste_as_text: true,
            toolbar: "undo redo | fontselect fontsizeselect formatselect | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist checklist | forecolor backcolor casechange permanentpen formatpainter removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media pageembed template link anchor codesample | a11ycheck ltr rtl | showcomments addcomment",

            external_filemanager_path:"{{url('tinymce/filemanager')}}/",
            filemanager_title:"Responsive Filemanager" ,
            external_plugins: { "filemanager" : "{{url('tinymce')}}/filemanager/plugin.min.js"}
        };



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

        $(document).find('iframe').each(function () {
            $(this).css('position','absolute');
            $(this).css('top','0px');
            $(this).css('width','100%');
            $(this).css('height','100%');
        })

        $( "select" ).change(function () {
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
        });

        $( "input[type=radio]" ).change(function () {
            var activity_id = $(this).attr('name');
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
        });

        $( "input" ).keyup(function () {
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
        });
    </script>
@endsection