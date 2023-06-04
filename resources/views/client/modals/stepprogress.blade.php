<div class="modal fade" id="modalRules" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header text-center" style="border-bottom: 0px;padding:.5rem;">
                <h5 class="modal-title">Application Trigger</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mx-3">
                <div class="row">
                    Some of the entered information will trigger the below Processes.
                </div>
                <div class="row rule-div">

                </div>
            </div>
            <div class="modal-footer text-center">
                <a href="javascript:void(0)" class="btn btn-sm btn-secondary" id="stayonprocess">Stay on current process</a>
                <a href="javascript:void(0)" class="btn btn-sm btn-secondary" id="rulemovetoprocess" >Save and move to selected process</a>
            </div>
        </div>
    </div>
</div>

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
                    <button class="btn btn-sm btn-outline-primary" id="addactioncancel">Cancel</button>
                    <button class="btn btn-sm btn-success" id="addactionsave">Send</button>
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
                            <input type="button" id="fileuploadcancel"  value="Cancel" class="btn btn-sm btn-outline-primary">
                            <input type="submit"  value="Upload" class="btn btn-sm btn-success">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalChangeMirrorValue" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="width: auto !important;">
            <div class="modal-header text-center" style="border-bottom: 0px;padding:.5rem;">
                <h5 class="modal-title">Change Mirror Value</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mx-3">
                <div class="row">
                    <input type="hidden" name="mirror-activity" id="mirror-activity">
                    <div class="md-form col-sm-12 text-left">
                        <div class="overlay">
                            <div class="spinner"></div>
                            <br/>
                            Loading...
                        </div>
                        <ul id="mirror_values" style="padding: 0px 1rem;margin:0px;list-style: none;">

                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-success" id="set-mirror-value">Change</button>
                <button class="btn btn-outline-primary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalSignForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header text-center" style="border-bottom: 0px;padding:.5rem;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mx-3">
                <div class="row">
                    <div class="md-form col-sm-12">
                        <label data-error="wrong" data-success="right" for="defaultForm-pass">Kindly confirm by entering your password.</label>
                        <input type="hidden" id="defaultForm-form_id" class="form-control form-control-sm validate form_form_id">
                        <input type="hidden" id="defaultForm-client_id" class="form-control form-control-sm validate form_client_id">
                        <input type="password" id="defaultForm-password" class="form-control form-control-sm validate form_pass">
                    </div>
                    <div class="md-form mb-4 col-sm-12">
                        <div class="sign_error_message invalid-feedback"></div>
                    </div>
                </div>
                <div class="row text-center col-sm-12">
                    <button class="btn btn-sm btn-default" id="signform">Sign</button>&nbsp;
                    <button class="btn btn-sm btn-default" id="cancelsignform">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>