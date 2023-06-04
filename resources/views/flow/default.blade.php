<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{!! asset('storage/favicon.ico') !!}" type="image/x-icon"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <title>{{env('APP_NAME')}}</title>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{!! asset('fontawesome/css/all.css') !!}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{!! asset('adminlte/dist/css/adminlte.min.css') !!}">
    <link rel="stylesheet" href="{!! asset('css/uhy-international.css') !!}">
    <link rel="stylesheet" href="{!! asset('css/progress-wizard.min.css') !!}">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@4.x/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{!! asset('css/jquery-ui.css') !!}">
    <link rel="stylesheet" href="{!! asset('css/perfect-scrollbar.css') !!}">
    <link rel="stylesheet" href="{!! asset('css/bootstrap/bootstrap-multiselect.css') !!}">
    <link rel="stylesheet" href="{{asset('chosen/chosen.min.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    {{--<link href="https://cdn.jsdelivr.net/npm/froala-editor@3.1.0/css/froala_editor.pkgd.min.css" rel="stylesheet" type="text/css" />--}}
    <link rel="stylesheet" href="https://unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css">
    <style>
        .ui-tooltip, .arrow:after {
            background: black !important;
            border: 0px solid transparent !important;
        }
        .ui-tooltip {
            padding: 5px 10px;
            color: white !important;
            font-size: 11px !important;
            font-weight:normal !important;
            border-radius: 5px;
            text-transform: uppercase;
            box-shadow: 0 0 7px black;
        }
        .arrow {
            width: 60px;
            height: 14px;
            overflow: hidden;
            position: absolute;
            left: 50%;
            margin-left: 0px;
            bottom: -10px;
        }
        .arrow.top {
            top: -16px;
            bottom: auto;
        }
        .arrow.left {
            left: 20%;
        }
        .arrow:after {
            content: "";
            position: absolute;
            left: 20px;
            top: -20px;
            width: 25px;
            height: 25px;
            box-shadow: 6px 5px 9px -9px black;
            -webkit-transform: rotate(45deg);
            -ms-transform: rotate(45deg);
            transform: rotate(45deg);
        }
        .arrow.top:after {
            bottom: -20px;
            top: auto;
        }

        /* =Tooltip Style -------------------- */

        /* Tooltip Wrapper */
        .has-tooltip {
            position: relative;
        }
        .has-tooltip .tooltip2 {
            opacity: 0;
            visibility: hidden;
            -webkit-transition: visibility 0s ease 0.5s,opacity .3s ease-in;
            -moz-transition: visibility 0s ease 0.5s,opacity .3s ease-in;
            -o-transition: visibility 0s ease 0.5s,opacity .3s ease-in;
            transition: visibility 0s ease 0.5s,opacity .3s ease-in;
        }
        .has-tooltip:hover .tooltip2 {
            opacity: 1;
            visibility: visible;
        }

        /* Tooltip Body */
        .tooltip2 {
            background-color: #222;
            bottom: 130%;
            color: #fff;
            font-size: 14px;
            left: -100%;
            margin-left: 0px;
            padding: 6px;
            position: absolute;
            text-align: left;
            text-decoration: none;
            text-shadow: none;
            width:auto;
            min-width: 600px;
            max-width: 1000px;
            overflow: auto;
            z-index: 4;
            -webkit-border-radius: 3px;
            -moz-border-radius: 3px;
            -o-border-radius: 3px;
            border-radius: 3px;
        }

        /* Tooltip Caret */
        .tooltip2:after {
            border-top: 5px solid #222;
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            bottom: -5px;
            content: " ";
            font-size: 0px;
            left: 25px;
            line-height: 0%;
            margin-left: -4px;
            position: absolute;
            width: 0px;
            z-index: 1;
        }

        .tooltip2 ol,.tooltip2 ul,.tooltip2 li{
            text-align: left;
            /*margin: 0px;
            padding:0px;*/
        }

        .wrapper, body, html {
            min-height: 100%;
            overflow-x: unset;
        }
    </style>
    @yield('extra-css')
</head>
<!--
BODY TAG OPTIONS:
=================
Apply one or more of the following classes to to the body tag
to get the desired effect
|---------------------------------------------------------|
|LAYOUT OPTIONS | sidebar-collapse                        |
|               | sidebar-mini                            |
|---------------------------------------------------------|
-->
<body class="hold-transition sidebar-mini">
<div id="overlay">
    <div class="spinner"></div>
    <br/>
    Loading...
</div>
<div id="overlay2" style="display: none;">

</div>
<div id="app" class="wrapper">
    @if(auth()->check() && auth()->user()->trial == 1)
        <attooh-trial-notification></attooh-trial-notification>
        <p id="countdown" style="text-align: center; position: absolute;top: 0;left: 0;z-index: 99999;"></p>
    @endif

    @include('flow.sidebar')
<!-- Content Wrapper. Contains page content -->
    @include('flow.header')
    <div class="content-wrapper main-wrapper">
        <!-- SETUP WIZARD -->

        <!-- Only display setup wizard if role is financial advisor -->
        @role('financialadvisor2')
        <blackboard-wizard></blackboard-wizard>
        @endrole

        @yield('content')
    </div>
    <!-- /.content-wrapper -->

        <div class="modal fade" id="edit_email_template">
            <div class="modal-dialog" style="width:800px !important;max-width:800px;">
                <div class="modal-content">
                    <div class="modal-header text-center" style="border-bottom: 0px;padding:.5rem;">
                        <h5 class="modal-title">View Email Template</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                        <div class="box-body">
                            <div class="form-group">
                                <input type="hidden" class="form-control" name="email_id" id="email_id" >
                                <input type="hidden" class="form-control" name="activity_id" id="activity_id" >
                            </div>
                            <div class="form-group mt-3">
                                {{Form::label('name', 'Name')}}
                                {{Form::text('name',null,['class'=>'form-control','placeholder'=>'Name','id'=>'email_title'])}}

                            </div>

                            <div class="form-group">
                                {{Form::label('Email Body')}}
                                {{ Form::textarea('email_content', null, ['class'=>'form-control my-editor','size' => '30x10','id'=>'email_content']) }}

                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-default btn-sm pull-left" data-dismiss="modal">Close</button>
                                <button type="button" onclick="saveEmailTemplate()" class="btn btn-sm btn-primary">Use Template</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="billing" style="z-index: 9999;position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%);">
            <div class="modal-dialog" style="width:400px !important;max-width:800px;">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="box-body" style="text-align: center;">
                            <strong>Access denied.</strong> Please contact the account owner.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="confirmModal">
            <div class="modal-dialog" style="width:450px !important;max-width:450px;">
                <div class="modal-content">
                    <div class="modal-header text-center" style="border-bottom: 0px;padding:.5rem;">
                        <h5 class="modal-title">Confirmation</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body mx-3">
                        <div class="form-group text-center">
                            {{Form::label('popup', '',['id'=>'confirmMessage'])}}

                        </div>
                        <div class="form-group text-center">
                            <button type="button" class="btn btn-sm btn-default" id="confirmOk">Yes</button>
                            <button type="button" class="btn btn-sm btn-default" id="confirmCancel">No</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="notifyModal">
            <div class="modal-dialog" style="width:450px !important;max-width:450px;">
                <div class="modal-content">
                    <div class="modal-header text-center" style="border-bottom: 0px;padding:.5rem;">
                        <h5 class="modal-title"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body mx-3">
                        <div class="form-group text-center">
                            {{Form::label('popup', '',['id'=>'notifyMessage'])}}

                        </div>
                        <div class="form-group text-center">
                            <button type="button" class="btn btn-sm btn-default" id="notifyOk">Ok</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<script src="{!! asset('js/jquery/jquery.min.js') !!}" type="text/javascript"></script>
<script src="{!! asset('js/jquery/jquery-ui.js') !!}" type="text/javascript"></script>
<script src="{!! asset('js/popper.min.js') !!}" type="text/javascript"></script>
<script src="{!! asset('js/bootstrap/bootstrap.min.js') !!}" type="text/javascript"></script>

<!-- AdminLTE -->
<script src="{!! asset('adminlte/dist/js/adminlte.js') !!}" type="text/javascript"></script>
<script src="{!! asset('adminlte/dist/js/jscolor.js') !!}" type="text/javascript"></script>
<script src="{!! asset('adminlte/dist/js/custom.js') !!}" type="text/javascript"></script>

<!-- OPTIONAL SCRIPTS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/js/all.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css" rel="stylesheet"/>

<script src="{!! asset('js/moment.min.js') !!}" type="text/javascript"></script>
<script src="https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=361nrfmxzoobhsuqvaj3hyc2zmknskzl4ysnhn78pjosbik2"></script>
<script src="{!! asset('js/tinymce/vue-tinymce.js') !!}" type="text/javascript"></script>

@auth
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
    <!-- SortableJS -->
    <script src="https://unpkg.com/sortablejs@1.4.2"></script>
    <!-- VueSortable -->
    <script src="https://unpkg.com/vue-sortable@0.1.3"></script>
@endauth

<script src="{!! asset('chosen/chosen.jquery.min.js') !!}" type="text/javascript"></script>
<script src="{!! asset('chosen/docsupport/init.js') !!}" type="text/javascript" charset="utf-8"></script>
<script src="{!! asset('js/autocomplete.js') !!}" type="text/javascript"></script>
<script src="{!! asset('js/bootstrap/bootstrap-multiselect.min.js') !!}" type="text/javascript"></script>
<script src="{!! asset('js/perfect-scrollbar.js') !!}" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <script>
    $(window).on('load', function () {
        $('.nav-client').css('transition','unset');
        $('.client-sidemenu').css('transition','unset');
        $('.client-content').css('transition','unset');
        var clientSidebar = localStorage.getItem('clientSidebar');
        var clientId = localStorage.getItem('clientId');

        if($("#clientSidebarId").length) {
            if (clientId === $("#clientSidebarId").val()) {

                if (clientSidebar === "No") {
                    $('.client-sidemenu').addClass('hideClient');
                    $('.client-info').addClass('hide-client-info');
                    $('.nav-client').addClass('nav-client-expand');
                    $('.client-content').addClass('client-content-expand');
                    $('#client-sidebar-btn').html('<span class="fa fa-angle-left"></span>')
                }
            }
        }

    setTimeout(removeLoader, 500); //wait for page load PLUS two seconds.
    });

    function removeLoader() {

    $("#overlay").fadeOut(300, function () {

        /*$("#overlay2").fadeIn();
        $("#billing").modal({backdrop: 'static', keyboard: false},'show');*/
        $(".client-content").show();
        $(".client-capture-content").show();


        $('.client-sidemenu').css('opacity',1);
        $('.client-content').css('opacity',1);
        $('.nav-client').css('opacity',1);

    });
    }

    function hideClientInfo(){
        var clientId = localStorage.getItem('clientId');
        if(clientId !== $("#clientSidebarId").val()) {
            localStorage.setItem('clientId', $("#clientSidebarId").val());
        }
        $('.nav-client').css('transition','margin-left 2s');
        $('.client-sidemenu').css('transition','margin-left 2s');
        $('.client-content').css('transition','margin-left 2s');

        if($('.client-sidemenu').hasClass('hideClient')){
            $('.client-sidemenu').removeClass('hideClient');
            $('.client-info').removeClass('hide-client-info');
            $('.nav-client').removeClass('nav-client-expand');
            $('.client-content').removeClass('client-content-expand');
            $('#client-sidebar-btn').html('<span class="fa fa-angle-left"></span>')
            localStorage.setItem('clientSidebar', 'Yes');
            localStorage.setItem('clientId', $("#clientSidebarId").val());
        } else {
            $('.client-sidemenu').addClass('hideClient');
            $('.client-info').addClass('hide-client-info');
            $('.nav-client').addClass('nav-client-expand');
            $('.client-content').addClass('client-content-expand');
            $('#client-sidebar-btn').html('<span class="fa fa-angle-right"></span>')
            localStorage.setItem('clientSidebar', 'No');
            localStorage.setItem('clientId', $("#clientSidebarId").val());
        }
    }
    </script>

<script>

    $(document).ready(function(){


        var active_elem = $(".nav-sidebar").find(".active");

        $(active_elem).parent('li').parent('ul').parent('li').addClass('menu-open');
        //$(active_elem).parent('li').parent('ul').addClass('test');

        $(".delete").on("click", function(e){
            e.preventDefault();
            if (!confirm("Are you sure you want to delete this record?")){
                return false;
            } else {
                $(this).parents('form:first').submit();
            }

        });

        $('#admin-menu').on('click',function(e){
            //alert();
            $(".nav-sidebar").find('li.admin-menu').toggle();
        })

        //show overlay
        @if(!strpos($_SERVER['REQUEST_URI'],'progress') && !strpos($_SERVER['REQUEST_URI'],'overview') && !strpos($_SERVER['REQUEST_URI'],'basket'))
        $(document).ajaxStart(function() {
            $('#overlay').fadeIn();
        });
        $(document).ajaxStop(function() {
            $('#overlay').fadeOut();
            $(".client-content").show();
        });
        @endif

        $('[data-toggle="tooltip"]').tooltip({
            items: "[data-original-title]",
            content: function() {
                var element = $( this );
                if ( element.is( "[data-original-title]" ) ) {
                    return element.attr( "title" );
                }
            },
            position: {
                my: "center bottom-10", // the "anchor point" in the tooltip element
                at: "center top", // the position of that anchor point relative to selected element
                using: function( position, feedback ) {
                    $( this ).css( position );
                    $( "<div>" )
                        .addClass( "arrow" )
                        .addClass( feedback.vertical )
                        .addClass( feedback.horizontal )
                        .appendTo( this );
                }
            }
        });



        $(".step-dropdown").change(function(){

            var url = $('option:selected',this).data('path');
            window.location.href = url;
        });

        $("#viewprocess").on('change', function () {
            let client_id = $('#client_id').val();
            let process_id = $('#viewprocess').val();
            let step_id = 0;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "GET",
                url: '/clients/getfirststep/' + client_id + '/' + process_id,
                success: function( data ) {
                    window.location.href = '/clients/' + client_id + '/progress/' + process_id + '/' + data;
                }
            });
        });

        $('.confirmExtraEmail').on('keypress',function(e){
            let email = $('#confirmEmailModal').find('.confirmExtraEmail').val();
            if(e.which === 13) {
                if (validateEmail(email)) {
                    $('#confirmEmailModal').find('.confirmExtraEmail').removeClass('is-invalid');
                    $('#confirmEmailModal').find('#confirmEmails').append('<li>' + email + '</li>');
                    $('#confirmEmailModal').find('.all-emails').append('<input type="hidden" name="extra-emails[]" value="' + email + '">');
                    $('#confirmEmailModal').find('.confirmExtraEmail').val('');
                } else {
                    $('#confirmEmailModal').find('.confirmExtraEmail').addClass('is-invalid');
                }
            }
        });

        $('#changeprocesscancel').on('click',function(){
            $('#modalChangeProcess').modal('hide');
        });

        $('input[type=radio][name=clientf]').change(function() {
            $(this).closest('form').submit();
        });

        $('#getApplicationDoc').on('click',function(){

            let i = 0;
            let cnt = $("#modalAllProcesses").find('.signature_checkbox:checked').length;
            let row = '';

            $('#overlay').show();

            function next(clientid,processid){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                });

                console.log('/api/signiflow/getsigniflowdocument/'+clientid+'/1/'+processid);

                $.ajax({
                    url: '/api/signiflow/getsigniflowdocument/'+clientid+'/1/'+processid,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        if(data.success == 0){
                            alert(data.error_message);
                        }

                        cnt = cnt - 1;
//console.log(data);
                        row = row + '<tr>';
                        // row = row + '<td>&bull;</td><td>'+data.message+'</td>';
                        //row = row + '<td>&bull;</td><td><a href="/storage/documents/processed_applications/'+clientid+'/'+data+'" style="padding-left:10px">'+data+'</a></td>';
                        row = row + '</tr>';
                        if(i === cnt) {
                            // run function here as its the last item in array
                            $('#overlay').hide();
                            /*$("#modalAllProcesses").modal('hide');*/
                            let process_id = $("#modalAllProcesses").find('#all_processes_process_id').val();
                            let step_id = $("#modalAllProcesses").find('#all_processes_step_id').val();

                            $("#modalAllProcesses").find('.modal-title').html('Sent for Signatures');
                            $("#modalAllProcesses").find('.instruction').html('All applications were successfullly submitted for signatures.<br />You can view them by navigating to the client documents tab by clicking <a href="/clients/'+clientid+'/documents/'+process_id+'/'+step_id+'/0"><strong>here</strong></a>.');
                            $("#modalAllProcesses").find('.btn-div').hide();
                            $("#modalAllProcesses").find('#all_processes').html(row);
                        }
                    },
                    error: function (data) {
                        alert('An error occurred while generating document(s): ' + data.statusText);
                        console.log('Error:', data);
                    }
                });
            }

            /*function next(clientid,processid){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                });

                $.ajax({
                    url: '/clients/submit_for_signature/' + clientid + '/' + processid,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        cnt = cnt - 1;

                        row = row + '<tr>';
                        row = row + '<td>&bull;</td><td><a href="/storage/documents/processed_applications/'+clientid+'/'+data+'" style="padding-left:10px">'+data+'</a></td>';
                        row = row + '</tr>';
                        if(i === cnt) {
                            // run function here as its the last item in array
                            $('#overlay').hide();
                            //$("#modalAllProcesses").modal('hide');
                            let process_id = $("#modalAllProcesses").find('#all_processes_process_id').val();
                            let step_id = $("#modalAllProcesses").find('#all_processes_step_id').val();

                            $("#modalAllProcesses").find('.modal-title').html('Sent for Signatures');
                            $("#modalAllProcesses").find('.instruction').html('All applications were successfullly submitted for signatures.<br />You can view them by clicking on the filename below or by navigating to the client documents tab by clicking <a href="/clients/'+clientid+'/documents/'+process_id+'/'+step_id+'"><strong>here</strong></a>.');
                            $("#modalAllProcesses").find('.btn-div').hide();
                            $("#modalAllProcesses").find('#all_processes').html(row);
                        }
                    },
                    error: function (data) {

                    }
                });
            }*/

            $("#modalAllProcesses").find('.signature_checkbox:checked').each(function () {
                let clientid = $(this).data('client');
                let processid = $(this).data('process');

                if(i === cnt) {
                    // run function here as its the last item in array
                } else {
                    // do the next ajax call
                    next(clientid,processid);
                    // alert(clientid + ' - ' + processid);
                }
            });


            /**/
        });

        $('#client_type').on('change',function(){
            $('#overlay').show();
            $(".client-index").hide();
            $( this ).closest( "form" ).submit();
        });

        $('#parent_branch').on('change',function(){
            $('#overlay').show();
            $(".client-index").hide();
            $( this ).closest( "form" ).submit();
        });

        $('#fund_consultant').on('change',function(){
            $('#overlay').show();
            $(".client-index").hide();
            $( this ).closest( "form" ).submit();
        });

    });

    (function($) {

        $(".cata-sub-nav").on('scroll', function() {
            $val = $(this).scrollLeft();

            if($(this).scrollLeft() + $(this).innerWidth()>=$(this)[0].scrollWidth){
                $(".nav-next").hide();
            } else {
                $(".nav-next").show();
            }

            if($val == 0){
                $(".nav-prev").hide();
            } else {
                $(".nav-prev").show();
            }
        });
        var w = $('.sidebar').outerWidth(true);

        $(".nav-next").on("click", function(){

            $(".cata-sub-nav").animate( { scrollLeft: '+=460' }, 200);
        });
        $(".nav-prev").on("click", function(){
            $(".cata-sub-nav").animate( { scrollLeft: '-=460' }, 200);
        });
    })(jQuery);

    function validateEmail(sEmail) {
        var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if(!regex.test(sEmail)) {
            return false;
        }else{
            return true;
        }
    }

    function confirmDialog(message, onConfirm){
        var fClose = function(){
            modal.modal("hide");
        };

        var fClose2 = function(){
            modal.modal("hide");
            if ($(document).find('#modalSendTemplate').hasClass('show')) {
                $('#modalSendTemplate').find('#sendtemplatecomposeemailsend').attr("disabled", false);
                $('#modalSendTemplate').find('.sendtemplatecancel').attr("disabled", false);
                $('#modalSendTemplate').find('#sendcomposemessage').html('');
                $('#modalSendTemplate').find('#sendtemplatetemplateemailsend').attr("disabled", false);
                $('#modalSendTemplate').modal('hide');
            }
            if ($(document).find('#modalSendDocument').hasClass('show')) {
                $('#modalSendDocument').find('#senddocumentcomposeemailsend').attr("disabled", false);
                $('#modalSendDocument').find('.senddocumentcancel').attr("disabled", false);
                $('#modalSendDocument').find('#sendcomposemessaged').html('');
                $('#modalSendDocument').find('#senddocumenttemplateemailsend').attr("disabled", false);
                $('#modalSendDocument').modal('hide');
            }
            if ($(document).find('#modalSendMA').hasClass('show')) {
                $('#modalSendMA').find('#sendmatemplateemailsend').attr("disabled", true);
                $('#modalSendMA').find('.sendmacancel').attr("disabled", true);
                $('#modalSendMA').find('#sendmamessage').html('');
                $('#modalSendMA').find('#sendmacomposeemailsend').attr("disabled", false);
                $('#modalSendMA').modal('hide');
            }
        };

        var modal = $("#confirmModal");
        modal.modal("show");
        $("#confirmMessage").empty().append(message);
        $("#confirmOk").unbind().one('click', onConfirm).one('click', fClose);
        $("#confirmCancel").unbind().one("click", fClose2);
    }

    function confirmEmailDialog(message, client_id, email, onConfirm){
        var fClose = function(){
            modal.modal("hide");
        };

        var modal = $("#confirmEmailModal");
        modal.modal("show");

        if(email.length > 0){
            $("#confirmEmails").empty().append('<li>'+email+'</li>');
        }

        $('.all-emails').append('<input type="hidden" name="extra-emails[]" value="'+email+'" />');
        $("#confirmEmailClient").val(client_id);
        $("#confirmEmailMessage").empty().append(message);

        $("#confirmEmailOk").unbind().one('click', onConfirm).one('click', fClose);
        $("#confirmEmailCancel").unbind().one("click", fClose);
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

    function completePrimary(client_id, url) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: url,
            data: {client_id: client_id},
            success: function (data) {
                if (data.message === 'Success') {
                    let YOUR_MESSAGE_STRING_CONST = "Are you sure you want to complete the instruction for this Primary Client?";

                    confirmDialog(YOUR_MESSAGE_STRING_CONST, function () {

                        $.ajax({
                            type: "POST",
                            url: '/client/complete/' + client_id,
                            data: {client_id: client_id},
                            success: function (data) {
                                if (data.message === 'Success') {

                                    //$('.flash_msg').html('<div class="alert alert-success">Primary Client successfully completed</div>');
                                } else {
                                    $('.flash_msg').html('<div class="alert alert-danger">An error occured while trying to complete the instruction.</div>');
                                }

                                setTimeout(function () {
                                    window.location.reload();
                                }, 2000);
                            }
                        });
                    });
                } else {
                    let YOUR_MESSAGE_STRING_CONST = "Not all required fields have been captured.";

                    notifyDialog(YOUR_MESSAGE_STRING_CONST);
                }
            }
        });
    }

    function sendClientEmail(client_id, client_email) {

        let YOUR_MESSAGE_STRING_CONST;

        if(client_email.length > 0) {
            YOUR_MESSAGE_STRING_CONST = "Are you sure you want to send an email to the following recipients?";
        } else {
            YOUR_MESSAGE_STRING_CONST = "";
        }


        confirmEmailDialog(YOUR_MESSAGE_STRING_CONST, client_id, client_email, function () {
            var emails = $('input[name="extra-emails[]"]').map(function(){
                return this.value;
            }).get();

            var process_id = $('#process_id').val();
            var step_id = $('#step_id').val();

            $('#overlay').fadeIn();

            $.ajax({
                type: "POST",
                url: '/client/' + client_id +'/sendclientemail',
                data: {client_id: client_id,emails:emails,process_id:process_id,step_id:step_id},
                success: function (data) {
                    if (data.message === 'Success') {
                        toastr.success('<strong>Success!</strong> '+data.success_msg);

                        toastr.options.timeOut = 1000;
                    } else {
                        toastr.error('<strong>Error!</strong> An error occured while trying to send the email.');

                        toastr.options.timeOut = 1000;
                    }
                    $('.all-emails').empty().append('<ul id=\'confirmEmails\'>\n' +
                        '\n' +
                        '                        </ul>');
                    /*setTimeout(function () {
                        window.location.reload();
                    }, 2000);*/
                    $('#overlay').fadeOut();
                }
            });
        });
    }

    function submitForSignatures(client_id, process_id, step_id){

        let clientid = client_id;
        let processid = process_id;
        let stepid = step_id;

        $("#modalAllProcesses").find('.modal-title').html('Send for Signatures');
        $("#modalAllProcesses").find('.instruction').html('Please select the applications you would like to submit for signatures.');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: '/clients/' + client_id + '/get_forms',
            type: "GET",
            dataType: "json",
            success: function (data) {
                $("#modalAllProcesses").modal('show');

                let row = '';
                $.each(data, function(key, value) {
                    row = row + '<tr><td style="vertical-align: top;padding-bottom: 7px;"><input type="checkbox" class="signature_checkbox" name="signature_checkbox[]"  data-client="'+client_id+'" data-process="'+value.process_id+'" data-href="clients/submit_for_signature/'+ client_id + '/'+value.process_id+'" /></td><td style="padding-left: 1rem;padding-bottom: 7px;">' + value.name + '</td></tr>';
                    /*row = row + '<li><a href="javascript:void(0)" onclick="getApplicationDoc('+client_id+','+value.process_id+')" data-client-id="'+client_id+'" data-href="clients/submit_for_signature/'+ client_id + '/'+value.process_id+'">' + value.name + '</a></li>';*/
                });

                $("#modalAllProcesses").find('#all_processes_process_id').val(processid);
                $("#modalAllProcesses").find('#all_processes_step_id').val(stepid);
                $("#modalAllProcesses").find('.btn-div').show();
                $("#modalAllProcesses").find('#all_processes').html(row);
            }
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
                $("#modalChangeProcess").find('.modal-body').css("overflow","visible");
                $("#modalChangeProcess").find('#move_to_process_new').empty();
                $("body").find('#move_to_process_new').trigger('chosen:updated');
                $("#modalChangeProcess").find('.client_id').val(client_id);
                $("#modalChangeProcess").find('.process_id').val(process_id);

                $.each(data, function(key, value) {
                    $("#modalChangeProcess").find('#move_to_process_new').append($("<optgroup></optgroup>").attr("label",key).attr("id",key.replace(/\ /g,'').toLowerCase()));
                    $.each(value, function(k, v) {
                        if(v.existing === '1'){

                        } else {
                            $("#modalChangeProcess").find('#' + key.replace(/\ /g, '').toLowerCase()).append($("<option></option>").attr("value", v.id).text(v.name));
                        }
                    });
                });

                $("body").find('#move_to_process_new').trigger('chosen:updated');

            }
        });
    }

    function startNewForm(client_id,process_id) {
        let clientid = client_id;
        let processid = process_id;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: '/clients/getnewform/'+clientid,
            type:"GET",
            dataType:"json",
            success:function(data){
                /*$('#modalSendTemplate').modal('hide');*/
                $("#modalChangeForm").modal('show');
                $("#modalChangeForm").find('.modal-body').css("overflow","visible");
                $("#modalChangeForm").find('#move_to_form_new').empty();
                $("#modalChangeForm").find('.client_id').val(client_id);
                $("#modalChangeForm").find('.process_id').val(process_id);

                $.each(data, function(key, value) {
                    $("#modalChangeForm").find('#move_to_form_new').append($("<optgroup></optgroup>").attr("label",key).attr("id",key.replace(/\ /g,'').toLowerCase()));
                    $.each(value, function(k, v) {
                        if(v.existing === '1'){

                        } else {
                            $("#modalChangeForm").find('#' + key.replace(/\ /g, '').toLowerCase()).append($("<option></option>").attr("value", v.id).text(v.name));
                        }
                    });
                });

                $("body").find('#move_to_form_new').trigger('chosen:updated');

            }
        });
    }

    function showOpenApplications(client_id){
        let clientid = client_id;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: '/clients/' + client_id + '/current_applications',
            type: "GET",
            dataType: "json",
            success: function (data) {
                $("#modalCurrentProcesses").modal('show');

                let row = '';
                $.each(data, function(key, value) {
                    row = row + '<li><a href="clients/'+ client_id + '/progress/'+value.process_id+'/'+value.step_id+'">' + value.name + '</a></li>';
                });

                $("#modalCurrentProcesses").find('#current_processes').html(row);
            }
        });
    }

    function showClosedApplications(client_id){
        let clientid = client_id;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: '/clients/' + client_id + '/closed_applications',
            type: "GET",
            dataType: "json",
            success: function (data) {
                $("#modalClosedProcesses").modal('show');

                let row = '';
                $.each(data, function(key, value) {
                    row = row + '<li><a href="clients/'+ client_id + '/progress/'+value.process_id+'/'+value.step_id+'">' + value.name + '</a></li>';
                });

                $("#modalClosedProcesses").find('#closed_processes').html(row);
            }
        });
    }

    function clientBasketGlobalSelectAll(){

        if($(".client-basket-select-all-all").prop('checked')){

            $(document).find(".client-basket .card").each(function () {
                let name = $(this).attr("data-name");

                $("#" + name).prop('checked', true);
                $(".select-this-" + name).prop('checked',true);
            });
        } else {
            $(".client-basket-select-all-all").prop('checked', false);
            $(".client-basket .card").each(function () {
                let name = $(this).attr("data-name");

                    $("#" + name).prop('checked', false);
                    $(".select-this-" + name).prop('checked',false);
            });
        }

    }

    function clientBasketSelectAll(section){
        if($(document).find("#" + section).prop('checked')){

            $("#" + section).prop('checked', true);
            $("#" + section).each(function(){

                    $(".select-this-" + section).prop('checked',true);
            });
        } else {
            $("#" + section).prop('checked', false);
            $("#" + section).each(function(){

                $(".select-this-" + section).prop('checked',false);
            });
        }
        //e.stopPropagation();
        $(".client-basket .card").each(function(){
            const name = $(this).attr("data-name");

            let total = $(document).find('#' + name).length;
            let total_selected = $(document).find('#' + name + ':checked').length;

            if(total === total_selected){
                $(".client-basket-select-all-all").prop('checked',true);
            } else {
                $(".client-basket-select-all-all").prop('checked',false);
            }
        });
    };

    function clientBasketSelect(section){
        let total = $(document).find('.select-this-' + section).length;
        let total_selected = $(document).find('.select-this-' + section + ':checked').length;

        if(total === total_selected){
            $("#" + section).prop('checked',true);
            $(".client-basket-select-all-all").prop('checked', false);
            let total = $(document).find('#' + section).length;
            let total_selected = $(document).find('#' + section + ':checked').length;

            if(total === total_selected){
                $(".client-basket .card").each(function(){
                    const name = $(this).attr("data-name");

                    let total = $(document).find('#' + name).length;
                    let total_selected = $(document).find('#' + name + ':checked').length;

                    if(total === total_selected){
                        $(".client-basket-select-all-all").prop('checked',true);
                    } else {
                        $(".client-basket-select-all-all").prop('checked',false);
                    }
                });
            } else {
                $(".client-basket-select-all-all").prop('checked', false);
            }
        } else {
            $("#" + section).prop('checked',false);
            $(".client-basket-select-all-all").prop('checked', false);
        }
    };

    $(document).ready(function () {

        $('[data-toggle="collapse"]').on('click',function(e){
            if ( $(this).parents('.accordion').find('.collapse.show') ){
                var idx = $(this).index('[data-toggle="collapse"]');
                if (idx == $('.collapse.show').index('.collapse')) {
                    // prevent collapse
                    //e.stopPropagation();
                }
            }
        });

        $('.search').on('search',function(){
            $('#overlay').fadeIn();
            $(this).closest('form').submit();
        });

        //open move to process modal

        $('#modalChangeProcess').on('hidden.bs.modal', function () {
            $('#modalChangeProcess').find('#changeprocessradio_msg').html('');
            $('#modalChangeProcess').find('#move_to_process_new_msg').html('');
            $('#modalChangeProcess').find('.client_id').val('');
            $('#modalChangeProcess').find('.process_id').val('');
            $('#modalChangeProcess').find('#move_to_process_new').removeClass('is-invalid');
            $('#modalChangeProcess').find('#move_to_process_new').empty();
        });

        //move to process depending on radio selection
        $('#changeprocesssave').on('click', function () {
            let err = 0;

            if ($('#modalChangeProcess').find('#move_to_process_new').val() === '0') {
                err++;
                $('#modalChangeProcess').find('#move_to_process_new').addClass('is-invalid');
                $('#modalChangeProcess').find('#move_to_process_new_msg').html('<span style="color: red;">Please select a application.</span>');
            }


            let process_action = 'keep';
            //get value of radio button in modal
            if (err === 0) {
                $('#overlay').show();
                $('#modalChangeProcess').find('#changeprocessradio_msg').html('');
                $('#modalChangeProcess').find('#move_to_process_new_msg').html('');

                if (process_action === 'keep') {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    let client_id = $('#modalChangeProcess').find('.client_id').val();
                    let process_id = $('#modalChangeProcess').find('.process_id').val();
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
        })

        $('#changeformsave').on('click', function () {
            let err = 0;

            if ($('#modalChangeForm').find('#move_to_form_new').val() === '0') {
                err++;
                $('#modalChangeForm').find('#move_to_form_new').addClass('is-invalid');
                $('#modalChangeForm').find('#move_to_form_new_msg').html('<span style="color: red;">Please select a form.</span>');
            }


            let process_action = 'keep';
            //get value of radio button in modal
            if (err === 0) {
                $('#overlay').show();
                $('#modalChangeForm').find('#changeprocessradio_msg').html('');
                $('#modalChangeForm').find('#move_to_form_new_msg').html('');

                if (process_action === 'keep') {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    let client_id = $('#modalChangeForm').find('.client_id').val();
                    let process_id = $('#modalChangeForm').find('.process_id').val();
                    let new_process_id = $('#modalChangeForm').find('#move_to_form_new').val();

                    $.ajax({
                        url: '/clients/' + client_id + '/keep_process/' + process_id + '/' + new_process_id,
                        type: "GET",
                        dataType: "json",
                        success: function (data) {
                            $("#modalChangeForm").modal('hide');

                            window.location.href = '/clients/' + client_id + '/progress/' + new_process_id + '/' + data.new_step_id + '/1';

                        }
                    });
                }
            }
        })

        $('.submitModal').on('click',function () {
            //$(this).closest(".modal-footer").html('');
             $(this).closest(".modal-footer").prev(".modal-body").find(".clientdetailsform").submit();
        })
    })


    /*Client Details*/
    function toggelClientBasket(){
            $('.client-basket').stop(true, true).toggle('slide', {
                direction: 'left',
            }, 750);

        $(".client-basket .card").each(function(){
            const name = $(this).attr("data-name");

            let total = $(document).find('.select-this-' + name).length;
            let total_selected = $(document).find('.select-this-' + name + ':checked').length;

            if(total === total_selected){
                $("#" + name).prop('checked',true);
                $(".client-basket .card").each(function(){
                    const name = $(this).attr("data-name");

                    let total = $(document).find('.select-this-' + name).length;
                    let total_selected = $(document).find('.select-this-' + name + ':checked').length;

                    if(total === total_selected){
                        $(".client-basket-select-all-all").prop('checked',true);
                    } else {
                        $(".client-basket-select-all-all").prop('checked',false);
                    }
                });
            } else {
                $("#" + name).prop('checked',false);
                $(".client-basket-select-all-all").prop('checked',false);
            }
        });
    }

    function composeMessage(client_id,process_id,step_id){

        let processid;
        let stepid;

        if($('#process_id').length){
            processid = $('#process_id').val();
        } else {
            processid = process_id;
        }

        if($('#step_id').length){
            stepid = $('#step_id').val();
        } else {
            stepid = step_id;
        }

        $.ajax({
            url: '/messages/create/' + client_id + '/' + processid + '/' + stepid,
            type: "GET",
            dataType: "json",
            success: function (data) {
                $('#modalSendMessage').modal('show');
                $('#modalSendMessage').find('.modal-title').html("Send Message");
                if(data.subject === 0){
                    $('#modalSendMessage').find('#message_subject').hide();
                } else {
                    $('#modalSendMessage').find('#message_subject').show();
                }

                tinymce.init(editor_config);
            }
        });
    }

    function composeMail(client_id){
        $('#modalSendMail').modal('show');
        tinymce.init(editor_config);
    }

    function sendMail(){
        
        $('#send_mail_form').submit();
        $('#modalSendMail').modal('hide');
    }

    function composeBillboardMessage(){
        $("#modalBillboardMessage").find(".billboard_client").val('');
        $("#modalBillboardMessage").find(".billboard_client").val('').trigger('chosen:updated');
        $("#modalBillboardMessage").find(".billboard_message").val('');
        $("#modalBillboardMessage").modal('show');
    }

    function showTaskOther() {
        if(($("#modalUserTask").data('bs.modal') || {})._isShown ){
            if($("#modalUserTask").find('.task_type').find("option:selected").text() === "Other"){
                $("#modalUserTask").find('.task_other_div').css('display','block');
                $("#modalUserTask").find('.task_other').focus();
            } else {
                $("#modalUserTask").find('.task_other_div').css('display','none ');
            }
        }
        if(($("#modalEditUserTask").data('bs.modal') || {})._isShown ){
            if($("#modalEditUserTask").find('.task_type').find("option:selected").text() === "Other"){
                $("#modalEditUserTask").find('.task_other_div').css('display','block');
                $("#modalEditUserTask").find('.task_other').focus();
            } else {
                $("#modalEditUserTask").find('.task_other_div').css('display','none ');
            }
        }
    }

    function composeUserTask(){
        $("#modalUserTask").find(".task_client").val('');
        $("#modalUserTask").find(".task_client").val('').trigger('chosen:updated');
        $("#modalUserTask").find(".task_message").val('');
        $("#modalUserTask").find(".task_other_div").css('display','none');
        $("#modalUserTask").modal('show');
    }

    function saveBillboardMessage(){
        let client = $("#modalBillboardMessage").find('.billboard_client').val();
        let heading = $("#modalBillboardMessage").find('.billboard_heading').val();
        let message = $("#modalBillboardMessage").find('.billboard_message').val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: '/billboard-message/save',
            type:"POST",
            data:{billboard_message:message,billboard_heading:heading,billboard_client:client},
            success:function(data){
                var rowCount = $('.billboard-table tr').length;

                $("#modalBillboardMessage").find(".billboard_client").val();
                $("#modalBillboardMessage").find(".billboard_heading").val('');
                $("#modalBillboardMessage").find(".billboard_message").val('');
                $("#modalBillboardMessage").modal('hide');

                toastr.success('<strong>Success!</strong> Message was successfully saved.');

                toastr.options.timeOut = 1000;

                if(rowCount > 1) {
                    if(data.billboard_heading === '') {
                        $('<tr class="message-' + data.message_id + '">' +
                            '<td class="billboard" colspan="100%">' +
                            '<span class="pull-right clickable close-icon" onclick="completeBillboardMessage(' + data.message_id + ')" data-effect="fadeOut"><i class="fas fa-trash" style="color:#f06072"></i></span>' +
                            '<div class="card-block">' +
                            '<blockquote class="card-blockquote">' +
                            '<div class="blockquote-body" onclick="showBillboardMessage(' + data.message_id + ')"><strong>' + data.billboard_heading + '</strong><br />' + data.billboard_message + '</div>' +
                            '</blockquote>' +
                            '</div>' +
                            '</td>' +
                            '</tr>').prependTo(".billboard-table > tbody");
                    } else {
                        $('<tr class="message-' + data.message_id + '">' +
                            '<td class="billboard" colspan="100%">' +
                            '<span class="pull-right clickable close-icon" onclick="completeBillboardMessage(' + data.message_id + ')" data-effect="fadeOut"><i class="fas fa-trash" style="color:#f06072"></i></span>' +
                            '<div class="card-block">' +
                            '<blockquote class="card-blockquote">' +
                            '<div class="blockquote-body" onclick="showBillboardMessage(' + data.message_id + ')"><strong>' + data.billboard_heading + '</strong><br />' + data.billboard_message + '</div>' +
                            '</blockquote>' +
                            '</div>' +
                            '</td>' +
                            '</tr>').prependTo(".billboard-table > tbody");
                    }
                }

                if($(".billboard-table .text-center").is(':visible')){
                    $(".billboard-table > tbody").html('');
                    if(data.billboard_heading === '') {
                        $('<tr class="message-' + data.message_id + '">' +
                            '<td class="billboard" colspan="100%">' +
                            '<span class="pull-right clickable close-icon" onclick="completeBillboardMessage(' + data.message_id + ')" data-effect="fadeOut"><i class="fas fa-trash" style="color:#f06072"></i></span>' +
                            '<div class="card-block">' +
                            '<blockquote class="card-blockquote">' +
                            '<div class="blockquote-body" onclick="showBillboardMessage(' + data.message_id + ')"><strong>' + data.billboard_heading + '</strong><br />' + data.billboard_message + '</div>' +
                            '</blockquote>' +
                            '</div>' +
                            '</td>' +
                            '</tr>').prependTo(".billboard-table > tbody");
                    } else {
                        $('<tr class="message-' + data.message_id + '">' +
                            '<td class="billboard" colspan="100%">' +
                            '<span class="pull-right clickable close-icon" onclick="completeBillboardMessage(' + data.message_id + ')" data-effect="fadeOut"><i class="fas fa-trash" style="color:#f06072"></i></span>' +
                            '<div class="card-block">' +
                            '<blockquote class="card-blockquote">' +
                            '<div class="blockquote-body" onclick="showBillboardMessage(' + data.message_id + ')"><strong>' + data.billboard_heading + '</strong><br />' + data.billboard_message + '</div>' +
                            '</blockquote>' +
                            '</div>' +
                            '</td>' +
                            '</tr>').prependTo(".billboard-table > tbody");
                    }
                }
            }
        });
    }

    function saveUserTask(){
        let client = $("#modalUserTask").find('.task_client').val();
        let message = $("#modalUserTask").find('.task_message').val();
        let date_start = $("#modalUserTask").find('.task_date_start').val();
        let date_end = $("#modalUserTask").find('.task_date_end').val();
        let type = $("#modalUserTask").find('.task_type').val();
        let other = $("#modalUserTask").find('.task_other').val();
        let attendees = $("#modalUserTask").find('.task_attendees').val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: '/user-task/save',
            type:"POST",
            data:{task_message:message,task_client:client,task_type:type,task_date_start:date_start,task_date_end:date_end,task_other:other,attendees:attendees},
            success:function(data){
                var rowCount = $('.task-table tr').length;

                $("#modalUserTask").find(".task_client").val('').trigger('chosen:updated');
                $("#modalUserTask").find(".task_type").val('').trigger('chosen:updated');
                $("#modalUserTask").find(".task_date_start").val('');
                $("#modalUserTask").find(".task_date_end").val('');
                $("#modalUserTask").find(".task_attendees").val('');
                $("#modalUserTask").find(".task_other").val('');
                $("#modalUserTask").find(".task_message").val('');
                $("#modalUserTask").modal('hide');

                toastr.success('<strong>Success!</strong> Task was successfully saved.');

                toastr.options.timeOut = 1000;

                if(rowCount > 1) {
                    if(data.client === '') {
                        if(data.task_type === 'Other') {
                            $('<tr class="task-' + data.task_id + '">' +
                                '<td class="usertask" colspan="100%">' +
                                '<span class="pull-right clickable close-icon" onclick="completeUserTask(' + data.task_id + ')" data-effect="fadeOut"><input type="checkbox" /></span>' +
                                '<div class="card-block">' +
                                '<blockquote class="card-blockquote">' +
                                '<div class="blockquote-body" onclick="showUserTask(' + data.task_id + ')"><strong>' + data.task_date_start + ' - ' + data.task_date_end + '</strong> : ' + data.task_other + '</div>' +
                                '</blockquote>' +
                                '</div>' +
                                '</td>' +
                                '</tr>').prependTo(".task-table > tbody");
                        } else {
                            $('.task-' + data.task_id).html('<td class="usertask" colspan="100%">' +
                                '<span class="pull-right clickable close-icon" onclick="completeUserTask(' + data.task_id + ')" data-effect="fadeOut"><input type="checkbox" /></span>' +
                                '<div class="card-block">' +
                                '<blockquote class="card-blockquote">' +
                                '<div class="blockquote-body" onclick="showUserTask(' + data.task_id + ')"><strong>' + data.task_date_start + ' - ' + data.task_date_end + '</strong> : ' + data.task_type + '<br /><small class="text-muted">' + data.client + '</small></div>' +
                                '</blockquote>' +
                                '</div>' +
                                '</td>');
                        }
                    } else {
                        if(data.task_type === 'Other') {
                            $('<tr class="task-' + data.task_id + '">' +
                                '<td class="usertask" colspan="100%">' +
                                '<span class="pull-right clickable close-icon" onclick="completeUserTask(' + data.task_id + ')" data-effect="fadeOut"><input type="checkbox" /></span>' +
                                '<div class="card-block">' +
                                '<blockquote class="card-blockquote">' +
                                '<div class="blockquote-body" onclick="showUserTask(' + data.task_id + ')"><strong>' + data.task_date_start + ' - ' + data.task_date_end + '</strong> : ' + data.task_other + '</div>' +
                                '</blockquote>' +
                                '</div>' +
                                '</td>' +
                                '</tr>').prependTo(".task-table > tbody");
                        } else {
                            $('<tr class="task-' + data.task_id + '">' +
                                '<td class="usertask" colspan="100%">' +
                                '<span class="pull-right clickable close-icon" onclick="completeUserTask(' + data.task_id + ')" data-effect="fadeOut"><input type="checkbox" /></span>' +
                                '<div class="card-block">' +
                                '<blockquote class="card-blockquote">' +
                                '<div class="blockquote-body" onclick="showUserTask(' + data.task_id + ')"><strong>' + data.task_date_start + ' - ' + data.task_date_end + '</strong> : ' + data.task_type + '</div>' +
                                '</blockquote>' +
                                '</div>' +
                                '</td>' +
                                '</tr>').prependTo(".task-table > tbody");
                        }
                    }
                }

                if($(".task-table .text-center").is(':visible')){
                    $(".task-table > tbody").html('');
                    if(data.client === '') {
                        if(data.task_type === 'Other') {
                            $('<tr class="task-' + data.task_id + '">' +
                                '<td class="usertask" colspan="100%">' +
                                '<span class="pull-right clickable close-icon" onclick="completeUserTask(' + data.task_id + ')" data-effect="fadeOut"><input type="checkbox" /></span>' +
                                '<div class="card-block">' +
                                '<blockquote class="card-blockquote">' +
                                '<div class="blockquote-body" onclick="showUserTask(' + data.task_id + ')"><strong>' + data.task_date + '</strong> - ' + data.task_other + '</div>' +
                                '</blockquote>' +
                                '</div>' +
                                '</td>' +
                                '</tr>').prependTo(".task-table > tbody");
                        } else {
                            $('<tr class="task-' + data.task_id + '">' +
                                '<td class="usertask" colspan="100%">' +
                                '<span class="pull-right clickable close-icon" onclick="completeUserTask(' + data.task_id + ')" data-effect="fadeOut"><input type="checkbox" /></span>' +
                                '<div class="card-block">' +
                                '<blockquote class="card-blockquote">' +
                                '<div class="blockquote-body" onclick="showUserTask(' + data.task_id + ')"><strong>' + data.task_date + '</strong> - ' + data.task_type + '</div>' +
                                '</blockquote>' +
                                '</div>' +
                                '</td>' +
                                '</tr>').prependTo(".task-table > tbody");
                        }
                    } else {
                        if(data.task_type === 'Other') {
                            $('<tr class="task-' + data.task_id + '">' +
                                '<td class="usertask" colspan="100%">' +
                                '<span class="pull-right clickable close-icon" onclick="completeUserTask(' + data.task_id + ')" data-effect="fadeOut"><input type="checkbox" /></span>' +
                                '<div class="card-block">' +
                                '<blockquote class="card-blockquote">' +
                                '<div class="blockquote-body" onclick="showUserTask(' + data.task_id + ')"><strong>' + data.task_date + '</strong> - ' + data.task_other + '</div>' +
                                '</blockquote>' +
                                '</div>' +
                                '</td>' +
                                '</tr>').prependTo(".task-table > tbody");
                        } else {
                            $('.task-' + data.task_id).html('<td class="usertask" colspan="100%">' +
                                '<span class="pull-right clickable close-icon" onclick="completeUserTask(' + data.task_id + ')" data-effect="fadeOut"><input type="checkbox" /></span>' +
                                '<div class="card-block">' +
                                '<blockquote class="card-blockquote">' +
                                '<div class="blockquote-body" onclick="showUserTask(' + data.task_id + ')"><strong>' + data.task_date + '</strong> - ' + data.task_type + '<br /><small class="text-muted">' + data.client + '</small></div>' +
                                '</blockquote>' +
                                '</div>' +
                                '</td>');
                        }
                    }
                }
            }
        });
    }

    function showBillboardMessage(msgid) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: '/billboard-message/'+msgid+'/show',
            type: "GET",
            success: function (data) {
                $('#modalShowBillboardMessage').modal('show');
                $('#modalShowBillboardMessage').find('.message_id').val(data.message_id);
                if(data.client === ''){
                    $('#modalShowBillboardMessage').find('.billboard_client').html('<i>No client found.</i>');
                } else {
                    $('#modalShowBillboardMessage').find('.billboard_client').html(data.client);
                }
                if(data.billboard_heading === ''){
                    $('#modalShowBillboardMessage').find('.billboard_heading').html('<i>No heading found.</i>');
                } else {
                    $('#modalShowBillboardMessage').find('.billboard_heading').html(data.billboard_heading);
                }
                if(data.billboard_message === ''){
                    $('#modalShowBillboardMessage').find('.billboard_message').html('<i>No message found.</i>');
                } else {
                    $('#modalShowBillboardMessage').find('.billboard_message').html(data.billboard_message);
                }
            }
        })
    }

    function showUserTask(taskid) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: '/user-task/'+taskid+'/show',
            type: "GET",
            success: function (data) {
                $('#modalShowUserTask').modal('show');
                $('#modalShowUserTask').find('.task_id').val(data.task_id);
                if(data.client === ''){
                    $('#modalShowUserTask').find('.task_client').html('<i>No client found.</i>');
                } else {
                    $('#modalShowUserTask').find('.task_client').html(data.client);
                }
                if(data.task_type === ''){
                    $('#modalShowUserTask').find('.task_type').html('<i>No task type found.</i>');
                } else {
                    $('#modalShowUserTask').find('.task_type').html(data.task_type);
                }
                if(data.task_type === 'Other'){
                    $('#modalShowUserTask').find('.task_other_div').css('display','block');
                    $('#modalShowUserTask').find('.task_other_div').html(data.task_other);
                } else {
                    $('#modalShowUserTask').find('.task_other_div').css('display','none');
                }
                if(data.task_date_start === ''){
                    $('#modalShowUserTask').find('.task_date_start').html('<i>No task date found.</i>');
                } else {
                    $('#modalShowUserTask').find('.task_date_start').html(data.task_date_start);
                }
                if(data.task_date_end === ''){
                    $('#modalShowUserTask').find('.task_date_end').html('<i>No task date found.</i>');
                } else {
                    $('#modalShowUserTask').find('.task_date_end').html(data.task_date_end);
                }
                if(data.task_message === ''){
                    $('#modalShowUserTask').find('.task_message').html('<i>No message found.</i>');
                } else {
                    $('#modalShowUserTask').find('.task_message').html(data.task_message);
                }

                if(data.task_attendees === '' || data.task_attendees === null){
                    $('#modalShowUserTask').find('.task_attendees').html('<i>No attendees found.</i>');
                } else {
                    let attendees = data.task_attendees;
                    if(data.task_attendees.length > 0) {
                        attendees = data.task_attendees.replace(/\;/g, ' - ');
                    }
                    $('#modalShowUserTask').find('.task_attendees').html(attendees);

                }
            }
        })
    }

    function completeBillboardMessage(msgid) {
        if (!confirm("Are you sure you want to delete this record?")){
            return false;
        } else {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '/billboard-message/' + msgid + '/complete',
                type:"GET",
                success:function(data){
                    $('.message-'+msgid).remove();
                    toastr.success('<strong>Success!</strong> Record was successfully deleted.');

                    toastr.options.timeOut = 1000;

                    var rowCount = $('.billboard-table tr').length;

                    if(rowCount > 1){

                    } else {
                        $('.billboard-table tbody').html('<tr>' +
                            '<td colspan="100%" class="text-center"><small class="alert alert-info w-100 d-block text-muted">There are no messages to display.</small></td>' +
                            '</tr>');
                    }

                }
            });
        }
    }

    function completeUserTask(taskid) {
        if (!confirm("Are you sure you want to mark this record as complete?")){
            return false;
        } else {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '/user-task/' + taskid + '/complete',
                type:"GET",
                success:function(data){
                    $('.task-'+taskid).remove();
                    toastr.success('<strong>Success!</strong> Record was successfully marked as complete.');

                    toastr.options.timeOut = 1000;

                    var rowCount = $('.task-table tr').length;

                    if(rowCount > 1){

                    } else {
                        $('.task-table tbody').html('<tr>' +
                            '<td colspan="100%" class="text-center"><small class="alert alert-info w-100 d-block text-muted">There are no tasks to display.</small></td>' +
                            '</tr>');
                    }

                }
            });
        }
    }

    function editBillboardMessage() {
        let msgid = $('#modalShowBillboardMessage').find('.message_id').val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: '/billboard-message/' + msgid + '/show',
            type:"GET",
            success:function(data){
                $('#modalShowBillboardMessage').modal('hide');
                $('#modalEditBillboardMessage').modal('show');

                $('#modalEditBillboardMessage').find('.message_id').val(data.message_id);
                if(data.client === ''){
                    $('#modalEditBillboardMessage').find('.billboard_client').val('');
                } else {
                    $('#modalEditBillboardMessage').find('.billboard_client').val(data.client_id);
                    $('#modalEditBillboardMessage').find('.billboard_client').val(data.client_id).trigger('chosen:updated');
                }
                if(data.billboard_heading === ''){
                    $('#modalEditBillboardMessage').find('.billboard_heading').val('');
                } else {
                    $('#modalEditBillboardMessage').find('.billboard_heading').val(data.billboard_heading);
                }
                if(data.billboard_message === ''){
                    $('#modalEditBillboardMessage').find('.billboard_message').val('');
                } else {
                    $('#modalEditBillboardMessage').find('.billboard_message').val(data.billboard_message);
                }
            }
        });

    }

    function editUserTask() {
        let taskid = $('#modalShowUserTask').find('.task_id').val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: '/user-task/' + taskid + '/show',
            type:"GET",
            success:function(data){
                $('#modalShowUserTask').modal('hide');
                $('#modalEditUserTask').modal('show');

                $('#modalEditUserTask').find('.task_id').val(data.task_id);
                if(data.client === ''){
                    $('#modalEditUserTask').find('.task_client').val('');
                } else {
                    $('#modalEditUserTask').find('.task_client').val(data.client_id);
                    $('#modalEditUserTask').find('.task_client').val(data.client_id).trigger('chosen:updated');
                }
                if(data.task_type === ''){
                    $('#modalEditUserTask').find('.task_type').val('');
                } else {
                    $('#modalEditUserTask').find('.task_type').val(data.task_type);
                }
                if(data.task_type === 'Other'){
                    $('#modalEditUserTask').find('.task_other_div').css('display','block');
                    $('#modalEditUserTask').find('.task_other').val(data.task_other);
                } else {
                    $('#modalEditUserTask').find('.task_other_div').css('display','none');
                }
                if(data.task_date_start === ''){
                    $('#modalEditUserTask').find('.task_date_start').val('');
                } else {
                    let start = data.task_date_start.replace(/\ /g,'T');
                    $('#modalEditUserTask').find('.task_date_start').val(start);
                }
                if(data.task_date_end === ''){
                    $('#modalEditUserTask').find('.task_date_end').val('');
                } else {
                    let end = data.task_date_end.replace(/\ /g,'T');
                    $('#modalEditUserTask').find('.task_date_end').val(end);
                }
                if(data.task_message === ''){
                    $('#modalEditUserTask').find('.task_message').val('');
                } else {
                    $('#modalEditUserTask').find('.task_message').val(data.task_message);
                }
            }
        });

    }

    function updateBillboardMessage(){
        let msgid = $("#modalEditBillboardMessage").find('.message_id').val();
        let client = $("#modalEditBillboardMessage").find('.billboard_client').val();
        let heading = $("#modalEditBillboardMessage").find('.billboard_heading').val();
        let message = $("#modalEditBillboardMessage").find('.billboard_message').val();


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: '/billboard-message/' + msgid + '/update',
            type:"POST",
            data:{billboard_heading:heading,billboard_message:message,billboard_client:client},
            success:function(data){
                var rowCount = $('.billboard-table tr').length;

                $("#modalEditBillboardMessage").find(".billboard_client").val('');
                $("#modalEditBillboardMessage").find(".billboard_client").val('').trigger('chosen:updated');
                $("#modalEditBillboardMessage").find(".billboard_heading").val('');
                $("#modalEditBillboardMessage").find(".billboard_message").val('');
                $("#modalEditBillboardMessage").modal('hide');

                toastr.success('<strong>Success!</strong> Message was successfully saved.');

                toastr.options.timeOut = 1000;

                if(rowCount > 1) {
                    if(data.billboard_heading === '') {
                        $('.message-'+msgid).html('<td class="billboard" colspan="100%">' +
                            '<span class="pull-right clickable close-icon" onclick="completeBillboardMessage(' + data.message_id + ')" data-effect="fadeOut"><i class="fas fa-trash" style="color:#f06072"></i></span>' +
                            '<div class="card-block">' +
                            '<blockquote class="card-blockquote">' +
                            '<div class="blockquote-body" onclick="showBillboardMessage(' + data.message_id + ')">' + data.billboard_message + '</div>' +
                            '</blockquote>' +
                            '</div>' +
                            '</td>');
                    } else {
                        $('.message-'+msgid).html('<td class="billboard" colspan="100%">' +
                            '<span class="pull-right clickable close-icon" onclick="completeBillboardMessage(' + data.message_id + ')" data-effect="fadeOut"><i class="fas fa-trash" style="color:#f06072"></i></span>' +
                            '<div class="card-block">' +
                            '<blockquote class="card-blockquote">' +
                            '<div class="blockquote-body" onclick="showBillboardMessage(' + data.message_id + ')"><strong>' + data.billboard_heading + '</strong><br />' + data.billboard_message + '</div>' +
                            '</blockquote>' +
                            '</div>' +
                            '</td>');
                    }
                }
            }
        });
    }

    function updateUserTask(){
        let taskid = $("#modalEditUserTask").find('.task_id').val();
        let client = $("#modalEditUserTask").find('.task_client').val();
        let message = $("#modalEditUserTask").find('.task_message').val();
        let date_start = $("#modalEditUserTask").find('.task_date_start').val();
        let date_end = $("#modalEditUserTask").find('.task_date_end').val();
        let type = $("#modalEditUserTask").find('.task_type').val();
        let other = $("#modalEditUserTask").find('.task_other').val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: '/user-task/' + taskid + '/update',
            type:"POST",
            data:{task_message:message,task_client:client,task_type:type,task_date_start:date_start,task_date_end:date_end,task_other:other},
            success:function(data){
                var rowCount = $('.task-table tr').length;

                $("#modalEditUserTask").find(".task_client").val('');
                $("#modalEditUserTask").find(".task_client").val('').trigger('chosen:updated');
                $("#modalEditUserTask").find(".task_message").val('');
                $("#modalEditUserTask").modal('hide');

                toastr.success('<strong>Success!</strong> Task was successfully saved.');

                toastr.options.timeOut = 1000;

                if(rowCount > 1) {
                    if(data.client === '') {
                        if(data.task_type === 'Other') {
                            $('.task-' + taskid).html('<td class="usertask" colspan="100%">' +
                                '<span class="pull-right clickable close-icon" onclick="completeUserTask(' + data.task_id + ')" data-effect="fadeOut"><input type="checkbox" /></span>' +
                                '<div class="card-block">' +
                                '<blockquote class="card-blockquote">' +
                                '<div class="blockquote-body" onclick="showUserTask(' + data.task_id + ')"><strong>' + data.task_date_start + '</strong> - ' + data.task_other + '</div>' +
                                '</blockquote>' +
                                '</div>' +
                                '</td>');
                        } else {
                            $('.task-' + taskid).html('<td class="usertask" colspan="100%">' +
                                '<span class="pull-right clickable close-icon" onclick="completeUserTask(' + data.task_id + ')" data-effect="fadeOut"><input type="checkbox" /></span>' +
                                '<div class="card-block">' +
                                '<blockquote class="card-blockquote">' +
                                '<div class="blockquote-body" onclick="showUserTask(' + data.task_id + ')"><strong>' + data.task_date_start + '</strong> - ' + data.task_type + '</div>' +
                                '</blockquote>' +
                                '</div>' +
                                '</td>');
                        }
                    } else {
                        if(data.task_type === 'Other') {
                            $('.task-' + taskid).html('<td class="usertask" colspan="100%">' +
                                '<span class="pull-right clickable close-icon" onclick="completeUserTask(' + data.task_id + ')" data-effect="fadeOut"><input type="checkbox" /></span>' +
                                '<div class="card-block">' +
                                '<blockquote class="card-blockquote">' +
                                '<div class="blockquote-body" onclick="showUserTask(' + data.task_id + ')"><strong>' + data.task_date_start + '</strong> - ' + data.task_other + '<br /><small class="text-muted">' + data.client + '</small></div>' +
                                '</blockquote>' +
                                '</div>' +
                                '</td>');
                        } else {
                            $('.task-' + taskid).html('<td class="usertask" colspan="100%">' +
                                '<span class="pull-right clickable close-icon" onclick="completeUserTask(' + data.task_id + ')" data-effect="fadeOut"><input type="checkbox" /></span>' +
                                '<div class="card-block">' +
                                '<blockquote class="card-blockquote">' +
                                '<div class="blockquote-body" onclick="showUserTask(' + data.task_id + ')"><strong>' + data.task_date_start + '</strong> - ' + data.task_type + '<br /><small class="text-muted">' + data.client + '</small></div>' +
                                '</blockquote>' +
                                '</div>' +
                                '</td>');
                        }
                    }
                }
            }
        });
    }

    function deleteBillboardMessage() {
        let msgid = $('#modalShowBillboardMessage').find('.message_id').val();

        if (!confirm("Are you sure you want to delete this record?")){
            return false;
        } else {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '/billboard-message/' + msgid + '/delete',
                type:"GET",
                success:function(data){

                    $('#modalShowBillboardMessage').modal('hide');
                    $('.message-'+msgid).remove();
                    toastr.success('<strong>Success!</strong> Record was successfully deleted.');

                    toastr.options.timeOut = 1000;

                    var rowCount = $('.billboard-table tr').length;

                    if(rowCount > 1){

                    } else {
                        $('.billboard-table tbody').html('<tr>' +
                            '<td colspan="100%" class="text-center"><small class="alert alert-info w-100 d-block text-muted">There are no messages to display.</small></td>' +
                            '</tr>');
                    }

                }
            });
        }
    }

    function deleteUserTask() {
        let taskid = $('#modalShowUserTask').find('.task_id').val();

        if (!confirm("Are you sure you want to delete this record?")){
            return false;
        } else {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '/user-task/' + taskid + '/delete',
                type:"GET",
                success:function(data){

                    $('#modalShowUserTask').modal('hide');
                    $('.task-'+taskid).remove();
                    toastr.success('<strong>Success!</strong> Record was successfully deleted.');

                    toastr.options.timeOut = 1000;

                    var rowCount = $('.task-table tr').length;

                    if(rowCount > 1){

                    } else {
                        $('.task-table tbody').html('<tr>' +
                            '<td colspan="100%" class="text-center"><small class="alert alert-info w-100 d-block text-muted">There are no tasks to display.</small></td>' +
                            '</tr>');
                    }

                }
            });
        }
    }

    function composeWhatsapp(client_id){
        $('#modalSendWhatsapp').modal('show');

        $("#modalSendWhatsapp").find(".client_id").val('');
        $("#modalSendWhatsapp").find(".whatsapp_message").val('');
        $("#modalSendWhatsapp").find(".template option[value='']").prop('selected', true);
        $('#modalSendWhatsapp').find('.template').trigger('chosen:updated');

        if($(".send_whatsapp_form").is(":visible")) {

        } else {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '/clients/'+client_id+'/getdetail',
                type:"GET",
                dataType:"json",
                success:function(data){
                        $("#modalSendWhatsapp").find('.client_id').val(data.id);
                    $('body').find('#modalSendWhatsapp').find('.recipient').empty();
                    $('body').find('#modalSendWhatsapp').find('.recipient').append('<option value="' + data.contact + '">' + data.clname + ': ' + data.contact + '</option>');
                    $('body').find('#modalSendWhatsapp').find('.recipient option[value="' + data.contact + '"]').prop('selected',true);
                    $('body').find('#modalSendWhatsapp').find('.recipient').trigger('chosen:updated');
                }
            });
        }
        //tinymce.init(editor_config);
    }

    function sendWhatsapp(){
        if($(".send_whatsapp_form").is(":visible")) {
            $('.send_whatsapp_form:visible').submit();
        } else {
            var client_id = $("#modalSendWhatsapp").find(".client_id").val();
            var recipient = $("#modalSendWhatsapp").find(".recipient").val();
            var whatsapp_message = $("#modalSendWhatsapp").find(".whatsapp_message").val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '/message',
                type:"POST",
                data:{client_id:client_id, recipient:recipient, whatsapp_message:whatsapp_message},
                success:function(data){
                    $("#modalSendWhatsapp").find(".client_id").val('');
                    $("#modalSendWhatsapp").find(".recipient").val();
                    $("#modalSendWhatsapp").find(".whatsapp_message").val('');
                    $("#modalSendWhatsapp option[value='']").prop('selected', true);
                    $("#modalSendWhatsapp").modal('hide');

                    toastr.success('<strong>Success!</strong> Whatsapp was successfully sent.');

                    toastr.options.timeOut = 1000;
                }
            });
        }
        $('#modalSendWhatsapp').modal('hide');
    }

    function getWhatsappTemplate(){
        let template = $("#modalSendWhatsapp").find('.template').val();
        var client_id = $("#modalSendWhatsapp").find(".client_id").val();
        // console.log(client_id)

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: '/whatsapptemplates/' + template + '/gettemplate/' + client_id,
            type:"GET",
            dataType:"json",
            success:function(data){
                $("#modalSendWhatsapp").find(".whatsapp_message").val(data.whatsapp_content);
            }
        });
    }

    function saveClientDetails(){
        if($('#crm_id').val() === '5'){
            if($('#parent_client').val() === ''){
                $('#parent_client').addClass('is-invalid');
            } else {
                let err = 0;
                $( ".kpi" ).each(function( index ) {
                    if($( this ).val() == ''){
                        err++;
                        $( this ).addClass('is-invalid');
                    } else {
                        $( this ).removeClass('is-invalid');
                    }
                    /*console.log( index + ": " + $( this ).val() );*/
                });

                if(err === 0) {
                    $('#parent_client').removeClass('is-invalid');
                    $('.clientdetailsform2:visible').submit();
                    $('#overlay').fadeIn();
                }
            }
        } else {
            let err = 0;
            $( ".kpi" ).each(function( index ) {
                if($( this ).val() == ''){
                    err++;
                    $( this ).addClass('is-invalid');
                } else {
                    $( this ).removeClass('is-invalid');
                }
                /*console.log( index + ": " + $( this ).val() );*/
            });

            if(err ===  0) {
                $('.clientdetailsform2:visible').submit();
                $('#overlay').fadeIn();
            }
        }
    }

    function saveAction() {
        let err = 0;

        let action_name = $('#action_name').val();
        let action_description = $('#action_description').val();
        let action_process = $('#actions_process').val();
        let action_step = $('#action_step').val();
        let action_recipients = $("#action_recipients").val();

        if(action_name.length === 0){
            err++;
            $('#action_name').addClass('is-invalid');
        } else {
            $('#action_name').removeClass('is-invalid');
            $('#save_action_name').val(action_name);
        }
        if(action_description.length === 0){
            err++;
            $('#action_description').addClass('is-invalid');
        } else {
            $('#action_description').removeClass('is-invalid');
            $('#save_action_description').val(action_description);
        }

        if($("#actions_process").val() == 0){
            err++;
            $('#actions_process').addClass('is-invalid');
        } else {
            $('#actions_process').removeClass('is-invalid');
            $('#save_action_process').val(action_process);
        }

        if($("#action_step").val() == 0){
            err++;
            $('#action_step').addClass('is-invalid');
        } else {
            $('#action_step').removeClass('is-invalid');
            $('#save_action_step').val(action_step);
        }

        if(action_recipients.length === 0){
            err++;
            $('#action_recipients_chosen').css('border','1px solid #dc3545');
        } else {
            $('#action_recipients_chosen').css('border','1px solid #ced4da');
            $('#save_action_recipients').val(action_recipients);
        }

        if(err === 0) {
            $('#save_action_form').submit();
            $('#overlay').fadeIn();
        }
    }

    function saveReport() {
        $('#reportform').submit();
        $('#overlay').fadeIn();
    }

    function saveClientDetailsModal(){
        $('.clientdetailsmodalform:visible').submit();
        $('#overlay').fadeIn();
    }

    function sendMessage(){
        $('#send_message_form').submit();
    }

    function saveUser() {
        $('#save_user_form').submit();
    }

    function saveForm() {
        $('#save_form_form').submit();
    }

    function saveWhatsappTemplate() {
        $('#whatsappform').submit();
    }

    function saveTaskType() {
        $('#tasktypeform').submit();
    }

    function saveSettings() {
        $('#save_settings_form').submit();
    }

    function saveRole() {
        $('#save_role_form').submit();
        $('#overlay').fadeIn();
    }

    function saveFormSection() {
        $('#save_form_section_form').submit();
    }

    function saveDocument() {
        $('#save_document_form').submit();
    }

    function saveTemplate() {
        $('#save_template_form').submit();
    }

    function saveProcessGroup() {
        $('#save_process_group_form').submit();
    }

    function saveProcess() {
        $('#save_process_form').submit();
    }

    function saveStep() {
        $('#save_step_form').submit();
    }

    function saveDivision() {
        $('#save_division_form').submit();
    }

    function saveRegion() {
        $('#save_region_form').submit();
    }

    function saveArea() {
        $('#save_area_form').submit();
    }

    function saveOffice() {
        $('#save_office_form').submit();
    }

    function addressKYC(clientID)
    {
        $('#overlay').fadeIn();
        axios.post('/api/address/kyc/individual', {
            client_id: clientID,
        })
            .then(function (response) {
                $('#overlay').fadeOut();

                // alert(response.data.message);
                notifyDialog(response.data.message);
            })
            .catch(function (error) {
                $('#overlay').fadeOut();

            });
    }

    function idvConfirm(clientID)
    {
        $('#overlay').fadeIn();
        axios.post('/api/cpb/idv/confirm', {
            client_id: clientID,
        })
        .then(function (response) {
            $('#overlay').fadeOut();
            notifyDialog(response.data.message);
        })
        .catch(function (error) {
            $('#overlay').fadeOut();

        });
    }

    function getProofOfAddress(clientID)
    {
        $('#overlay').fadeIn();
        axios.post('/api/cpb/getproofofaddress', {
            client_id: clientID,
        })
            .then(function (response) {
                $('#overlay').fadeOut();

                notifyDialog(response.data.message);
            })
            .catch(function (error) {
                $('#overlay').fadeOut();

            });
    }

    function getAVS(clientID)
    {
        $('#overlay').fadeIn();
        axios.post('/api/cpb/getavs', {
            client_id: clientID,
        })
            .then(function (response) {
                $('#overlay').fadeOut();

                notifyDialog(response.data.message);
            })
            .catch(function (error) {
                $('#overlay').fadeOut();

            });
    }

    function editInfo(clientID) {
        $('.noedit').addClass('d-none');
        $('.noedit').removeClass('d-block');
        $('.yesedit').removeClass('d-none');
    }

    function saveInfo(clientID) {
        let trade_name = $('input[name="trade_name"]').val();
        let contact_name = $('input[name="contact_name"]').val();
        let contact_email = $('input[name="contact_email"]').val();
        let contact_officenr = $('input[name="contact_officenr"]').val();
        let contact_cellnr = $('input[name="contact_cellnr"]').val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: '/saveinfo',
            data:{client_id:clientID, trade_name:trade_name, contact_name:contact_name, contact_email:contact_email, contact_cellnr:contact_cellnr, contact_officenr:contact_officenr},
            success: function (data) {
                if (data.result === 'success') {

                    $('input[name="trade_name"]').val(data.trade_name);
                    $('.trade_name').html(data.trade_name);

                    $('input[name="contact_name"]').val(data.contact_name);
                    if(data.contact_name === null){
                        $('.contact_name').html('No contact person captured.');
                    } else {
                        $('.contact_name').html(data.contact_name);
                    }

                    $('input[name="contact_email"]').val(data.contact_email);
                    if(data.contact_email === null){
                        $('.contact_email').html('No email captured.');
                    } else {
                        $('.contact_email').html(data.contact_email);
                    }

                    $('input[name="contact_officenr"]').val(data.contact_officenr);
                    if(data.contact_officenr === null){
                        $('.contact_officenr').html('No office number captured.');
                    } else {
                        $('.contact_officenr').html(data.contact_officenr);
                    }

                    $('input[name="contact_cellnr"]').val(data.contact_cellnr);
                    if(data.contact_cellnr === null){
                        $('.contact_cellnr').html('No cellphone number captured.');
                    } else {
                        $('.contact_cellnr').html(data.contact_cellnr);
                    }

                    toastr.success('<strong>Success!</strong> Client Information successfully saved.');

                    toastr.options.timeOut = 1000;

                    $('.yesedit').addClass('d-none');
                    $('.noedit').addClass('d-block');
                    $('.noedit').removeClass('d-none');
                }
            }
        });
    }

    @if(Session::has('flash_success'))
        toastr.success('<strong>Success!</strong> {{Session::get('flash_success')}}');

        toastr.options.timeOut = 1000;
        {{Session::forget('flash_success')}}
    @endif

    $("#client-basket-add").on('click',function(){

        var client_id = $(this).data('client');
        var checked_values = [];
        var nonchecked_values = [];
        $('#client-basket-form .collapse input[type=checkbox]').each( function() {
            if( $(this).is(':checked') ) {
                checked_values.push( $(this).val() );
            } else {
                nonchecked_values.push( $(this).val() );
            }
        });

        $('#overlay').fadeIn();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: '/forms/include-in-basket',
            data: {'client_id':client_id,'checked_values': checked_values,'nonchecked_values': nonchecked_values},
            success: function(data) {
                $('#overlay').fadeOut();

                toastr.success(data.success);

                toastr.options.timeOut = 1000;

                toggelClientBasket();
            }
        });
    });

    $('.isClientProgressing').on('click',function(){
    // function isClientProgressing(client_id){

        let clientid = $(this).data('client');
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: '/clients/' + clientid + '/isprogressing',
            type: "GET",
            dataType: "json",
            success: function (data) {
                window.location.reload();
            }
        });
    });

    var editor_config = {
        path_absolute : "/",
        branding: false,
        relative_urls: false,
        convert_urls : false,
        menubar : false,
        paste_data_images: true,
        browser_spellcheck: true,
        selector: "textarea.my-editor",
        statusbar: false,
        setup: function (editor) {
            editor.on('change', function () {
                tinymce.triggerSave();
            });
        },
        plugins: [
            "advlist autolink lists link image charmap print preview hr anchor pagebreak",
            "searchreplace visualblocks visualchars code fullscreen",
            "insertdatetime media nonbreaking save table contextmenu directionality",
            "emoticons template paste textcolor colorpicker textpattern"
        ],
        paste_as_text: true,
        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent",

        external_filemanager_path:"{{url('tinymce/filemanager')}}/",
        filemanager_title:"Responsive Filemanager" ,
        external_plugins: { "filemanager" : "{{url('tinymce')}}/filemanager/plugin.min.js"}
    };
</script>


<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/no-data-to-display.js"></script>
<script src="https://rawgit.com/highcharts/rounded-corners/master/rounded-corners.js"></script>
@yield('extra-js')
<script>
    $(function() {

        var numItems = $('.progress-indicator').find('.card').length;
        if (numItems < parseInt(6)) {
            $('.progress-indicator').addClass('justify-content-center');
        }
        if (numItems === parseInt(6)) {
            $('.progress-indicator').addClass('justify-content-center');
        }
        if (numItems > parseInt(6)) {
            let offs = $('#step_' + $('#active_step_id').val()).offset().left;
            $('#scrolling-wrapper').animate({scrollLeft: offs - 300}, 0);
        }
    });
</script>
</body>
</html>
