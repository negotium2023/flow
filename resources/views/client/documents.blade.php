@extends('client.show')

@section('title') Document Vault @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <div class="nav-btn-group">
            <a href="{{route('documents.create',['client'=>$client->id,'process_id'=>$process_id,'step_id'=>$step["id"]])}}" class="btn btn-primary">Document</a>
            <button type="button" id="modal-launcher" class="btn btn-success" style="margin-left:10px;display: none" data-toggle="modal" data-target="#emails">
                Send Documents
            </button>
        </div>
    </div>
@endsection

@section('tab-content')
    <div class="client-detail">
    <div class="content-container m-0 p-0">
        @yield('header')
        <div class="container-fluid index-container-content">
            <div class="table-responsive h-100">
                <table class="table table-bordered table-sm table-hover table-fixed">
                    <thead>
                    <tr>
                        <th>Name <span id="more-info" data-toggle="tooltip" data-placement="top" title="Tick a checkbox to select a document">?</span></th>
                        <th>Type</th>
                        <th>Size</th>
                        <th>Uploader</th>
                        <th>Added</th>
                        <th class="last">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($client->documents as $document)
                        <tr>
                            <td>
                                @if($document->form_process_id > 0)
                                    <input class="select-document" type="checkbox" value="{{$document->id}}"> <a href="{{route('form',['client'=>$document->client_id,'q'=>$document->file])}}" download="{{$document->name}}.{{$document->type()}}">{{$document->name}}</a>
                                @else
                                    <input class="select-document" type="checkbox" value="{{$document->id}}"> <a href="{{route('document',['q'=>$document->file])}}" download="{{$document->name}}.{{$document->type()}}">{{$document->name}}</a>
                                @endif
                            </td>
                            <td>{{$document->type()}}</td>
                            <td>{{$document->size()}}</td>
                            <td>@if($document->user_id != 0) <a href="{{route('profile',$document->user)}}" title="{{$document->user->name()}}"><img src="{{route('avatar',['q'=>$document->user->avatar])}}" class="blackboard-avatar blackboard-avatar-inline" alt="{{$document->user->name()}}"/></a> @else Uploaded by client @endif</td>
                            <td>{{\Carbon\Carbon::parse($document->updated_at)->format('d/m/Y')}}</td>
                            <td class="last">
                                <a href="{{route('documents.edit',['document_id'=>$document,'client'=>$client->id,'process_id'=>$process_id,'step_id'=>$step["id"]])}}" class="btn btn-success btn-sm"><i class="fas fa-pencil-alt"></i></a>
                                {{ Form::open(['method' => 'DELETE','route' => ['documents.destroy','id'=>$document->id,'client_id' =>$document->client_id,'process_id'=>$process_id,'step_id'=>$step["id"]],'style'=>'display:inline']) }}
                                <a href="#" class="delete deleteDoc btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
                                {{Form::close() }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="100%" class="text-center">No documents match those criteria.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

            <div class="modal fade" id="emails" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Enter Email Addresses You Want To Send To</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div id="body-result" style="display: none;">
                            </div>
                            <div id="body-wrapper">
                                <div class="alert alert-info" role="alert">
                                    If you want to enter multiple emails please seperate them by a comma character (,)
                                </div>
                                <div class="alert alert-danger" id="error"></div>
                                <div id="show-emails"><ul></ul></div>
                                <div class="form-row">
                                    <div class="form-group col-md-6 pb-0">
                                        <label for="email-addresses">Email Address</label>
                                        <input class="form-control form-control-sm" type="text" id="email-addresses">
                                    </div>
                                    <div class="form-group col-md-6 pb-0">
                                        <label for="email-subject">Email Subject</label>
                                        <input class="form-control form-control-sm" type="text" id="email-subject">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12 pt-0 pb-0">
                                        <label for="">Email Body</label>
                                        <div class="w-100">
                                            <textarea id="email-body" class="form-control form-control-sm my-editor w-100"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="loader" style="display: none">
                                <div class="lds-ring" ><div></div><div></div><div></div><div></div></div>
                                <strong>Sending...</strong>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button id="send"  class="btn btn-success">Send</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('extra-css')
    <style>
        .modal-body>#loader>strong{
            display: block;
            text-align: center;
        }
        .lds-ring {
            display: block;
            position: relative;
            width: 80px;
            height: 80px;
            margin-left: auto;
            margin-right: auto;
        }
        .lds-ring div {
            box-sizing: border-box;
            display: block;
            position: absolute;
            width: 64px;
            height: 64px;
            margin: 8px;
            border: 8px solid #4c5e77;
            border-radius: 50%;
            animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
            border-color: #4c5e77 transparent transparent transparent;
        }
        .lds-ring div:nth-child(1) {
            animation-delay: -0.45s;
        }
        .lds-ring div:nth-child(2) {
            animation-delay: -0.3s;
        }
        .lds-ring div:nth-child(3) {
            animation-delay: -0.15s;
        }
        @keyframes lds-ring {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
        #more-info{
            border: 1px solid #fff;
            padding: 0 8px;
            display: inline-block;
            margin-left: 10px;
            border-radius: 50px;
            transition: background 0.4s ease;
            transition: color 0.4s ease;
            cursor: pointer;
        }
        #more-info:hover{
            background: #ffffff;
            color: #343a40;
        }
        .second-toolbar{
            display: none!important;
        }
        .fr-element>p{
            margin-bottom: 0!important;
        }

        #error{
            display: none;
        }
        .select-document{
            margin-right: 1rem;
            margin-left: 15px;
        }
        #show-emails>ul{
            list-style: none;
        }
        #show-emails>ul>li{
            padding: 5px 10px;
            border-left: 3px solid rgb(76, 94, 119);
            background: rgba(76, 94, 119, .08);
            margin-bottom: 3px;
        }
        .remove{
            padding: 0 .6rem;
            cursor: pointer;
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

        .progress { position:relative; width:100%; border: 1px solid #7F98B2; padding: 1px; border-radius: 3px; display:none; }
        .bar { background-color: #B4F5B4; width:0%; height:25px; border-radius: 3px; }
        .percent { position:absolute; display:inline-block; top:3px; left:48%; color: #7F98B2;}
    </style>
@endsection
@section('extra-js')
    <script>
        let documents = [];
        $('.select-document').on('click', function () {
            if ($(this).is(':checked')){
                documents.push($(this).val())
                if (documents.length > 0){
                    $('#modal-launcher').show();
                    $('#body-result').hide();
                    $('#body-wrapper, #send').show();
                    tinymce.init(editor_config);
                }
            }else{
                for( var i = 0; i < documents.length; i++){
                    if ( documents[i] == $(this).val()) {
                        documents.splice(i, 1);
                    }
                }
                if (documents.length === 0) $('#modal-launcher').hide();
            }
        })

        let myEmails = $('#email-addresses');
        let keynum;
        let emailList = [];
        let subject;
        const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

        myEmails.on('keyup', function(e) {

            if(window.event){ // IE
                keynum = e.keyCode;
            }
            else if(e.which){ // Netscape/Firefox/Opera
                keynum = e.which;
            }
            if (keynum == 188){
                let email = $(this).val().replace(',','');
                if (re.test(String(email).toLowerCase())){
                    emailList.push(email);
                    let displayList = '';

                    uniqueEmails(emailList).forEach((value, ind) => {
                        displayList += "<li>"+value+"<span class='float-right remove' onclick=\"removeEmail("+ind+")\"><i class=\"fas fa-times\"></i></span></li>"
                    } )
                    $("#show-emails ul").html(displayList);
                    $(this).val('');
                }else{
                    $("#error").show().html(
                        "Please enter a valid email address"
                    );
                    $(this).val().replace(',','');
                }
            }
        })

        $('#send').on('click', () =>{
            if (myEmails.val() != ''){
                if (re.test(String(myEmails.val().replace(',', '')).toLowerCase())){
                    emailList.push(myEmails.val().replace(',', ''))
                }else{
                    $("#error").show().html("Please enter a valid email address");
                    $(this).val().replace(',','');
                    return false;
                }
            }
            if (uniqueEmails(myEmails).length === 0){
                $('#error').show().html("Please enter at least one email address");
                return false;
            }
            if ($("#email-subject").val() != ''){
                subject = $("#email-subject").val();
            }else{
                $('#error').show().html("Please enter the subject of the email");
                return false;
            }

            if (uniqueEmails(myEmails).length > 0 && $("#email-subject").val() != ''){
                $('#body-wrapper, #send').hide();
                $("#loader").show();
            }


            axios.post('/senddocuments', {
                documentsID: documents,
                emails: uniqueEmails(myEmails),
                subject: subject,
                body: $("#emails").find("#email-body").val()
            })
                .then(function (response) {
                    $("#body-wrapper").hide();
                    $("#body-result").show().html(
                        "<div class='alert alert-success'>Your documents have been sent successfully</div>"
                    );
                    $("#loader").hide();
                    setTimeout(() => {
                        $('#emails').modal('hide')
                        $('#emails').find('#email-addresses').val('');
                        $('#emails').find('#email-subject').val('');
                        $('#emails').find('#email-body').val('');
                        tinymce.get('email-body').setContent('');
                        $("#loader").hide();
                        $("#body-result").hide();
                    }, 1500);
                    $('.select-document').each(function(i, obj) {
                        obj.checked = false;
                    });
                    $("#modal-launcher").hide();
                    documents = [];
                    myEmails = [];
                })
                .catch(function (error) {
                    $('#error').show().html("Something went wrong");
                    setTimeout(() =>{
                        $('#error').hide();
                        $("#loader").hide();
                        $('#body-wrapper, #send').show();
                    }, 1500);
                });
        })

        function removeEmail(index) {
            emailList.splice(index, 1);
            let displayList = '';
            uniqueEmails(emailList).forEach((value, ind) => {
                displayList += "<li>"+value+"<span class='float-right remove' onclick=\"removeEmail("+ind+")\"><i class=\"fas fa-times\"></i></span></li>"
            } )
            $("#show-emails ul").html(displayList);
        }

        function uniqueEmails(emails) {
            let uniqueEmails = [];
            $.each(emailList, function(i, el){
                if($.inArray(el, uniqueEmails) === -1) uniqueEmails.push(el);
            });

            return uniqueEmails;
        }
    </script>
@endsection