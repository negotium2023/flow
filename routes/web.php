<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::post('/webhooks/inbound', 'WhatsappController@incoming');

Route::get('/', function () {
    return view('login');
})->name('home');
Auth::routes();
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('mlogin', 'Auth\LoginController@login')->name('mlogin');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

Route::group(['prefix' => 'messages'], function () {
    Route::get('/', ['as' => 'messages', 'uses' => 'MessagesController@index']);
    Route::get('create', ['as' => 'messages.create', 'uses' => 'MessagesController@create']);
    Route::get('create/{client_id}/{process_id}/{step_id}', ['as' => 'messages.client', 'uses' => 'MessagesController@client_create']);
    Route::get('create/{client_id}/{related_party_id}', ['as' => 'messages.relatedparty', 'uses' => 'MessagesController@relatedparty_create']);
    Route::post('/', ['as' => 'messages.store', 'uses' => 'MessagesController@store']);
    Route::get('{id}', ['as' => 'messages.show', 'uses' => 'MessagesController@show']);
    Route::put('{id}', ['as' => 'messages.update', 'uses' => 'MessagesController@update']);
});

Route::get('powerpoint', 'ReportController@powerpoint')->name('powerpoint');

// Registration Routes...
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');

Route::get('profile/{user?}', 'UserController@profile')->name('profile');
Route::get('settings', 'UserController@settings')->name('settings');
Route::post('settings/password', 'UserController@handlePassword')->name('settings.password');
Route::post('settings/profile', 'UserController@handleProfile')->name('settings.profile');
Route::post('settings/notifications', 'UserController@handleNotifications')->name('settings.notifications');

Route::any('/', 'HomeController@index')->name('home');
Route::any('/home', 'HomeController@index');
Route::get('recents', 'HomeController@recents')->name('recents');
Route::any('dashboard', 'HomeController@dashboard')->name('dashboard');
Route::any('progress', 'HomeController@progress')->name('progress');
Route::get('calendar', 'HomeController@calendar')->name('calendar');
Route::get('help', 'HelpController@create')->name('help.create');
Route::post('help', 'HelpController@store')->name('help.store');

Route::resource('emaillogs', 'EmailLogsController');
Route::get('emaillogs/{email_id}', 'EmailLogsController@show')->name('emaillogs.show');

/*Route::get('storage/crf', 'AssetController@getCrf')->name('crf_client');*/
Route::get('storage/avatar', 'AssetController@getAvatar')->name('avatar');
Route::get('storage/document', 'AssetController@getDocument')->name('document');
Route::get('storage/form', 'AssetController@getForm')->name('form');
Route::get('storage/template', 'AssetController@getTemplate')->name('template');

Route::get('clients/amlreviewed', 'ClientController@amlReviewed')->name('clients.amlreviewed');
Route::get('clients/amlapproved', 'ClientController@amlApproved')->name('clients.amlapproved');
Route::get('clients/amlreviewedandapproved', 'ClientController@amlReviewedAndApproved')->name('clients.amlreviewedandapproved');
Route::get('clients/completed/{step_id}', 'ClientController@completed')->name('clients.completed');
Route::get('clients/{client_id}/generate_form/{process_id}', 'ClientDocumentsController@generateFormDocument');
Route::get('clients/{client_id}/sign_form/{process_id}', 'ClientDocumentsController@signFormDocument');

Route::get('clients/xlsexport','ClientController@xlsExport')->name('clients.xlsexport');
Route::resource('clients', 'ClientController');

Route::get('clients/{client}/progress','ClientController@progress')->name('clients.progress');
Route::any('clients/create','ClientController@create')->name('clients.create');
Route::any('clients/{client}/members/{process}/{step}','ClientController@members')->name('clients.members');
Route::post('clients/{client}/updates','ClientController@update')->name('clients.savedetail');
Route::get('clients/{client}/avatar','ClientController@generateAvatar')->name('clients.avatar');
Route::get('clients/{client}/processes/{process}/{step}/{is_form}','ClientController@processes')->name('clients.processes');
Route::post('clients/{client}/progress','ClientController@storeProgress')->name('clients.storeprogress');
Route::post('clients/{client}/actions','ClientController@storeActions')->name('clients.storeactions');
Route::post('clients/{client}/progress/{process?}/{step?}','ClientController@completeStep')->name('clients.completestep');
Route::post('clients/{client}/progress2/{process?}/{step?}','ClientController@completeStep2')->name('clients.completestep2');
Route::get('clients/{client}/progress/{process}/{step}/{is_form}','ClientController@stepProgress')->name('clients.stepprogress');
Route::get('clients/{client}/progressaction/{process}/{step}/{action_id}/{is_form}','ClientController@stepProgressAction')->name('clients.stepprogressaction');
Route::get('clients/{client}/documents/{process}/{step}/{is_form}','ClientController@documents')->name('clients.documents');
Route::get('clients/{client}/forms/{process}/{step}','ClientController@forms')->name('clients.forms');
Route::get('clients/{client}/edit/{process}/{step}/{is_form}','ClientController@edit')->name('clients.edit');
Route::get('clients/{client}/forms/uploadform','ClientController@uploadforms')->name('forms.uploadforms');
Route::get('clients/{client}/forms/editform/{formid}','ClientController@edituploadforms')->name('forms.editforms');
Route::post('clients/{client}/forms/storeform','ClientController@storeuploadforms')->name('forms.uploadstore');
Route::post('clients/{clientid}/forms/{formid}/formupdate','ClientController@updateuploadforms')->name('forms.formupdate');
Route::get('clients/{client}/forms/createcrf','ClientController@createCrfForm')->name('forms.createcrf');
Route::get('clients/{client}/forms/editcrf/{formid}','ClientController@editCrfForm')->name('forms.editcrf');
Route::get('clients/{client}/forms/signcrf/{formid}','ClientController@signCrfForm')->name('forms.signcrf');
Route::post('clients/{clientid}/forms/{formid}/updatecrf','ClientController@updateCrfForm')->name('forms.updatecrf');
Route::post('clients/{client}/forms/storecrf','ClientController@saveCrfForm')->name('forms.storecrf');
Route::get('clients/{client}/forms/{formid}/generatecrf','ClientController@generateCrfForm')->name('forms.generatecrf');
Route::get('clients/{client}/forms/upload','ClientController@saveCrfForm')->name('forms.upload');
Route::get('clients/{client}/forms/delete/{formid}','ClientController@deleteCrfForm')->name('crfforms.destroy');
Route::get('clients/{client_id}/completed/{step_id}/{newdate}','ClientController@complete')->name('clients.complete');
Route::get('clients/{client_id}/uncompleted/{step_id}','ClientController@uncomplete')->name('clients.uncomplete');
Route::get('clients/{client_id}/changecompleted/{step_id}/{newdate}','ClientController@changecomplete')->name('clients.changecomplete');
Route::post('clients/{client}/sendnotification/{activity?}','ClientController@sendNotification')->name('clients.sendnotification');
Route::post('clients/{client}/sendtemplate/{activity?}','ClientController@sendTemplate')->name('clients.sendtemplate');
Route::post('clients/{client}/senddocument/{activity?}','ClientController@sendDocument')->name('clients.senddocument');
Route::get('clients/{client}/viewtemplate/{template?}','ClientController@viewTemplate')->name('clients.viewtemplate');
Route::get('clients/{client}/viewdocument/{document?}','ClientController@viewDocument')->name('clients.viewdocument');
/*Route::put('clients/{client}/follow','ClientController@follow')->name('clients.follow');*/
Route::get('clients/{client}/activityprogress/{process}/{step}','ClientController@activityProgress')->name('clients.activityprogress');
Route::get('clients/{client}/getcomments','ClientController@getComment')->name('clients.getcomment');
Route::post('clients/{client}/storecomment','ClientController@storeComment')->name('clients.storecomment');
Route::post('clients/deleteclientcomment/{comment}','ClientController@deleteComment')->name('clients.deletecomment');
Route::post('clients/{client}/progressing','ClientController@storeProgressing')->name('clients.progressing');
/*Route::post('clients/{client}/qa','ClientController@storeQA')->name('clients.qa');*/
/*Route::post('clients/{client}/qacomplete','ClientController@completeQA')->name('clients.qacomplete');
Route::post('clients/{client}/qaprod','ClientController@storeProd')->name('clients.qaprod');*/
Route::post('clients/{client}/approval','ClientController@approval')->name('clients.approval');
Route::post('clients/{client}/senddocuments/{activity?}','ClientController@sendDocuments')->name('clients.senddocuments');

Route::post('clients/{client}/sendmail','ClientController@sendMail')->name('clients.sendMail');
Route::post('clients/{client}/sendwhatsapp','ClientController@sendWhatsapp')->name('clients.sendWhatsapp');

Route::post('voice/record/{card}','TaskController@recordVoice')->name('tasks.voicerecord');

Route::get('clients/{clientid}/overview/{process}/{step}','ClientController@overview')->name('clients.overview');
Route::get('clients/{clientid}/details/{process}/{step}/{is_form}','ClientController@details')->name('clients.details');
Route::get('clients/{client}/delete','ClientController@destroy')->name('clients.delete');
Route::get('clients/{clientid}/restore','ClientController@restore')->name('clients.restore');

Route::post('clients/{clientid}/addcomment/{activityid}','ActivityCommentController@addComment');
Route::post('clients/{clientid}/showcomment/{activityid}','ActivityCommentController@showComment');
Route::post('clients/deletecomment/{commentid}','ActivityCommentController@destroyComment');
Route::post('clients/editcomment/{commentid}','ActivityCommentController@editComment');
Route::post('clients/updatecomment/{commentid}','ActivityCommentController@updateComment');

Route::get('client/{client}/basket/{process}/{step}/{is_form}', 'ClientBasketController@show')->name('client.basket');
Route::get('client/{client}/{process}/clientbasket', 'ClientBasketController@clientBasketActivities');
Route::any('client/{client}/progress/{process}/{step}', 'ClientBasketController@clientProgress')->name('client.progress');
Route::post('client/{client}/storeprogress/{process}/{step}', 'ClientBasketController@clientStoreProgress')->name('client.storeprogress');
Route::post('client/{client}/sendclientemail', 'ClientBasketController@sendClientEmail');

Route::get('activity/include-in-basket', 'ClientBasketController@includeActivityInClientBasket')->name('basket.include');
Route::post('forms/include-in-basket', 'FormsController@includeInputInClientBasket');

Route::get('clients/{client}/actions/{process}/{step}/{is_form}','ClientController@actions')->name('clients.actions');
/*Route::get('qa-checklist/{client_id}/create','QAChecklistController@create')->name('qaChecklist.create');
Route::post('qa-checklist/{client_id}','QAChecklistController@store')->name('qaChecklist.store');
Route::patch('qa-checklist/{client_id}','QAChecklistController@update')->name('qaChecklist.update');
Route::get('qa-checklist','QAChecklistController@report')->name('report.qaChecklist');*/
Route::post('/clients/{client}/assignactivity','ActionsController@assignActivityToUser')->name('clients.assignactivity');
Route::get('/clients/getfirststep/{clientid}/{processid}','ProcessController@getProcessFirstStep');
Route::get('/clients/getnewprocesses/{clientid}','ProcessController@getNewProcesses');
Route::get('/clients/getnewform/{clientid}','ProcessController@getNewForms');
Route::get('/client/get-extra-detail/{section_id}','FormsController@getExtraDetail');

Route::get('storeclientactivity/{token}','ClientController@createClientActivity')->name('clients.createclientactivity');
Route::post('storeclientactivity/{token}','ClientController@storeClientActivity')->name('clients.storeclientactivity');

Route::get('/clients/{clientid}/autocomplete_process/{processid}/{newprocess}','ClientController@autocompleteClientProcess');
Route::get('/clients/{clientid}/keep_process/{processid}/{newprocess}','ClientController@keepClientProcess');
Route::get('/clients/{clientid}/current_applications','ClientController@getClientCurrentProcesses');
Route::get('/clients/{clientid}/closed_applications','ClientController@getClientClosedProcesses');
Route::get('/clients/{clientid}/get_applications','ClientController@getClientProcesses');
Route::get('/clients/{clientid}/get_forms','ClientController@getClientForms');
Route::get('/clients/{clientid}/getactivitymirrorvalues/{activityid}','ClientController@getActivityMirrorValues');

Route::get('/clients/{clientid}/getdetail','ClientController@getClientDetail');
Route::post('/clients/{clientid}/saveconsultant','ClientController@storeConsultant');
Route::post('/client/complete/{client}','ClientController@completeClient');
Route::post('/client/work-item-qa/{client}','ClientController@WorkItemQA')->name('workitem.qa');
Route::post('/related/work-item-qa/{relatedparty}','RelatedPartyController@WorkItemQA');
Route::post('/client/checkactivities/{client}','ClientController@checkClientActivities');
Route::get('/clients/{clientid}/isprogressing', 'ClientController@isProgressing');

Route::resource('referrers', 'ReferrerController');
Route::resource('documents', 'DocumentController');
Route::delete('documents/{id}/{client_id}/destroy/{process_id}/{step_id}', 'DocumentController@destroy')->name('documents.destroy');

Route::resource('templates', 'TemplateController');
Route::get('/templates/activities/{process_id}', 'TemplateController@getVars');
Route::post('/templates/clients', 'TemplateController@getClientVars');

Route::get('roles', 'RoleController@index')->name('roles.index');
Route::get('roles/create', 'RoleController@create')->name('roles.create');
Route::post('roles', 'RoleController@store')->name('roles.store');
Route::put('roles', 'RoleController@update')->name('roles.update');
Route::delete('roles/{role?}', 'RoleController@destroy')->name('roles.destroy');

/*Route::get('businessunits', 'BusinessUnitController@index')->name('businessunits.index');
Route::get('businessunits/create', 'BusinessUnitController@create')->name('businessunits.create');
Route::post('businessunits/store', 'BusinessUnitController@store')->name('businessunits.store');
Route::get('businessunits/{businessunit}/edit', 'BusinessUnitController@edit')->name('businessunits.edit');
Route::post('businessunits/{businessunit}/update', 'BusinessUnitController@update')->name('businessunits.update');
Route::delete('businessunits/{businessunit}/delete', 'BusinessUnitController@destroy')->name('businessunits.destroy');
Route::get('businessunits/{businessunit}/show', 'BusinessUnitController@show')->name('businessunits.show');*/

/*Route::get('committee', 'CommitteeController@index')->name('committees.index');
Route::get('committee/create', 'CommitteeController@create')->name('committees.create');
Route::post('committee/store', 'CommitteeController@store')->name('committees.store');
Route::get('committee/{committee}/edit', 'CommitteeController@edit')->name('committees.edit');
Route::post('committee/{committee}/update', 'CommitteeController@update')->name('committees.update');
Route::get('committee/{committee}/delete', 'CommitteeController@destroy')->name('committees.destroy');
Route::get('committee/{committee}/show', 'CommitteeController@show')->name('committees.show');*/

/*Route::get('project', 'ProjectController@index')->name('projects.index');
Route::get('project/create', 'ProjectController@create')->name('projects.create');
Route::post('project/store', 'ProjectController@store')->name('projects.store');
Route::get('project/{project}/edit', 'ProjectController@edit')->name('projects.edit');
Route::post('project/{project}/update', 'ProjectController@update')->name('projects.update');
Route::get('project/{project}/delete', 'ProjectController@destroy')->name('projects.destroy');
Route::get('project/{project}/show', 'ProjectController@show')->name('projects.show');*/


Route::resource('calendarevents', 'CalendarEventController');
Route::get('calendarevents/accept/{id}', 'CalendarEventController@accept')->name('calendarevents.accept');
Route::get('calendarevents/reject/{id}', 'CalendarEventController@reject')->name('calendarevents.reject');
// Route::get('azure/token', 'MicrosoftCalendarController@microsoftToken')->name('azure.token');
Route::get('azure/signin', 'MicrosoftAuthController@signin')->name('azure.signin');
Route::get('azure/callback', 'MicrosoftAuthController@callback')->name('azure.callback');
Route::get('azure/signout', 'MicrosoftAuthController@signout')->name('azure.signout');
Route::get('azure/calendar_list', 'MicrosoftCalendarController@listView')->name('azure.calendar_list');
Route::resource('azure/calendar', 'MicrosoftCalendarController');
Route::resource('azure_task', 'MicrosoftTaskController');

//Route::resource('processes', 'ProcessController');
Route::get('processes', 'ProcessController@groupIndex')->name('processesgroup.index');
Route::get('processes/create', 'ProcessController@groupCreate')->name('processesgroup.create');
Route::post('processes/store', 'ProcessController@groupStore')->name('processesgroup.store');
Route::get('processes/{group_id}', 'ProcessController@index')->name('processes.index');
Route::get('processes/{group_id}/edit', 'ProcessController@groupEdit')->name('processesgroup.edit');
Route::post('processes/{group_id}/update', 'ProcessController@groupUpdate')->name('processesgroup.update');
Route::delete('processes/{group_id}', 'ProcessController@groupDestroy')->name('processesgroup.destroy');
Route::get('processes/step_count/{process_id}', 'ProcessController@processStepCount');
Route::delete('processes/{group_id}/{process}/{processid}', 'ProcessController@destroy')->name('processes.destroy');
Route::get('processes/{group_id}/create', 'ProcessController@create')->name('processes.create');
Route::post('processes/{group_id}/store', 'ProcessController@store')->name('processes.store');
Route::get('processes/{group_id}/{process}/show', 'ProcessController@show')->name('processes.show');
Route::get('processes/{group_id}/{process}/edit', 'ProcessController@edit')->name('processes.edit');
Route::post('processes/{group_id}/{process}/update', 'ProcessController@update')->name('processes.update');
Route::post('processes/{process}/copy', 'ProcessController@copy')->name('process.copy');
Route::get('processes/{process}/steps/create', 'StepController@create')->name('steps.create');
Route::post('processes/{process}/steps/create', 'StepController@store')->name('steps.store');
Route::get('steps/{step}/edit', 'StepController@edit')->name('steps.edit');
Route::put('steps/{step}/edit', 'StepController@update')->name('steps.update');
Route::delete('steps/{step}', 'StepController@destroy')->name('steps.destroy');
Route::post('steps/{step}/move', 'StepController@move')->name('steps.move');


Route::resource('forms', 'FormsController');
Route::get('forms/{form}/edit', 'FormsController@edit')->name('forms.edit');
Route::post('forms/{formid}/update', 'FormsController@update')->name('forms.update');
Route::get('forms/{client}/edit_form/{form_id}', 'FormsController@editDynamicForm')->name('forms.create_dynamic_form');
Route::post('forms/{client}/storeform/{formid}', 'FormsController@storeDynamicForm')->name('forms.storeform');
Route::get('forms/{form}/show', 'FormsController@show')->name('forms.show');
Route::delete('forms/{form}/{formid}', 'FormsController@destroy')->name('forms.destroy');

Route::resource('forms_section', 'FormSectionController');
Route::get('forms_section/{form}/create', 'FormSectionController@create')->name('form_section.create');
Route::post('forms_section/{form}/store', 'FormSectionController@store')->name('form_section.store');
Route::get('forms_section/{form}/edit', 'FormSectionController@edit')->name('form_section.edit');
Route::put('forms_section/{form_section}/update', 'FormSectionController@update')->name('form_section.update');
Route::delete('forms_section/{form}/destroy', 'FormSectionController@destroy')->name('form_section.destroy');
Route::post('forms_section/{form}/move', 'FormSectionController@move')->name('form_section.move');


Route::resource('actions', 'ActionsController');
Route::get('actions', 'ActionsController@index')->name('action.index');
Route::post('actions/save', 'ActionsController@store')->name('action.save');
Route::post('actions/save_send', 'ActionsController@storeSend')->name('action.save_send');
Route::get('actions/{action_id}/edit', 'ActionsController@edit')->name('action.edit');
Route::post('actions/{action_id}/update', 'ActionsController@update')->name('action.update');
Route::post('actions/{action_id}/update_send', 'ActionsController@updateSend')->name('action.update_send');
Route::post('/get_process_steps/{process_id}','ActionsController@getProcessSteps');
Route::post('/get_action_process_activities/{process_id}/{step_id}','ActionsController@getActionActivity');
Route::post('/get_action_process_selected_activities/{process_id}/{step_id}','ActionsController@getSelectedActionActivity');
Route::post('/get_addaction_process_selected_activities/{process_id}/{step_id}','ActionsController@getSelectedAddActionActivity');
Route::post('/clear_addaction_activities','ActionsController@clearAddActionActivity');
Route::post('/set_addaction_activities/{activityid}','ActionsController@setAddActionActivity');
Route::post('/get_action_activities/{process_id}/{step_id}/{action_id}','ActionsController@getActionActivities');
Route::post('/get_edit_action_process_activities/{process_id}/{step_id}/{activity_id}','ActionsController@getEditActionActivity');
Route::post('/search_action_process_activities/{search}','ActionsController@searchActionActivity');
Route::post('/search_action_activities/{process_id}/{step_id}/{search}','ActionsController@searchActionActivities');
Route::post('/search_addaction_activities/{process_id}/{step_id}/{search}','ActionsController@searchAddActionActivities');
Route::post('/store_client_action/{client_id}','ActionsController@storeClientAction');
Route::post('/search_action/{search}','ActionsController@searchAction');
Route::post('/store_action_activity/{activity_id}','ActionsController@storeActionActivity');
Route::post('/store_addaction_activity/{activity_id}','ActionsController@storeAddActionActivity');
Route::get('actions/{id}/deactivate', 'ActionsController@deactivate')->name('action.deactivate');
Route::get('actions/{id}/activateuser', 'ActionsController@activate')->name('action.activate');
Route::get('/search_clients/{search}','ClientController@searchClients');
Route::get('/get_clients','ClientController@getClients');

Route::resource('custom_report', 'CustomReportController');
Route::get('custom_report/create', 'CustomReportController@create')->name('custom_report.create');
Route::get('custom_report/{custom_report_id}/{report_type}', 'CustomReportController@show')->name('custom_report.show');
Route::get('custom_report/{custom_report_id}/edit', 'CustomReportController@edit')->name('custom_report.edit');
Route::post('custom_report/{custom_report_id}/update', 'CustomReportController@update')->name('custom_report.update');
Route::get('custom_report/{custom_report_id}/{report_type}/pdfexport', 'CustomReportController@pdfexport')->name('custom_report.pdfexport');
Route::delete('custom_report/{custom_report_id}', 'CustomReportController@destroy')->name('custom_report.destroy');
Route::get('customreport/{custom_report_id}/{report_type}/export', 'CustomReportController@export')->name('custom_report.export');


Route::get('/get_report_activities/{process_id}','CustomReportController@getActivities');
Route::get('/get_report_selected_activities/{custom_report_id}','CustomReportController@getSelectedActivities');

Route::prefix('reports')->group(function () {
    Route::get('', 'ReportController@index')->name('reports.index');
    Route::post('store', 'ReportController@store')->name('reports.store');
    Route::get('create', 'ReportController@create')->name('reports.create');
    Route::delete('destroy/{report}', 'ReportController@destroy')->name('reports.destroy');
    Route::put('update/{report}', 'ReportController@update')->name('reports.update');
    //Todo - Invetigate why the resource statement above breaks the below report, thus the resource hardcode blog above
    Route::get('converted', 'ReportController@converted')->name('reports.converted');
    Route::get('fees', 'ReportController@fees')->name('reports.fees');
    Route::get('conversion', 'ReportController@conversion')->name('reports.conversion');
    Route::get('cch', 'ReportController@cch')->name('reports.cch');
    Route::get('feeproposalsent', 'ReportController@feeProposalSent')->name('reports.feeproposalsent');
    Route::get('aml', 'ReportController@aml')->name('reports.aml');
    Route::get('loe', 'ReportController@loe')->name('reports.loe');
    Route::get('loa', 'ReportController@loa')->name('reports.loa');
    Route::get('crf', 'ReportController@crf')->name('reports.crf');
    Route::get('referrer', 'ReportController@referrer')->name('reports.referrer');
    Route::get('clientreports', 'ReportController@clientReports')->name('reports.clientreports');
    Route::get('{activity}/show', 'ReportController@show')->name('reports.show');
    Route::get('{reportid}/edit', 'ReportController@edit')->name('reports.edit');
    Route::post('{reportid}/update', 'ReportController@update')->name('reports.update');
    Route::get('{activity}/export', 'ReportController@export')->name('reports.export');
    Route::get('{activity}/pdfexport', 'ReportController@pdfexport')->name('reports.pdfexport');
    Route::get('assigned_actions', 'ReportController@assignedactivities')->name('reports.assigned_actions');
    Route::post('powerpointexport', 'ReportController@powerpointExport')->name('reports.powerpointexport');
    Route::post('generate_report_export', 'ReportController@generateReportExport')->name('reports.generate_report_export');
    //Route::get('generatepowerpoint', 'ReportController@generatePowerPoint')->name('reports.powerpointgenerate');
    //Route::get('powerpointshow', 'ReportController@powerpointShow')->name('reports.powerpointshow');
    Route::get('generate_report', 'ReportController@generateReport')->name('reports.generate_report');
    Route::get('auditreport', 'ReportController@auditReport')->name('reports.auditreport');
    Route::get('usagereport', 'UsageReportController@index')->name('reports.usage');
    Route::get('productivityreport', 'ProductivityReportController@index')->name('reports.productivity');
    Route::get('sla', 'SLAReportController@index')->name('reports.sla');

});

Route::post('reports/productivityreportajax', 'ProductivityReportController@ajaxCall');

Route::get('assigned_actions/{assignedactionid}/delete/{activityid}', 'ActionsController@deleteAssignedAction')->name('assignedactions.delete');
Route::get('assigned_actions/{assignedactionid}/complete/{activityid}', 'ActionsController@completeAssignedAction')->name('assignedactions.complete');
Route::get('assigned_actions/pdfexport', 'ReportController@assignedactivitiesPdfExport')->name('assignedactions.pdfexport');
Route::get('assigned_actions/export', 'ReportController@assignedactivitiesExport')->name('assignedactions.export');
Route::get('audit/pdfexport', 'ReportController@auditPdfExport')->name('audit.pdfexport');
Route::get('audit/export', 'ReportController@auditExport')->name('audit.export');
Route::get('/reports/getprocesssteps/{process_id}', 'ProcessController@getSteps');

Route::prefix('graphs')->group(function () {
    Route::get('newclients', 'GraphController@newClients')->name('graphs.newclients');
    Route::get('targetdata', 'GraphController@targetData')->name('graphs.targetdata');
    Route::get('yearlycomparison', 'GraphController@yearlyComparison')->name('graphs.yearlycomparison');
    Route::get('qtrcomparison', 'GraphController@grtCompparison')->name('graphs.qtrcomparison');
    Route::get('gtrfeestotal', 'GraphController@gtrFeesTotal')->name('graphs.gtrfeestotal');

    //Dashboard
    Route::get('getoutstandingactivitiesajax', 'HomeController@getOutstandingActivitiesAjax')->name('graphs.outstandingactivities');
    Route::get('getcompletedclientsajax', 'HomeController@getCompletedClientsAjax')->name('graphs.completedclients');
});

Route::resource('calendarevents', 'CalendarEventController');
Route::get('calendarevents/accept/{id}', 'CalendarEventController@accept')->name('calendarevents.accept');
Route::get('calendarevents/reject/{id}', 'CalendarEventController@reject')->name('calendarevents.reject');

Route::get('locations','LocationController@index')->name('locations.index');
Route::resource('divisions', 'DivisionController');
Route::resource('regions', 'RegionController');
Route::resource('areas', 'AreaController');
Route::resource('offices', 'OfficeController');
Route::post('get_offices', 'AreaController@getOffices');


Route::get('/users/qausers/{client}', 'UserController@getqausers');
Route::resource('users', 'UserController');
Route::get('users/{user}/deactivate', 'UserController@deactivate')->name('users.deactivate');
Route::get('users/{user}/activateuser', 'UserController@activateuser')->name('users.activateuser');


Route::get('insight','InsightController@index')->name('insight.index');

Route::get('configs','ConfigController@index')->name('configs.index');
Route::put('configs','ConfigController@update')->name('configs.update');
Route::get('config/theme', 'ConfigController@createTheme')->name('theme.create');
Route::Post('config/theme', 'ConfigController@storeTheme')->name('theme.store');
Route::Patch('config/theme/{theme}', 'ConfigController@updateTheme')->name('theme.update');
Route::get('/get_process_steps/{process_id}','ConfigController@getProcessSteps');
Route::get('/get_process_avg_steps/{process_id}','ConfigController@getProcessAvgSteps');
Route::get('/get_outstanding_step/{process_id}','ConfigController@getOutstandingStep');
Route::get('/get_outstanding_activities/{step_id}','ConfigController@getOutstandingActivities');
Route::get('/get_process_steps_for_ageing/{process_id}','ConfigController@getProcessStepToCalculateAgeFrom');

Route::get('signin', 'AuthController@signin');
Route::get('/authorize', 'AuthController@gettoken');

Route::get('outlook/mail', 'OutlookController@mail')->name('outlook.mail');
Route::get('outlook/calendar', 'OutlookController@calendar')->name('outlook.calendar');


Route::get('activitieslog/{id}', 'LogController@activityLog')->name('activitieslog');

Route::get('/user/verify/{token}', 'Auth\RegisterController@verifyUser');
Route::get('/check_password/{password}', 'UserController@verifyPassword');

Route::resource('emailtemplates','EmailTemplateController');
Route::get('emailtemplates/create','EmailTemplateController@create')->name('emailtemplates.create');
Route::post('emailtemplates/store','EmailTemplateController@store')->name('emailtemplates.store');
Route::get('emailtemplates/{id}/edit','EmailTemplateController@edit')->name('emailtemplates.edit');
Route::post('emailtemplates/{id}/update','EmailTemplateController@update')->name('emailtemplates.update');
Route::delete('emailtemplates/{id}/destroy','EmailTemplateController@destroy')->name('emailtemplates.destroy');
Route::get('editemail/{id}','EmailTemplateController@ajaxedit');
Route::post('updateemail/{id}','EmailTemplateController@ajaxupdate');
Route::post('getsubject/{id}','EmailTemplateController@getSubject');

Route::resource('emailsignatures','EmailSignatureController');
Route::get('emailsignatures/create','EmailSignatureController@create')->name('emailsignatures.create');
Route::post('emailsignatures/store','EmailSignatureController@store')->name('emailsignatures.store');
Route::get('emailsignatures/{id}/edit','EmailSignatureController@edit')->name('emailsignatures.edit');
Route::post('emailsignatures/{id}/update','EmailSignatureController@update')->name('emailsignatures.update');
Route::delete('emailsignatures/{id}/destroy','EmailSignatureController@destroy')->name('emailsignatures.destroy');

Route::resource('whatsapptemplates','WhatsappTemplateController');
Route::get('whatsapptemplates/create','WhatsappTemplateController@create')->name('whatsapptemplates.create');
Route::post('whatsapptemplates/store','WhatsappTemplateController@store')->name('whatsapptemplates.store');
Route::get('whatsapptemplates/{id}/edit','WhatsappTemplateController@edit')->name('whatsapptemplates.edit');
Route::post('whatsapptemplates/{id}/update','WhatsappTemplateController@update')->name('whatsapptemplates.update');
Route::delete('whatsapptemplates/{id}/destroy','WhatsappTemplateController@destroy')->name('whatsapptemplates.destroy');
Route::get('whatsapptemplates/{id}/gettemplate/{client_id}','WhatsappTemplateController@getTemplate');


Route::get('tasktypes','TaskTypeController@index')->name('task_types.index');
Route::get('tasktypes/create','TaskTypeController@create')->name('task_types.create');
Route::post('tasktypes/store','TaskTypeController@store')->name('task_types.store');
Route::get('tasktypes/{id}/edit','TaskTypeController@edit')->name('task_types.edit');
Route::post('tasktypes/{id}/update','TaskTypeController@update')->name('task_types.update');
Route::delete('tasktypes/{id}/destroy','TaskTypeController@destroy')->name('task_types.destroy');

Route::post('/readnotifications','UserController@readNotifications');
Route::post('/readnotificationshistory','UserController@readNotificationsHistory');
Route::post('/markallnotifications','UserController@markAllNotifications');

Route::resource('notifications', 'NotificationHistoryController');
Route::post('/getnotificationscount','UserController@getNotificationsCount');

Route::post('/getqacount','QAController@getQACount');
Route::post('/getqas','QAController@getQAs');
Route::post('/markallqanotifications','QAController@markAllQANotifications');

Route::post('/getmessagecount','MessagesController@getMessageCount');
Route::post('/getmessages','MessagesController@getMessages');

Route::get('search','SearchController@getResults');

/*Route::resource('relatedparty','RelatedPartyController');
Route::get('relatedparty/{client}/{related_party}/progress','RelatedPartyController@progress')->name('relatedparty.progress');
Route::get('relatedparty/{client}/{related_party}/documents','RelatedPartyController@documents')->name('relatedparty.documents');
Route::get('relatedparty/show/{client_id}', 'RelatedPartyController@related_index')->name('relatedparty.related_index');
Route::post('relatedparty/save/{client_id}/{related_party_id}', 'RelatedPartyController@store');
Route::get('relatedparty/{client_id}/{process_id}/{step_id}/related_party/{related_party_id}', 'RelatedPartyController@stepProgress')->name('relatedparty.stepprogress');
Route::get('relatedparty/{client_id}/{related_party_id}/{process_id}/{step_id}/details', 'RelatedPartyController@show')->name('relatedparty.show');
Route::get('relatedparty/{process_id}/edit/{related_party_id}', 'RelatedPartyController@edit')->name('relatedparty.edit');
Route::post('relatedparty/update/{related_party_id}', 'RelatedPartyController@update')->name('relatedparty.update');
Route::get('relatedparty/activities/{client_id}/{related_party_id}', 'RelatedPartyController@activities')->name('relatedparty.activities');
Route::get('relatedparty/getclient/{client_id}', 'RelatedPartyController@getClient')->name('relatedparty.getclient');
Route::get('relatedparty/addactivities/{client_id}/{process_id}/{step_id}', 'RelatedPartyController@addActivities')->name('relatedparty.addactivities');
Route::post('relatedparty/{client}/sendnotification/{related_party}/{activity?}','RelatedPartyController@sendNotification')->name('relatedparty.sendnotification');
Route::post('relatedparty/{client}/sendtemplate/{related_party}/{activity?}','RelatedPartyController@sendTemplate')->name('relatedparty.sendtemplate');
Route::get('relatedparty/{client}/viewtemplate/{related_party}/{template?}','RelatedPartyController@viewTemplate')->name('relatedparty.viewtemplate');
Route::post('relatedparty/{client}/senddocument/{related_party}/{activity?}','RelatedPartyController@sendDocument')->name('relatedparty.senddocument');
Route::get('relatedparty/{client}/viewdocument/{related_party}/{document?}','RelatedPartyController@viewDocument')->name('relatedparty.viewdocument');
Route::post('relatedparty/{client}/senddocuments/{related_party}/{activity?}','RelatedPartyController@sendDocuments')->name('relatedparty.senddocuments');
Route::post('relatedparty/{client}/{related_party}/storecomment','RelatedPartyController@storeComment')->name('relatedparty.storecomment');
Route::post('relatedparty/{client}/{related_party}/progressing','RelatedPartyController@storeProgressing')->name('relatedparty.progressing');
Route::post('relatedparty/{client}/{related_party}/approval','RelatedPartyController@approval')->name('relatedparty.approval');
Route::get('relatedparty/{client}/{related_party}/delete','RelatedPartyController@destroy')->name('relatedparty.delete');
Route::get('relatedparty/{clientid}/{related_party}restore','RelatedPartyController@restore')->name('relatedparty.restore');
Route::post('relatedparty/{related_party}/progress/{process?}/{step?}','RelatedPartyController@completeStep')->name('relatedparty.completestep');
Route::post('relatedparty/{related_party}/progress2/{process?}/{step?}','RelatedPartyController@completeStep2')->name('relatedparty.completestep2');
Route::get('/relatedparty/edit/{parent_id}/{related_party}','RelatedPartyController@editRelatedParty');
Route::get('/relatedparty/delete/{parent_id}/{related_party}','RelatedPartyController@deleteRelatedParty');
Route::post('/relatedparty/update/{parent_id}/{related_party}','RelatedPartyController@updateRelatedParty');
Route::post('/relatedparty/link/{parent_id}/{related_party}','RelatedPartyController@linkRelatedParty');
Route::post('/relatedparty/manage/{related_party}','RelatedPartyController@manageRelatedParty');
Route::post('/relatedparty/complete/{related_party}','RelatedPartyController@completeRelatedParty');
Route::post('/relatedparty/checkactivities/{related_party}','RelatedPartyController@checkRelatedPartyActivities');*/


Route::get('/ajax_upload', 'FileController@index');

Route::post('/ajax_upload/action', 'FileController@action')->name('ajaxupload.action');

/*Route::get('/organogram/{client_id}','RelatedPartyController@organogram');
Route::post('/save_jpg','ReportController@saveJpg');*/

Route::post('/video/upload','ActionableVideoUploadController@upload');
Route::get('video/{filename}', function ($filename)
{
    $path = storage_path('app/files/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
});
Route::post('/image/upload','ActionableImageUploadController@upload');

/*Route::get('qa-analyst-report', 'QAChecklistAnalystController@index')->name('qaanalystreport.index');*/

Route::get('docFusionAPI/{clientId}/{process_id}', 'APIController@index')->name('docFusionAPI.index');
// Route::get('clients/submit_for_signature/{clientId}/{process_id}', 'APIController@index')->name('docFusionAPI.index');
Route::get('clients/submit_for_signature/{clientId}/{process_id}', 'APIController@index')->name('docFusionAPI.index');

Route::post('docFusionAPI/updatedocument', 'APIController@updateDocument');

Route::get('is-wizard-dismissed', 'WizardController@isWizardDismissed');
Route::post('dismiss-wizard', 'WizardController@dismissWizard');
Route::get('fa-details/load', 'WizardController@FADetails');
Route::post('fa-details', 'WizardController@storeFADetails');

Route::post('/rules','ActivityController@getRules');
Route::post('/getrule','ActivityController@getRule');
Route::get('/getprocesses2', 'ProcessController@getProcesses2');
Route::get('getsteps','StepController@getSteps');
Route::get('getsections','FormSectionController@getSections');
Route::get('getsectioninputs','FormSectionInputController@getInputs');
Route::get('getstepactivities','ActivityController@getStepActivities');
Route::get('getremainingsteps','StepController@getRemainingSteps');
Route::get('getactivities','ActivityController@getActivities');
Route::get('getactivitytype','ActivityController@getActivityType');
Route::get('getdependantactivities','ActivityController@getDependantActivities');
Route::get('getdependantsteps','StepController@getDependantSteps');
Route::get('getdropdownitems','ActivityController@getDropdownItems');
Route::get('getdropdowntext','ActivityController@getDropdownText');
Route::post('/getcurrencies', 'ActivityController@getCurrencies');

Route::post('senddocuments', 'DocumentController@send');

Route::get('trial', 'DismissTrialController@index');
Route::get('subscription/cancel/{user}', 'DismissTrialController@cancelSubscription')->name('subscription.cancel');
Route::post('dismiss-trial', 'DismissTrialController@store');

Route::get('passport/clients', 'PassportController@clients')->name('passport.clients');
Route::get('passport/authorizedclients', 'PassportController@authorizedClients')->name('passport.authorizedclients');
Route::get('passport/personalaccesstokens', 'PassportController@personalAccessTokens')->name('passport.personalaccesstokens');

Route::get('workflows/{board_id}', 'BoardController@workflows')->name('workflows');
Route::get('workflows', 'BoardController@workflows')->name('workflows');
Route::resource('board', 'BoardController');
Route::resource('section', 'SectionController');
Route::post('section/delete', 'SectionController@destroy');
Route::post('board/update', 'BoardController@update');
Route::post('board/delete', 'BoardController@destroy');
Route::post('board/getboards', 'BoardController@getBoards');
Route::post('board/getboardsections', 'BoardController@getBoardSections');
Route::post('task/subtask/{task}', 'TaskController@storeSubtask')->name('store.subtask');
Route::any('task/update_date/{task}', 'TaskController@updateDueDate');
Route::any('task/update_status/{task}', 'TaskController@updateStatus');
Route::any('task/delete/{task}', 'TaskController@delete');
Route::any('task/advisor', 'TaskController@getAdvisor');
Route::get('email_templates', 'EmailTemplateController@getTemplates');
Route::get('email_template/{template_id}', 'EmailTemplateController@getTemplate');
Route::post('send_email_template', 'EmailTemplateController@sendEmailTemplate');
Route::resource('task', 'TaskController');
Route::get('cardtemplates/{sectionid}', 'CardTemplateController@index');
Route::get('cardtemplates/show/{sectionid}', 'CardTemplateController@show');
Route::get('card/delete/{card_id}', 'CardController@destroy')->name('card.delete');
Route::get('card/archive/{card_id}', 'CardController@archive')->name('card.archive');
Route::get('card/unarchive/{card_id}', 'CardController@unarchive')->name('card.unarchive');
Route::get('card/complete_tasks/{card_id}', 'CardController@completeTasks')->name('card.completetasks');
Route::get('card/status', 'CardController@getStatuses')->name('card.status');
Route::get('card/get-cards', 'CardController@getCardsDropDown');
Route::get('card/get-office-clients', 'CardController@getOfficeClients');
Route::get('cards', 'CardController@getCardsDropDown')->name('cards');
Route::post('card/attachdocument', 'CardController@uploadDocument');
Route::post('card/deletedocument', 'CardController@deleteDocument');
Route::post('card/copy', 'CardController@copyCard');
Route::post('card/move', 'CardController@moveCard');
Route::resource('card', 'CardController');

Route::post('card-template/save', 'CardTemplateController@store');
Route::resource('discussion', 'DiscussionController');

Route::post('user-task/save','UserTaskController@store')->name('user-task.save');
Route::get('user-task/{id}/show','UserTaskController@show');
Route::post('user-task/{id}/update','UserTaskController@update');
Route::get('user-task/{id}/complete','UserTaskController@complete');
Route::get('user-task/{id}/delete','UserTaskController@delete');

Route::post('billboard-message/save','BillboardMessageController@store');
Route::get('billboard-message/{id}/show','BillboardMessageController@show');
Route::post('billboard-message/{id}/update','BillboardMessageController@update');
Route::get('billboard-message/{id}/complete','BillboardMessageController@complete');
Route::get('billboard-message/{id}/delete','BillboardMessageController@delete');

Route::post('/message', 'WhatsappController@message')->name('clients.whatsappMessage');
Route::post('/saveinfo', 'ClientController@saveInfo');

// Route::post('/webhooks/status', function(Request $request) {
//     $data = $request->all();
//     Log::Info($data);
// });

