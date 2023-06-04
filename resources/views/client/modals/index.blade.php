<div class="modal fade" id="modalChangeProcess" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width: 500px;">
        <div class="modal-content">
            <div class="modal-header text-center" style="border-bottom: 0px;padding:.5rem;">
                <h5 class="modal-title">Start New Case</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mx-3">
                <div class="row">
                    <div class="md-form col-sm-12 text-left">
                        <input type="hidden" class="client_id" />
                        <input type="hidden" class="process_id" />
                        <select name="process" class=" chosen-select form-control form-control-sm {{($errors->has('process') ? ' is-invalid' : '')}}" id="move_to_process_new">

                        </select>
                        <div id="move_to_process_new_msg" class="is-invalid"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="md-form col-sm-12 text-right btn-div p-0">
                    <button class="btn btn-outline-primary" data-dismiss="modal">Cancel</button>&nbsp;
                    <button class="btn btn-success" id="changeprocesssave">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalChangeForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width: 500px;">
        <div class="modal-content">
            <div class="modal-header text-center" style="border-bottom: 0px;padding:.5rem;">
                <h5 class="modal-title">Start New Form</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mx-3">
                <div class="row">
                    <div class="md-form col-sm-12 text-left">
                        <input type="hidden" class="client_id" />
                        <input type="hidden" class="process_id" />
                        <select name="process" class=" chosen-select form-control form-control-sm {{($errors->has('process') ? ' is-invalid' : '')}}" id="move_to_form_new">

                        </select>
                        <div id="move_to_form_new_msg" class="is-invalid"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="md-form col-sm-12 text-right btn-div p-0">
                    <button class="btn btn-outline-primary" data-dismiss="modal">Cancel</button>&nbsp;
                    <button class="btn btn-success" id="changeformsave">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCurrentProcesses" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width: 500px;">
        <div class="modal-content">
            <div class="modal-header text-center" style="border-bottom: 0px;padding:.5rem;">
                <h5 class="modal-title">Current Applications</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mx-3">
                <div class="row">
                    <div class="md-form col-sm-12 text-left">
                        <ul id="current_processes" style="padding: 0px 1rem;margin:0px">

                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="md-form col-sm-12 text-right btn-div p-0">
                    <button class="btn btn-outline-primary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalClosedProcesses" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width: 500px;">
        <div class="modal-content">
            <div class="modal-header text-center" style="border-bottom: 0px;padding:.5rem;">
                <h5 class="modal-title">Closed Applications</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mx-3">
                <div class="row">
                    <div class="md-form col-sm-12 text-left">
                        <ul id="closed_processes" style="padding: 0px 1rem;margin:0px">

                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="md-form col-sm-12 text-right btn-div p-0">
                    <button class="btn btn-outline-primary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAllProcesses" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width: 700px;">
        <div class="modal-content">
            <div class="modal-header text-center" style="border-bottom: 0px;padding:.5rem;">
                <h5 class="modal-title">Submit for Signatures</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mx-3">
                <input type="hidden" id="all_processes_process_id" name="all_processes_process_id">
                <input type="hidden" id="all_processes_step_id" name="all_processes_step_id">
                <div class="row">
                    <div class="md-form col-sm-12 text-left">
                        <p class="instruction"></p>
                        <table id="all_processes">

                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="md-form col-sm-12 text-right btn-div p-0">
                    <button class="btn btn-outline-primary" data-dismiss="modal">Close</button>&nbsp;
                    <a href="javascript:void(0)" class="btn btn-success" id="getApplicationDoc">Submit</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalSendMessage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width: 618px;max-width: 618px;">
        <div class="modal-content">
            <div class="modal-header text-center" style="border-bottom: 0px;padding:32px 32px 0px 32px;">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('messages.store') }}" method="post" class="form-inline" id="send_message_form">
                    {{ csrf_field() }}
                    <div class="col-md-12">
                        <div class="form-group">
                            @if($message_users->count() > 0)
                                <select name="recipients" data-placeholder="Add recipients" class="form-control form-control-sm select2 chosen-select" multiple>
                                    @foreach($message_users as $user)

                                        <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>

                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <!-- Subject Form Input -->
                        <div class="form-group" id="message_subject">
                            <input type="text" class="form-control form-control-sm" name="subject" placeholder="Subject" value="{{ old('subject') }}">
                        </div>

                        <!-- Message Form Input -->
                        <div class="form-group">
                            <textarea name="message" rows="10" id ="message" class="my-editor form-control form-control-sm">@if(Session::has('page_url')) Hi<br /><br />please have a look at <a href="{{Session::get('page_url')}}">{{Session::get('page_url')}}</a>. @endif</textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-primary" data-dismiss="modal">Cancel</button>&nbsp;
                <button class="btn btn-success" onclick="sendMessage()">Send Message</button>
            </div>
        </div>
    </div>
</div>
@if(isset($client_emails))
<div class="modal fade" id="modalSendMail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width: 618px;max-width: 618px;">
        <div class="modal-content">
            <div class="modal-header text-center" style="border-bottom: 0px;padding:32px 32px 0px 32px;">
                <h5 class="modal-title">Compose Mail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @if (isset($client->id))
                <form action="{{ route('clients.sendMail', $client->id )}}" method="post" class="form-inline" id="send_mail_form">
                    {{ csrf_field() }}
                    <div class="col-md-12">
                        <div class="form-group">
                                <select name="recipient[]" data-placeholder="Recipient" class="form-control form-control-sm chosen-select" multiple>
                                    @foreach($client_emails as $key => $value)
                                    @foreach($value as $k => $v)
                                        <option value="{{(isset($v["value"]) ? $v["value"] : '')}}">{{(isset($v["value"]) ? $v["value"] : '')}}</option>
                                    @endforeach
                                    @endforeach
                                </select>
                        </div>
                        <!-- Subject Form Input -->
                        <div class="form-group" id="msubject">
                            <input type="text" class="form-control form-control-sm" name="mail_subject" placeholder="Subject" value="">
                        </div>

                        <!-- Message Form Input -->
                        <div class="form-group">
                            <textarea name="mail_message" rows="10" id ="mail_message" class="my-editor form-control form-control-sm"></textarea>
                        </div>
                    </div>
                </form>
                @else
                    
                @endif
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-primary" data-dismiss="modal">Cancel</button>&nbsp;
                <button class="btn btn-success" onclick="sendMail()">Send Mail</button>
            </div>
        </div>
    </div>
</div>
@endif

{{--<div class="modal fade" id="modalSendWhatsapp" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width: 618px;max-width: 618px;">
        <div class="modal-content">
            <div class="modal-header text-center" style="border-bottom: 0px;padding:32px 32px 0px 32px;">
                <h5 class="modal-title">Compose Whatsapp Message</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pb-0 mb-0">
                @if (isset($client->id))
                    <form action="{{ route('clients.whatsappMessage') }}" method="post" class="form-inline" class="send_whatsapp_form">
                        {{ csrf_field() }}
                        <input type="hidden" class="client_id" value="{{$client->id}}">
                        <div class="col-md-12">
                            <div class="form-group">
                                <select disabled id="recipient" name="recipient" data-placeholder="Recipient" class="form-control form-control-sm select2 chosen-select recipient">
                                    <option selected value="{{$client->contact}}">{{$client->first_name}} {{$client->last_name}}: {{(substr($client->contact,0,1) == '0' ? '27'.substr($client->contact,1) : $client->contact )}}</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <select name="template" placeholder="Recipient" class="form-control form-control-sm select2 chosen-select template" onchange="getWhatsappTemplate()">
                                    @foreach($whatsapp_templates as $key => $value)
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Message Form Input -->
                            <div class="form-group">
                                <textarea name="whatsapp_message" rows="10" class="whatsapp_message" class="form-control form-control-sm" style="width: 100%;"></textarea>
                            </div>
                        </div>
                    </form>
                @else
                    <input type="hidden" class="client_id">
                    <div class="col-md-12 pb-0">
                        <div class="col-md-12 p-0">
                            <select disabled name="recipient" placeholder="Recipient" class="form-control form-control-sm select2 chosen-select recipient">

                            </select>
                        </div>

                        <div class="col-md-12 p-0">
                            <select name="template" placeholder="Recipient" class="form-control form-control-sm select2 chosen-select template" onchange="getWhatsappTemplate()">
                                @foreach($whatsapp_templates as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Message Form Input -->
                        <div class="col-md-12 p-0">
                            <textarea name="whatsapp_message" rows="10" class="whatsapp_message"  class="form-control form-control-sm" style="width: 100%;"></textarea>
                        </div>
                    </div>
                @endif
                --}}{{-- <form method="post" action="/message">
                    {{ csrf_field() }}
                    <label for="number">Where to send? <input name="number" id="number" type="text" size="20" /></label>
                    <label for="number">Message <textarea name="message" id="message" type="text"></textarea></label>
                    <input type="submit" />
                </form> --}}{{--
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-primary" data-dismiss="modal">Cancel</button>&nbsp;
                <button class="btn btn-success" onclick="sendWhatsapp()">Send Whatsapp</button>
            </div>
        </div>
    </div>
</div>--}}

@if(isset($client_list))
<div class="modal fade" id="modalBillboardMessage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width: 618px;max-width: 618px;">
        <div class="modal-content">
            <div class="modal-header text-center" style="border-bottom: 0px;padding:32px 32px 0px 32px;">
                <h5 class="modal-title">Add a Billboard Message</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pb-0 mb-0">

                <div class="col-md-12 pb-0">
                    <!-- Message Form Input -->
                    <div class="col-md-12 p-0">
                        <input name="billboard_heading" id="billboard_heading" class="form-control form-control-sm billboard_heading" />
                    </div>
                    {{--<div class="col-md-12 p-0">
                        <select name="client" placeholder="Client" class="form-control form-control-sm select2 chosen-select billboard_client">
                            <option value="">Please Select</option>
                            @foreach($client_list as $client)
                                <option value="{{$client["id"]}}">{{$client["client_name"]}}</option>
                            @endforeach
                        </select>
                    </div>--}}
                    <!-- Message Form Input -->
                    <div class="col-md-12 p-0">
                        <textarea name="billboard_message" rows="10" class="billboard_message"  class="form-control form-control-sm" style="width: 100%;"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-primary" data-dismiss="modal">Cancel</button>&nbsp;
                <button class="btn btn-success" onclick="saveBillboardMessage()">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditBillboardMessage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width: 618px;max-width: 618px;">
        <div class="modal-content">
            <div class="modal-header text-center" style="border-bottom: 0px;padding:32px 32px 0px 32px;">
                <h5 class="modal-title">Edit a Billboard Message</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pb-0 mb-0">

                <div class="col-md-12 pb-0">
                    <input type="hidden" class="message_id">
                    <!-- Message Form Input -->
                    <div class="col-md-12 p-0">
                        <input name="billboard_heading" id="billboard_heading" class="form-control form-control-sm billboard_heading" />
                    </div>
                    {{--<div class="col-md-12 p-0">
                        <select name="client" placeholder="Client" class="form-control form-control-sm select2 chosen-select billboard_client">
                            <option value="">Please Select</option>
                            @foreach($client_list as $client)
                                <option value="{{$client["id"]}}">{{$client["client_name"]}}</option>
                            @endforeach
                        </select>
                    </div>--}}
                    <!-- Message Form Input -->
                    <div class="col-md-12 p-0">
                        <textarea name="billboard_message" rows="10" class="billboard_message"  class="form-control form-control-sm" style="width: 100%;"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-primary" data-dismiss="modal">Cancel</button>&nbsp;
                <button class="btn btn-success" onclick="updateBillboardMessage()">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalShowBillboardMessage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width: 618px;max-width: 618px;">
        <div class="modal-content">
            <div class="modal-header text-center" style="border-bottom: 0px;padding:32px 32px 0px 32px;">
                <h5 class="modal-title">Billboard Message</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pb-0 mb-0">

                <div class="col-md-12 pb-0">
                    <input type="hidden" class="message_id">
                    <!-- Message Form Input -->
                    <div class="col-md-12 p-0 billboard_heading">
                    </div>
                    {{--<div class="col-md-12 p-0 billboard_client">
                    </div>--}}
                    <!-- Message Form Input -->
                    <div class="col-md-12 p-0 pt-1 billboard_message">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-primary" data-dismiss="modal">Cancel</button>&nbsp;
                <button class="btn btn-success" onclick="editBillboardMessage()">Edit</button>
                <button class="btn btn-danger" onclick="deleteBillboardMessage()">Delete</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalUserTask" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width: 618px;max-width: 618px;">
        <div class="modal-content">
            <div class="modal-header text-center" style="border-bottom: 0px;padding:32px 32px 0px 32px;">
                <h5 class="modal-title">Add a Task</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mb-0" style="padding:0px 32px 0px;">

                <div class="col-md-12 pb-0 pl-0 pr-0">
                    <!-- Message Form Input -->
                    <div class="col-md-12 p-0">
                        <label>Client</label>
                        <select name="client" placeholder="Client" class="form-control form-control-sm select2 chosen-select task_client">
                            <option value="">Please Select</option>
                            @foreach($client_list as $client)
                                <option value="{{$client["id"]}}">{{$client["client_name"]}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12 p-0">
                        <label>Subject</label>
                        <select name="task_type" onchange="showTaskOther()" class="task_type form-control form-control-sm w-100 mt-0">
                            @foreach($task_types as $key=>$value)
                                <option value="{{$value}}">{{$value}}</option>
                            @endforeach
                                {{--<option value="">Please Select</option>
                            <option value="Admin meeting">Admin meeting</option>
                            <option value="Member information session">Member information session</option>
                            <option value="Healthcare assistance one-on-one">Healthcare assistance one-on-one</option>
                            <option value="EB assistance one-on-one">EB assistance one-on-one</option>
                            <option value="Claim assistance meeting">Claim assistance meeting</option>
                            <option value="Board of Management Committee Meeting">Board of Management Committee Meeting</option>
                            <option value="Drop-off">Drop-off</option>--}}
                                <option value="Other">Other</option>
                        </select>
                    </div>
                    <!-- Message Form Input -->
                    <div class="col-md-12 p-0 task_other_div" style="display: none;">
                        <label>Description</label>
                        <input type="text" name="task_other" class="task_other form-control form-control-sm mt-0" style="width: 100%;">
                    </div>
                    <div class="col-md-12 p-0">
                        <label>Attendees <small>(Email address ; separated by)</small></label>
                        <input type="text" name="task_attendees" class="task_attendees form-control form-control-sm mt-0" style="width: 100%;">
                    </div>
                    <div class="col-md-12 p-0">
                    <div class="col-md-6 p-0 d-inline-block float-left pr-1">
                        <label>From</label>
                        <input type="datetime-local" name="task_date_start" class="task_date_start form-control form-control-sm mt-0" style="width: 100%;">
                    </div>
                    <div class="col-md-6 p-0 d-inline-block pl-1">
                        <label>To</label>
                        <input type="datetime-local" name="task_date_end" class="task_date_end form-control form-control-sm mt-0" style="width: 100%;">
                    </div>
                    </div>
                    <div class="col-md-12 p-0">
                        <label>Body</label>
                        <textarea name="task_message" rows="10" class="task_message form-control form-control-sm mt-0" style="width: 100%;"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-primary" data-dismiss="modal">Cancel</button>&nbsp;
                <button class="btn btn-success" onclick="saveUserTask()">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditUserTask" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width: 618px;max-width: 618px;">
        <div class="modal-content">
            <div class="modal-header text-center" style="border-bottom: 0px;padding:32px 32px 0px 32px;">
                <h5 class="modal-title">Edit Task</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pb-0 mb-0">

                <div class="col-md-12 pb-0">
                    <input type="hidden" class="task_id">
                    <!-- Message Form Input -->
                    <div class="col-md-12 p-0">
                        <select name="client" placeholder="Client" class="form-control form-control-sm select2 chosen-select task_client">
                            <option value="">Please Select</option>
                            @foreach($client_list as $client)
                                <option value="{{$client["id"]}}">{{$client["client_name"]}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12 p-0">
                        <select name="task_type" onchange="showTaskOther()" class="task_type form-control form-control-sm w-100">
                            @foreach($task_types as $key=>$value)
                                <option value="{{$value}}">{{$value}}</option>
                            @endforeach
                            {{--<option value="">Please Select</option>
                            <option value="Admin meeting">Admin meeting</option>
                            <option value="Member information session">Member information session</option>
                            <option value="Healthcare assistance one-on-one">Healthcare assistance one-on-one</option>
                            <option value="EB assistance one-on-one">EB assistance one-on-one</option>
                            <option value="Claim assistance meeting">Claim assistance meeting</option>
                            <option value="Board of Management Committee Meeting">Board of Management Committee Meeting</option>
                            <option value="Drop-off">Drop-off</option>--}}
                                <option value="Other">Other</option>
                        </select>
                    </div>
                    <!-- Message Form Input -->
                    <div class="col-md-12 p-0 task_other_div" style="display: none;">
                        <input type="text" name="task_other" class="task_other"  class="form-control form-control-sm chosen-select" style="width: 100%;">
                    </div>
                    <div class="col-md-12 p-0">
                        <div class="col-md-6 d-inline-block float-left p-0">
                            <input type="datetime-local" name="task_date" class="task_date_start form-control form-control-sm" style="width: 100%;">
                        </div>
                        <div class="col-md-6 d-inline-block p-0">
                            <input type="datetime-local" name="task_date" class="task_date_end form-control form-control-sm" style="width: 100%;">
                        </div>
                    </div>
                    <!-- Message Form Input -->
                    <div class="col-md-12 p-0">
                        <textarea name="task_message" rows="10" class="task_message"  class="form-control form-control-sm" style="width: 100%;"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-primary" data-dismiss="modal">Cancel</button>&nbsp;
                <button class="btn btn-success" onclick="updateUserTask()">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalShowUserTask" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width: 618px;max-width: 618px;">
        <div class="modal-content">
            <div class="modal-header text-center" style="border-bottom: 0px;padding:32px 32px 0px;">
                <h5 class="modal-title">Task</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mb-0" style="padding:0px 32px 0px;">

                <div class="col-md-12 pb-0 pr-0 pl-0">
                    <input type="hidden" class="task_id">
                    <label>Client</label>
                    <div class="col-md-12 p-0 task_client">
                    </div>
                    <label class="mt-1">Subject</label>
                    <div class="col-md-12 p-0 task_type">
                    </div>
                    <label class="mt-1">Description</label>
                    <div class="col-md-12 p-0 task_other_div" style="display: none;">
                    </div>
                    <label class="mt-1">Attendees <small>(Email address ; separated by)</small></label>
                    <div class="col-md-12 p-0 task_attendees">
                    </div>
                    <div class="col-md-12 p-0">
                    <label class="mt-1 col-md-6 d-inline-block float-left p-0 pr-1">From</label>
                    <label class="mt-1 col-md-6 d-inline-block p-0 pl-1">To</label>
                    <div class="col-md-6 d-inline-block float-left p-0 pr-1 task_date_start">
                    </div>
                    <div class="col-md-6 d-inline-block p-0 pl-1 task_date_end">
                    </div>
                    </div>
                    <label class="mt-1">Body</label>
                    <div class="col-md-12 p-0 pt-1 task_message">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-primary" data-dismiss="modal">Cancel</button>&nbsp;
                {{--<button class="btn btn-success" onclick="editUserTask()">Edit</button>
                <button class="btn btn-danger" onclick="deleteUserTask()">Delete</button>--}}
            </div>
        </div>
    </div>
</div>
@endif