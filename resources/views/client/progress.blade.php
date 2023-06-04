@extends('client.show')

@section('tab-content')
    <div class="col">

        <b>
            <div class="row mb-3 text-center">
                <div class="col" style="background-color: {{$client->process->getStageHex(0)}}">Not-started</div>
                <div class="col" style="background-color: {{$client->process->getStageHex(1)}}">Started</div>
                <div class="col" style="background-color: {{$client->process->getStageHex(2)}}">Completed</div>
            </div>
        </b>

        {{Form::open(['url' => route('clients.storeprogress',$client->id), 'method' => 'post','files'=>true])}}

        @foreach($process_progress as $key => $step)
            <div id="step_header_{{$step['id']}}" class="p-2" style="background-color: {{$client->process->getStageHex($step['stage'])}}">
                <h3 id="{{$step['order']}}" class="d-inline">
                    {{$step['name']}}
                </h3>
                <div class="d-inline float-right">
                    <div id="step_{{$step['id']}}" class="d-inline"></div>
                    <button type="button" class="btn btn-sm btn-secondary" onclick="completeStep({{$step['id']}})"><i class="fa fa-check"></i> Auto-complete</button>
                </div>
            </div>
            <ul class="list-group mt-3 mb-3">
                @foreach($step['activities'] as $activity)

                    <li id="list_{{$activity['id']}}" class="list-group-item" style="background-color: {{$client->process->getStageHex($activity['stage'])}}">
                        {{$activity['name']}}

                        <small class="text-muted"> [{{$activity['type_display']}}] @if($activity['kpi']==1) <i class="fa fa-asterisk" title="Activity is required for step completion"></i> @endif</small>

                        {-- //activity type hook --}

                        @if($activity['type']=='date')
                            <input name="{{$activity['id']}}" type="date" min="1900-01-01" max="9999-12-31" value="{{(isset($activity['value']) ? $activity['value'] : old($activity['id']))}}" class="form-control" placeholder="Insert date..."/>
                        @endif

                        @if($activity['type']=='text')
                            {{Form::text($activity['id'],(isset($activity['value']) ? $activity['value'] : old($activity['id'])),['class'=>'form-control','placeholder'=>'Insert text...'])}}
                        @endif

                        @if($activity['type']=='boolean')
                            {{Form::select($activity['id'],[1=>'Yes',0=>'No'],(isset($activity['value']) ? $activity['value'] : ''),['class'=>'form-control','placeholder'=>'Please select...'])}}
                        @endif

                        @if($activity['type']=='template_email')
                            <div class="row">
                                <div class="col-md-5 input-group">
                                    {{Form::select($activity['id'],$templates,(isset($activity['value']) ? $activity['value'] : ''),['class'=>'form-control','placeholder'=>'Please select...'])}}
                                </div>
                                <div class="col-md-7 input-group">
                                    {{Form::text($activity['id'],(isset($client->email) ? $client->email : old($client->email)),['class'=>'form-control','placeholder'=>'Insert email...'])}}
                                    <div class="input-group-append" onclick="viewTemplate({{$activity['id']}})">
                                        <button type="button" class="btn">View Template</button>
                                    </div>
                                    &nbsp;&nbsp;
                                    <div class="input-group-append" onclick="submitTemplate({{$activity['id']}})">
                                        <button type="button" class="btn">Send Template</button>
                                    </div>
                                </div>
                            </div>
                            <div id="message_{{$activity['id']}}"></div>
                        @endif

                        @if($activity['type']=='document_email')
                            <div class="input-group">
                                {{Form::select($activity['id'],$documents,(isset($activity['value']) ? $activity['value'] : ''),['class'=>'form-control','placeholder'=>'Please select...'])}}
                                <div class="input-group-append">
                                    <button type="button" class="btn">Send Document</button>
                                </div>
                            </div>
                        @endif

                        @if($activity['type']=='document')
                            {{Form::file($activity['id'],['class'=>'form-control'. ($errors->has($activity['id']) ? ' is-invalid' : ''),'placeholder'=>'File'])}}
                            @foreach($errors->get($activity['id']) as $error)
                                <div class="invalid-feedback">
                                    {{ $error }}
                                </div>
                            @endforeach
                        @endif

                        @if($activity['type']=='dropdown')
                            {{Form::select($activity['id'],$activity['dropdown_items'],(isset($activity['value']) ? $activity['value'] : ''),['class'=>'form-control','placeholder'=>'Please select...'])}}
                        @endif

                        @if($activity['type']=='notification')
                            <br>
                            <button type="button" class="btn btn-primary btn-sm" onclick="sendNotification({{$activity['id']}})"><i class="fa fa-paper-plane"></i> Send notification</button>
                            <div id="message_{{$activity['id']}}"></div>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endforeach

        @if(count($process_progress)>0)

            <div class="blackboard-complete-btn mr-3 mb-3">
                <a href="{{ route('clients.complete', $client->id) }}" class="btn btn-primary btn-lg"><i class="fa fa-hourglass-end"></i>Complete</a>
            </div>

            <div class="blackboard-fab mr-3 mb-3">
                <button type="submit" class="btn btn-primary btn-lg"><i class="fa fa-save"></i> Save</button>
            </div>
        @endif

        {{Form::close()}}

        <small class="text-muted"> Using process for: <b>{{$client->process->office->name}}</b> last updated: <b>{{$client->process->updated_at->diffForHumans()}}</b></small>
    </div>
@endsection

@section('extra-js')
    <script>

        function viewTemplate(activity) {

            $('#message_' + activity).html('');

            if ($("select[name=" + activity + "]").val() == "") {
                $('#message_' + activity).html('<span style="color: red;">Please select template</span>');
                return;
            }

            var template = $("select[name=" + activity + "]").val();

            window.location.href = "{{ route('clients.viewtemplate', $client)}}/" + template;

        }

        function submitTemplate(activity) {

            if ($("select[name=" + activity + "]").val() == "") {
                $('#message_' + activity).html('<span style="color: red;">Please select template</span>');
                return;
            }

            $('#message_' + activity).html('<span style="color: red;">Sending, please wait ...</span>');
            var data = {
                email: $("input[name=" + activity + "]").val(),
                template_file: $("select[name=" + activity + "]").val()
            };

            axios.post('{{route('clients.sendtemplate',$client)}}/' + activity, data)
                .then(function (data) {
                    $('#message_' + activity).html('<span style="color: green">Template sent successfully.</span>');
                })
                .catch(function () {
                    $('#message_' + activity).html('<span style="color: red">There was a problem with this request.</span>');
                });
        }

        function sendNotification(activity) {
            $('#message_' + activity).html('<span style="color: red;">Sending ...</span>');

            axios.post('{{route('clients.sendnotification',$client)}}/' + activity)
                .then(function () {
                    $('#message_' + activity).html('<span style="color: green">Notifications sent successfully.</span>');
                })
                .catch(function () {
                    $('#message_' + activity).html('<span style="color: red">There was a problem with this request.</span>');
                });
        }

        function completeStep(step) {
            $('#step' + step).html('<span style="color: red;">Updating ...</span>');

            axios.post('{{route('clients.completestep',$client)}}/' + step)
                .then(function (data) {
                    var auto_completed_values = data["data"].activities_auto_completed;
                    for(i = 0; i< auto_completed_values.length; i++){
                        $("#step_header_"+step).css("background-color", '#3CFF463f');
                        $("#list_"+auto_completed_values[i]).css("background-color", '#3CFF463f');
                    }
                    $('#step_' + step).html('<span style="color: green">Done &nbsp;</span>');
                })
                .catch(function () {
                    $('#step_' + step).html('<span style="color: red">Error &nbsp;</span>');
                });
        }
    </script>
@endsection