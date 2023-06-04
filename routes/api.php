<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/signiflow/login', 'SigniFlowController@login')->name('signiflow.login');
Route::get('/signiflow/getsigniflowdocument/{client_id}/{user_id}/{process_id}', 'SigniFlowController@getSigniflowDocument')->name('signiflow.getsigniflowdocument');
Route::get('/signiflow/getclientconsent/{client_id}', 'SigniFlowController@getClientConsent')->name('signiflow.getclientconsent');
Route::get('/signiflow/getstatutorynotice/{client_id}', 'SigniFlowController@getStatutoryNotice')->name('signiflow.getstatutorynotice');
// Route::get('/signiflow/getsigneddocument', 'SigniFlowController@getSignedDocument')->name('signiflow.getsigneddocument');

Route::post('/address/kyc/individual', 'BureauController@confirmIndividualKYCAddress')->name('address.kyc.individual');
Route::post('/cpb/idv/confirm', 'BureauController@confirmIDV')->name('cpb.idv.confirm');
Route::post('/cpb/getproofofaddress', 'BureauController@getProofOfAddress')->name('cpb.getproofofaddress');
Route::get('/cpb/getidvlist', 'BureauController@getIDVList')->name('cpb.idvlist');
Route::get('/cpb/getaddresslist', 'BureauController@getAddressList')->name('cpb.getaddresslist');
Route::get('/cpb/getemploymentlist', 'BureauController@getEmploymentList')->name('cpb.getemploymentlist');
Route::get('/cpb/gettelephonelist', 'BureauController@getTelephoneList')->name('cpb.gettelephonelist');
Route::post('/cpb/getavs', 'BureauController@getAVS')->name('cpb.getavs');
// Route::get('/testlogin', 'BureauController@testlogin')->name('testlogin');

Route::post('/login', 'AuthController@login');
Route::post('/register', 'AuthController@register');
Route::middleware('auth:api')->post('/logout', 'AuthController@logout');

Route::get('/search','SearchController@getResults');
Route::get('getusers','UserController@getUsers');
Route::post('getnotifications','UserController@getNotifications');

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', 'Auth\AuthController@login')->name('login');
    Route::post('register', 'Auth\AuthController@register');
    Route::group(['middleware' => 'auth:api'], function() {
        Route::get('logout', 'Auth\AuthController@logout');
        Route::get('user', 'Auth\AuthController@user');
    });
});

Route::post('/whatsapp-replies', 'WhatsappController@listenToReplies');

/* Workflow Intergration - Start */
Route::get('/clients/get-new-processes','API\WorkflowIntergrationController@getNewProcesses')->name('clients.get-new-process');
Route::post('/clients/start-process','API\WorkflowIntergrationController@startProcess')->name('clients.start-process');
Route::post('/execution/create-trigger','API\WorkflowIntergrationController@createTrigger')->name('execution.create-trigger');
Route::get('/clients/get-all-processes','API\WorkflowIntergrationController@getAllProcesses')->name('clients.get-all-process');
Route::get('/get-client','API\WorkflowIntergrationController@getClient')->name('clients.get-client');
Route::post('/create-client','API\WorkflowIntergrationController@createClient')->name('clients.create-client');
/* Workflow Intergration - End */

/* Boards - Start */
Route::get('/get-boards','API\BoardController@index')->name('get-boards');
Route::post('/create-board','API\BoardController@store')->name('create-board');
/* Boards - End */

/* Boards - Start */
Route::get('/get-boards','API\BoardController@index')->name('get-boards');
Route::post('/create-board','API\BoardController@store')->name('create-board');
/* Boards - End */

/* Section - Start */
Route::get('/get-sections','API\SectionController@index')->name('get-sections');
Route::post('/create-section','API\SectionController@store')->name('create-section');
/* Section - End */

/* Section - Start */
Route::get('/get-cards','API\CardController@index')->name('get-cards');
Route::post('/create-card','API\CardController@store')->name('create-card');
/* Section - End */

/* Comments - Start */
Route::get('/clients/get-comments','API\CommentController@index')->name('clients.get-comments');
Route::post('/clients/create-comment','API\CommentController@store')->name('clients.create-comment');
/* Comments - End */