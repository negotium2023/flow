<?php

namespace App\Http\Controllers\API;

use App\Client;
use App\ClientProcess;
use App\FormInputAmountData;
use App\FormInputBooleanData;
use App\FormInputDateData;
use App\FormInputDropdownData;
use App\FormInputIntegerData;
use App\FormInputPercentageData;
use App\FormInputTextareaData;
use App\FormInputTextData;
use App\Forms;
use App\FormSection;
use App\Process;
use App\ProcessGroup;
use App\Step;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WorkflowIntergrationController extends Controller
{
    public function __construct()
    {
        // $this->authorize('auth');
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trigger_name' => 'required|string',
            'trigger_date' => 'required|date_format:Y-m-d',
            'process_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => 0,
                'message' => 'Validation failed',
                'data' => $validator->errors()
            ], 422);
        }

        try {

            $client = new Client;
            $client->company = $request->input('company');
            $client->company_registration_number = $request->input('company_reg');
            $client->first_name = $request->input('trigger_name');
            $client->last_name = $request->input('last_name');
            $client->initials = $request->input('initials');
            $client->known_as = $request->input('known_as');
            // $client->id_number = $request->input('id_number');
            $client->completed_at = $request->input('trigger_date');
            $client->email = $request->input('email');
            $client->contact = $request->input('contact');
            $client->contact_office = $request->input('contact_office');
            // $client->contact_role = $request->input('contact_role');
            $client->introducer_id = 1;
            $client->office_id = 1;
            $client->process_id = 1; // $request->input('process');
            $client->step_id = Step::where('process_id', $client->process_id)->orderBy('order','asc')->first()->id;
            $client->needs_approval = 0;
            $client->crm_id = 2; // $request->input('crm');
            if($request->has('parent_client') && $request->input('parent_client') != ''){
                $client->parent_id = $request->input('parent_client');
            }
            if(Auth::check() && Auth::user()->is('consultant')){
                $client->consultant_id = Auth::id();
                $client->assigned_date = now();
            }

            $client->save();

            Client::where('id',$client->id)->update([
                'hash_first_name' => DB::raw("AES_ENCRYPT('".addslashes($request->input('first_name'))."','Qwfe345dgfdg')"),
                'hash_last_name' => DB::raw("AES_ENCRYPT('".addslashes($request->input('last_name'))."','Qwfe345dgfdg')"),
                // 'hash_id_number' => DB::raw("AES_ENCRYPT('".addslashes($request->input('id_number'))."','Qwfe345dgfdg')"),
                'hash_email' => DB::raw("AES_ENCRYPT('".addslashes($request->input('company_email'))."','Qwfe345dgfdg')"),
                'hash_contact' => DB::raw("AES_ENCRYPT('".addslashes($request->input('contact'))."','Qwfe345dgfdg')")
            ]);

            // Call this to autopopulate from CBP, just comment out if you would like to manuaaly fill the forms
            /*if($client->id_number != '') {
                $this->autoPopulateFromCBP($client->id);
            }*/

            $cp = new ClientProcess();
            $cp->client_id = $client->id;
            $cp->process_id = $client->process_id; // $request->input('process');
            $cp->step_id = Step::where('process_id',$client->process_id)->orderBy('order','asc')->first()->id;
            $cp->save();

            $extra = Forms::where('id',$request->input('crm'))->first();
            if($extra) {
                $sections = FormSection::with('form_section_inputs.input')->where('form_id', $extra->id)->get();

                foreach ($sections as $section) {
                    foreach ($section->form_section_inputs as $activity) {

                        if ($request->has($activity->id) && !is_null($request->input($activity->id))) {

                            switch ($activity->input_type) {
                                case 'App\FormInputBoolean':
                                    FormInputBooleanData::insert([
                                        'data' => $request->input($activity->id),
                                        'form_input_boolean_id' => $activity->input_id,
                                        'client_id' => $client->id,
                                        'user_id' => auth()->id(),
                                        'duration' => 120,
                                        'created_at' => now()
                                    ]);

                                    if($activity->mapped_field != null){
                                        //dd($activity);
                                        Client::where('id',$client->id)->update([$activity->mapped_field => $request->input($activity->id)]);
                                    }

                                    break;
                                case 'App\FormInputDate':
                                    FormInputDateData::insert([
                                        'data' => $request->input($activity->id),
                                        'form_input_date_id' => $activity->input_id,
                                        'client_id' => $client->id,
                                        'user_id' => auth()->id(),
                                        'duration' => 120,
                                        'created_at' => now()
                                    ]);

                                    if($activity->mapped_field != null){
                                        //dd($activity);
                                        Client::where('id',$client->id)->update([$activity->mapped_field => $request->input($activity->id)]);
                                    }

                                    break;
                                case 'App\FormInputText':
                                    FormInputTextData::insert([
                                        'data' => $request->input($activity->id),
                                        'form_input_text_id' => $activity->input_id,
                                        'client_id' => $client->id,
                                        'user_id' => auth()->id(),
                                        'duration' => 120,
                                        'created_at' => now()
                                    ]);

                                    if($activity->mapped_field != null){
                                        //dd($activity);
                                        Client::where('id',$client->id)->update([$activity->mapped_field => $request->input($activity->id)]);
                                    }


                                    break;
                                case 'App\FormInputPercentage':
                                    FormInputPercentageData::insert([
                                        'data' => $request->input($activity->id),
                                        'form_input_percentage_id' => $activity->input_id,
                                        'client_id' => $client->id,
                                        'user_id' => auth()->id(),
                                        'duration' => 120,
                                        'created_at' => now()
                                    ]);

                                    if($activity->mapped_field != null){
                                        //dd($activity);
                                        Client::where('id',$client->id)->update([$activity->mapped_field => $request->input($activity->id)]);
                                    }
                                    break;
                                case 'App\FormInputAmount':
                                    FormInputAmountData::insert([
                                        'data' => $request->input($activity->id),
                                        'form_input_amount_id' => $activity->input_id,
                                        'client_id' => $client->id,
                                        'user_id' => auth()->id(),
                                        'duration' => 120,
                                        'created_at' => now()
                                    ]);

                                    if($activity->mapped_field != null){
                                        //dd($activity);
                                        Client::where('id',$client->id)->update([$activity->mapped_field => $request->input($activity->id)]);
                                    }
                                    break;
                                case 'App\FormInputInteger':
                                    FormInputIntegerData::insert([
                                        'data' => $request->input($activity->id),
                                        'form_input_integer_id' => $activity->input_id,
                                        'client_id' => $client->id,
                                        'user_id' => auth()->id(),
                                        'duration' => 120,
                                        'created_at' => now()
                                    ]);

                                    if($activity->mapped_field != null){
                                        //dd($activity);
                                        Client::where('id',$client->id)->update([$activity->mapped_field => $request->input($activity->id)]);
                                    }
                                    break;
                                case 'App\FormInputTextarea':
                                    FormInputTextareaData::insert([
                                        'data' => $request->input($activity->id),
                                        'form_input_textarea_id' => $activity->input_id,
                                        'client_id' => $client->id,
                                        'user_id' => auth()->id(),
                                        'duration' => 120,
                                        'created_at' => now()
                                    ]);
                                    if($activity->mapped_field != null){
                                        //dd($activity);
                                        Client::where('id',$client->id)->update([$activity->mapped_field => $request->input($activity->id)]);
                                    }
                                    break;


                                case 'App\FormInputDropdown':
                                    foreach ($request->input($activity->id) as $key => $value) {
                                        FormInputDropdownData::insert([
                                            'form_input_dropdown_id' => $activity->input_id,
                                            'form_input_dropdown_item_id' => $value,
                                            'client_id' => $client->id,
                                            'user_id' => auth()->id(),
                                            'duration' => 120,
                                            'created_at' => now()
                                        ]);

                                        if($activity->mapped_field != null){
                                            //dd($activity);
                                            Client::where('id',$client->id)->update([$activity->mapped_field => $value]);
                                        }
                                    }
                                    break;
                                default:
                                    //todo capture defaults
                                    break;
                            }

                        }
                    }
                }
            }


        } catch (\Exception $e) {
            return response()->json([
                'success' => 0,
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }

        if(isset($request->object) && $request->object == 1){

            return $client->id;

        } else {

            return response()->json([
                'success' => 1,
                'message' => 'Executor successfully created.',
                'data' => [
                    'client_id' => $client->id
                ]
            ]);

        }
    }

    public function createClient(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:clients',
            'phone_number' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => 0,
                'message' => 'Validation failed',
                'data' => $validator->errors()
            ], 422);
        }

        try {

            $client = new Client;
            $client->company = $request->input('company');
            $client->company_registration_number = $request->input('company_reg');
            $client->first_name = $request->input('first_name');
            $client->last_name = $request->input('last_name');
            $client->initials = $request->input('initials');
            $client->known_as = $request->input('known_as');
            // $client->id_number = $request->input('id_number');
            $client->completed_at = $request->input('trigger_date');
            $client->email = $request->input('email');
            $client->contact = $request->input('phone_number');
            $client->contact_office = $request->input('contact_office');
            // $client->contact_role = $request->input('contact_role');
            $client->introducer_id = 1;
            $client->office_id = 1;
            $client->process_id = 1; // $request->input('process');
            $client->step_id = Step::where('process_id', $client->process_id)->orderBy('order','asc')->first()->id;
            $client->needs_approval = 0;
            $client->crm_id = 2; // $request->input('crm');
            if($request->has('parent_client') && $request->input('parent_client') != ''){
                $client->parent_id = $request->input('parent_client');
            }
            if(Auth::check() && Auth::user()->is('consultant')){
                $client->consultant_id = Auth::id();
                $client->assigned_date = now();
            }

            $client->save();

            Client::where('id',$client->id)->update([
                'hash_first_name' => DB::raw("AES_ENCRYPT('".addslashes($request->input('first_name'))."','Qwfe345dgfdg')"),
                'hash_last_name' => DB::raw("AES_ENCRYPT('".addslashes($request->input('last_name'))."','Qwfe345dgfdg')"),
                // 'hash_id_number' => DB::raw("AES_ENCRYPT('".addslashes($request->input('id_number'))."','Qwfe345dgfdg')"),
                'hash_email' => DB::raw("AES_ENCRYPT('".addslashes($request->input('company_email'))."','Qwfe345dgfdg')"),
                'hash_contact' => DB::raw("AES_ENCRYPT('".addslashes($request->input('contact'))."','Qwfe345dgfdg')")
            ]);

            // Call this to autopopulate from CBP, just comment out if you would like to manuaaly fill the forms
            /*if($client->id_number != '') {
                $this->autoPopulateFromCBP($client->id);
            }*/

            $cp = new ClientProcess();
            $cp->client_id = $client->id;
            $cp->process_id = $client->process_id; // $request->input('process');
            $cp->step_id = Step::where('process_id',$client->process_id)->orderBy('order','asc')->first()->id;
            $cp->save();

            $extra = Forms::where('id',$request->input('crm'))->first();
            if($extra) {
                $sections = FormSection::with('form_section_inputs.input')->where('form_id', $extra->id)->get();

                foreach ($sections as $section) {
                    foreach ($section->form_section_inputs as $activity) {

                        if ($request->has($activity->id) && !is_null($request->input($activity->id))) {

                            switch ($activity->input_type) {
                                case 'App\FormInputBoolean':
                                    FormInputBooleanData::insert([
                                        'data' => $request->input($activity->id),
                                        'form_input_boolean_id' => $activity->input_id,
                                        'client_id' => $client->id,
                                        'user_id' => auth()->id(),
                                        'duration' => 120,
                                        'created_at' => now()
                                    ]);

                                    if($activity->mapped_field != null){
                                        //dd($activity);
                                        Client::where('id',$client->id)->update([$activity->mapped_field => $request->input($activity->id)]);
                                    }

                                    break;
                                case 'App\FormInputDate':
                                    FormInputDateData::insert([
                                        'data' => $request->input($activity->id),
                                        'form_input_date_id' => $activity->input_id,
                                        'client_id' => $client->id,
                                        'user_id' => auth()->id(),
                                        'duration' => 120,
                                        'created_at' => now()
                                    ]);

                                    if($activity->mapped_field != null){
                                        //dd($activity);
                                        Client::where('id',$client->id)->update([$activity->mapped_field => $request->input($activity->id)]);
                                    }

                                    break;
                                case 'App\FormInputText':
                                    FormInputTextData::insert([
                                        'data' => $request->input($activity->id),
                                        'form_input_text_id' => $activity->input_id,
                                        'client_id' => $client->id,
                                        'user_id' => auth()->id(),
                                        'duration' => 120,
                                        'created_at' => now()
                                    ]);

                                    if($activity->mapped_field != null){
                                        //dd($activity);
                                        Client::where('id',$client->id)->update([$activity->mapped_field => $request->input($activity->id)]);
                                    }


                                    break;
                                case 'App\FormInputPercentage':
                                    FormInputPercentageData::insert([
                                        'data' => $request->input($activity->id),
                                        'form_input_percentage_id' => $activity->input_id,
                                        'client_id' => $client->id,
                                        'user_id' => auth()->id(),
                                        'duration' => 120,
                                        'created_at' => now()
                                    ]);

                                    if($activity->mapped_field != null){
                                        //dd($activity);
                                        Client::where('id',$client->id)->update([$activity->mapped_field => $request->input($activity->id)]);
                                    }
                                    break;
                                case 'App\FormInputAmount':
                                    FormInputAmountData::insert([
                                        'data' => $request->input($activity->id),
                                        'form_input_amount_id' => $activity->input_id,
                                        'client_id' => $client->id,
                                        'user_id' => auth()->id(),
                                        'duration' => 120,
                                        'created_at' => now()
                                    ]);

                                    if($activity->mapped_field != null){
                                        //dd($activity);
                                        Client::where('id',$client->id)->update([$activity->mapped_field => $request->input($activity->id)]);
                                    }
                                    break;
                                case 'App\FormInputInteger':
                                    FormInputIntegerData::insert([
                                        'data' => $request->input($activity->id),
                                        'form_input_integer_id' => $activity->input_id,
                                        'client_id' => $client->id,
                                        'user_id' => auth()->id(),
                                        'duration' => 120,
                                        'created_at' => now()
                                    ]);

                                    if($activity->mapped_field != null){
                                        //dd($activity);
                                        Client::where('id',$client->id)->update([$activity->mapped_field => $request->input($activity->id)]);
                                    }
                                    break;
                                case 'App\FormInputTextarea':
                                    FormInputTextareaData::insert([
                                        'data' => $request->input($activity->id),
                                        'form_input_textarea_id' => $activity->input_id,
                                        'client_id' => $client->id,
                                        'user_id' => auth()->id(),
                                        'duration' => 120,
                                        'created_at' => now()
                                    ]);
                                    if($activity->mapped_field != null){
                                        //dd($activity);
                                        Client::where('id',$client->id)->update([$activity->mapped_field => $request->input($activity->id)]);
                                    }
                                    break;


                                case 'App\FormInputDropdown':
                                    foreach ($request->input($activity->id) as $key => $value) {
                                        FormInputDropdownData::insert([
                                            'form_input_dropdown_id' => $activity->input_id,
                                            'form_input_dropdown_item_id' => $value,
                                            'client_id' => $client->id,
                                            'user_id' => auth()->id(),
                                            'duration' => 120,
                                            'created_at' => now()
                                        ]);

                                        if($activity->mapped_field != null){
                                            //dd($activity);
                                            Client::where('id',$client->id)->update([$activity->mapped_field => $value]);
                                        }
                                    }
                                    break;
                                default:
                                    //todo capture defaults
                                    break;
                            }

                        }
                    }
                }
            }


        } catch (\Exception $e) {
            return response()->json([
                'success' => 0,
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }

        if(isset($request->object) && $request->object == 1){

            return $client->id;

        } else {

            return response()->json([
                'success' => 1,
                'message' => 'Client successfully created.',
                'data' => [
                    'client_id' => $client->id
                ]
            ]);

        }
    }

    public function getClient(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => 0,
                'message' => 'Validation failed',
                'data' => $validator->errors()
            ], 422);
        }

        $client = Client::select('id', 'first_name', 'last_name', 'email', 'contact as phone_number')->where('email', $request->email)->first();

        return response()->json([
            'success' => 1,
            'message' => 'Client successfully retrieved.',
            'data' => [
                $client
            ]
        ]);
    }

    public function getNewProcesses(Request $request)
    {
        $client = Client::find($request->client_id);

        $processes_drop_down = $client->startNewProcessDropdown();
        $processes = [];
        foreach ($processes_drop_down as $main_key => $process_drop_down)
        {
            $process_group = ProcessGroup::where('name', $main_key)->first();
            foreach ($process_drop_down as $process)
            {
                $processes[] = [
                  'WorkflowID' => $process_group->id,
                  'WorkflowName' => $main_key,
                  'ProcessID' => $process['id'],
                  'ProcessName' => $process['name']
                ];
            }
        }

        return response()->json(['success' => 1, 'message' => 'Processes retrieved successfully.', 'data' => $processes]);
    }

    public function startProcess(Request $request)
    {
        $client = Client::find($request->client_id);

        $process_first_step = Step::where('process_id', $request->process_id)->orderBy('order','asc')->first();

        $client->process_id = $request->process_id;
        $client->step_id = $process_first_step->id;
        $client->save();

        $client_process = new ClientProcess();
        $client_process->client_id = $client->id;
        $client_process->process_id = $request->process_id;
        $client_process->step_id = $process_first_step->id;
        $client_process->active = 1;
        $client_process->save();

        return response()->json(['success' => 1, 'message' => 'Process started successfully.', 'data' => ['step_id' => $process_first_step->id]]);
    }

    public function getAllProcesses()
    {
        $client = new Client();

        $processes_drop_down = $client->startNewProcessDropdown();
        $processes = [];
        foreach ($processes_drop_down as $main_key => $process_drop_down)
        {
            $process_group = ProcessGroup::where('name', $main_key)->first();
            foreach ($process_drop_down as $process)
            {
                $processes[] = [
                    'WorkflowID' => $process_group->id,
                    'WorkflowName' => $main_key,
                    'ProcessID' => $process['id'],
                    'ProcessName' => $process['name']
                ];
            }
        }

        return response()->json(['success' => 1, 'message' => 'Processes retrieved successfully.', 'data' => $processes]);
    }

    public function createTrigger(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trigger_name' => 'required|string',
            'trigger_date' => 'required|date_format:Y-m-d',
            'process_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => 0,
                'message' => 'Validation failed',
                'data' => $validator->errors()
            ], 422);
        }

        $request->object = 1; // Set this to return an array

        $client_id = $this->create($request);

        // return $client_id;

        $client = Client::find($client_id);

        $process_first_step = Step::where('process_id', $request->process_id)->orderBy('order','asc')->first();
        // return $client_id;
        $client->process_id = $request->process_id;
        $client->step_id = $process_first_step->id;
        $client->save();

        $client_process = new ClientProcess();
        $client_process->client_id = $client->id;
        $client_process->process_id = $request->process_id;
        $client_process->step_id = $process_first_step->id;
        $client_process->active = 1;
        $client_process->save();

        return response()->json(['success' => 1, 'message' => 'Process started successfully.', 'data' => ['step_id' => $process_first_step->id]]);
    }
}
