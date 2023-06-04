<?php

namespace App\Http\Controllers;

use App\Actions;
use App\ActionsAssigned;
use App\Activity;
use App\Client;
use App\OfficeUser;
use App\Process;
use App\ProcessArea;
use App\Step;
use App\User;
use App\UserNotification;
use Illuminate\Http\Request;
use App\Config;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\ActionActivities;
use App\Http\Requests\StoreActionRequest;
use App\Notification;
use App\Events\NotificationEvent;

class ActionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {

        $request->session()->forget('actions_activities');

        $actions = Actions::with('activity.name')->whereNotNull('activities')->get();
        //dd($actions);

        $action_data = $actions->map(function ($action){
            $activity_name = Activity::select('name')
                ->whereIn('id', explode(',', $action->activities))
                ->get()->map(function ($activity){
                    return $activity->name;
                });

            return [
                'id' => $action->id,
                'name' => $action->name,
                'description' => $action->description,
                'activity' => $activity_name,
                'due_date' => $action->due_date,
                'created_by' =>  User::select('id',DB::raw("CONCAT(CAST(AES_DECRYPT(`hash_first_name`, 'Qwfe345dgfdg') AS CHAR(50)),' ',CAST(AES_DECRYPT(`hash_last_name`, 'Qwfe345dgfdg') AS CHAR(50))) as full_name"))->where('id',$action->created_by)->pluck('full_name','id'),
                'status' => $action->status
            ];
        });

        return view('actions.index')->with(['actions' => $action_data]);
    }

    public function create(){

        $clients = Client::all();

        $clients_data = Client::all();

        foreach ($clients_data as $client){
            $clients[$client->id] = ($client->company != null ? $client->company : $client->first_name.' '.$client->last_name);
        }

        //Processes Dropdown
        $offices = OfficeUser::select('office_id')->where('user_id',Auth::id())->get();

        $office_processes = ProcessArea::select('process_id')->whereIn('office_id',collect($offices)->toArray())->get();

        $processes = [];

        $cps = Process::with('pgroup','steps2')->whereIn('id',collect($office_processes)->toArray())->orWhere('global',1)->where('process_type_id',1)->whereHas('steps2')->get();


        //dd($cps);
        foreach($cps as $cp){
            if(isset($cp->pgroup)) {
                $processes[$cp->pgroup->name][] = [
                    "id" => $cp->id,
                    "name" => $cp->name
                ];
            } else {
                $processes['None'][] = [
                    "id" => $cp->id,
                    "name" => $cp->name
                ];
            }
        }

        ksort($processes);

        $processes += array_splice($processes,array_search('None',array_keys($processes)),1);

        $parameters = [
            'process' => $processes,
            'config' => Config::first(),
            'recipients' => User::select('id',DB::raw("CONCAT(first_name,' ',last_name) as full_name"))->pluck('full_name','id'),
            'clients' => $clients
        ];

        return view('actions.create')->with($parameters);
    }

    public function store(Request $request){
        $action = new Actions();
        $action->name = $request->input('action_name');
        $action->description = $request->input('action_description');
        $action->created_by = Auth::id();
        $action->process_id = $request->input("actions_process");
        $action->step_id = $request->input("action_step");
        $action->users = implode(',',$request->input('action_recipients'));

        /*returns the same results as above line
        $action->users = '';
        if($request->has('save_action_recipients')) {
            foreach (explode(',',$request->input('save_action_recipients')) as $recipients):
                if ($action->users == '')
                    $action->users = $recipients;
                else
                    $action->users .= "," . $recipients;
            endforeach;
        }*/

        $action->activities = implode(',', $request->session()->get('actions_activities'));

        /*returns the same results as above
        $action->activities = '';
        if($request->session()->has('actions_activities')) {
            foreach ($request->session()->get('actions_activities') as $clients):
                if ($action->activities == '')
                    $action->activities = $clients;
                else
                    $action->activities .= "," . $clients;
            endforeach;
        }*/


        $action->save();

//        foreach(explode(',',$request->input('save_action_recipients')) as $recipients) {
//
//            if($request->has('save_action_clients')){
//                foreach(explode(',',$request->input('save_action_clients')) as $clients2):
//
//                    foreach ($request->session()->get('actions_activities') as $activities):
//                        $activity = new ActionActivities();
//                        $activity->action_id = $action->id;
//                        $activity->client_id = $clients2;
//                        $activity->process_id = $request->input('save_action_process');
//                        $activity->activity_id = $activities;
//                        $activity->user_id = $recipients;
//                        $activity->save();
//                    endforeach;
//
//                endforeach;
//            }
//        }

        return redirect(route('action.index'))->with('flash_success', 'Action successfully saved.');

    }

    /**
     * ????? I need some explanations
     */

    public function assignActivityToUser(Request $request){
        $action = new ActionsAssigned();
        $action->created_by = Auth::id();
        $action->due_date = $request->input('due_date');
        $action->process_id = $request->input("process_id");
        $action->step_id = $request->input("step_id");

        $action->clients = $request->input('client_id');

        $action->users = '';
        if($request->has('user_ids')) {
            foreach ($request->input('user_ids') as $recipients):
                if ($action->users == '')
                    $action->users = $recipients;
                else
                    $action->users .= "," . $recipients;
            endforeach;
        }

        $action->activities = $request->input('activity_id');
        $action->save();

        foreach($request->input('user_ids') as $recipients) {

            $signature_processes_array = [];
            $signature_processes = Process::where('form',1)->get();

            foreach ($signature_processes as $signature_process){
                array_push($signature_processes_array,$signature_process->process_id);
            }

            $client_name = Client::find($request->input('client_id'));
            $step_name = Step::find($request->input('step_id'));
            $notification = new Notification;
            $notification->name = 'This is a '.$step_name->name .' action : '.(isset($client_name->company) && $client_name->company != null ? $client_name->company : $client_name->first_name.' '.$client_name->last_name);
            $notification->link = route('clients.stepprogressaction', [$request->input('client_id'),$request->input("process_id"),$request->input('step_id'),$action->id,(in_array($request->input('process_id'),$signature_processes_array,true) ? 1 : 0)]);
            $notification->type = 1;
            //$notification->link = route('clients.progress', $client).'/1';
            $notification->save();

            $user_notifications = [];
            array_push($user_notifications, [
                'user_id' => $recipients,
                'notification_id' => $notification->id
            ]);

            NotificationEvent::dispatch($recipients, $notification);

            UserNotification::insert($user_notifications);

                $activity = new ActionActivities();
                $activity->action_id = $action->id;
                $activity->client_id = $request->input('client_id');
                $activity->process_id = $request->input('process_id');
                $activity->step_id = $request->input('step_id');
                $activity->activity_id = $request->input('activity_id');
                $activity->user_id = $recipients;
                $activity->save();
        }
    }

    /**
     * ????? I need some explanations
     */

    public function storeClientAction(Request $request,$client_id){

        $signature_processes_array = [];
        $signature_processes = Process::where('form',1)->get();

        foreach ($signature_processes as $signature_process){
            array_push($signature_processes_array,$signature_process->process_id);
        }

        $action = new ActionsAssigned();
        $action->name = $request->input('save_action_name');
        $action->description = $request->input('save_action_description');
        $action->created_by = Auth::id();
        $action->due_date = $request->input('save_action_due_date');
        $action->process_id = $request->input("save_action_process");
        $action->step_id = $request->input("save_action_step");

        $action->clients = $client_id;

        $action->users = '';
        if($request->has('save_action_recipients')) {
            foreach (explode(',',$request->input('save_action_recipients')) as $recipients):
                if ($action->users == '')
                    $action->users = $recipients;
                else
                    $action->users .= "," . $recipients;
            endforeach;
        }

        $action->activities = '';
        if($request->session()->has('actions_activities')) {
            foreach ($request->session()->get('actions_activities') as $clients):
                if ($action->activities == '')
                    $action->activities = $clients;
                else
                    $action->activities .= "," . $clients;
            endforeach;
        }

        $action->save();

        foreach(explode(',',$request->input('save_action_recipients')) as $recipients) {

                    $client_name = Client::find($client_id);
                    $notification = new Notification;
                    $notification->name = $request->input('save_action_description') . ' : ' . (isset($client_name->company) && $client_name->company != null ? $client_name->company : $client_name->first_name.' '.$client_name->last_name);
            $notification->link = route('clients.stepprogressaction', [$client_name->id,$action->process_id,$action->step_id,$action->id,(in_array($action->process_id,$signature_processes_array,true) ? 1 : 0)]);
                    $notification->type = 1;
                    //$notification->link = route('clients.progress', $client).'/1';
                    $notification->save();

                    $user_notifications = [];
                    array_push($user_notifications, [
                        'user_id' => $recipients,
                        'notification_id' => $notification->id
                    ]);

                    NotificationEvent::dispatch($recipients, $notification);

                    UserNotification::insert($user_notifications);


                    foreach ($request->session()->get('actions_activities') as $activities):
                        $activity = new ActionActivities();
                        $activity->action_id = $action->id;
                        $activity->client_id = $client_id;
                        $activity->process_id = $request->input('save_action_process');
                        $activity->step_id = $request->input('save_action_step');
                        $activity->activity_id = $activities;
                        $activity->user_id = $recipients;
                        $activity->save();
                    endforeach;


        }
        $request->session()->put('flash_success','Action successfully sent.');

        return redirect(route('clients.actions',[$client_id,$client_name->process_id,$client_name->step,0]));

    }

    public function edit(Request $request, $action_id){
        $action = Actions::whereNotNull('activities')->where('id', $action_id)->first();

        $request->session()->put('actions_activities', explode(",", $action->activities));

        $parameters = [
            'action' => $action,
            'process' => Process::all()->pluck('name','id')->prepend('Please Select','0'),
            'steps' => Step::all()->pluck('name','id')->prepend('Please Select','0'),
            'recipients' => User::select(DB::raw("CONCAT(first_name,' ',COALESCE(`last_name`,'')) AS full_name"), 'id')->orderBy('first_name')->orderBy('last_name')->pluck('full_name', 'id'),
        ];

        return view('actions.edit')->with($parameters);
    }

    public function update(Request $request, $action_id){
        $action = Actions::find($action_id);
        $action->name = $request->input('action_name');
        $action->description = $request->input('action_description');

        /*$action->users = '';
        if($request->has('save_action_recipients')) {
            foreach (explode(',',$request->input('save_action_recipients')) as $recipients):
                if ($action->users == '')
                    $action->users = $recipients;
                else
                    $action->users .= "," . $recipients;
            endforeach;
        }*/

        $action->users = implode(',',$request->input('action_recipients'));
        /*$action->activities = '';
        if($request->session()->has('actions_activities')) {
            foreach ($request->session()->get('actions_activities') as $clients):
                if ($action->activities == '')
                    $action->activities = $clients;
                else
                    $action->activities .= "," . $clients;
            endforeach;
        }*/

        $action->activities = implode(',', $request->session()->get('actions_activities'));

        $action->save();


        $request->session()->forget('actions_activities');

        return redirect(route('action.index'))->with('flash_success', 'Action successfully updated');
    }

    public function activate($id){
        $action = Actions::find($id);
        $action->status = 1;
        $action->save();

        return redirect(route('action.index'))->with('flash_success', 'Action activated successfully');
    }

    public function deactivate($id){
        $action = Actions::find($id);
        $action->status = 0;
        $action->save();

        return redirect(route('action.index'))->with('flash_success', 'Action deactivated successfully');
    }

    public function getProcessSteps($process_id){
        /* $steps = Step::where('process_id',$process_id)->orderBy('order','asc')->get();

        $step_data = array();

        foreach($steps as $step){
            array_push($step_data,[
               'id' => $step->id,
               'name' => $step->name
            ]);
        }*/

        $step_data = Step::where('process_id',$process_id)
            ->orderBy('order','asc')
            ->get()->map(function ($step){
                return [
                    'id' => $step->id,
                    'name' => $step->name
                ];
            });

        return response()->json($step_data);
    }

    public function getActionActivity(Request $request,$process_id,$step_id)
    {
        $request->session()->forget('actions_activities');

        $steps = Activity::where('step_id',$step_id)->get();
//dd($activities);
        $process_activities = array();
        //$selected = array();
        //dd($selected_steps);
        foreach($steps as $step){

                if($request->session()->has('actions_activities')) {
                    $activities = $request->session()->get('actions_activities');

                    if(in_array($step->id,$activities)) {
                        array_push($process_activities, [
                            'id' => $step->id,
                            'name' => $step->name,
                            'selected' => '1'
                        ]);
                    } else {
                        array_push($process_activities, [
                            'id' => $step->id,
                            'name' => $step->name,
                            'selected' => '0'
                        ]);
                    }
                } else {
                    array_push($process_activities, [
                        'id' => $step->id,
                        'name' => $step->name,
                        'selected' => '0'
                    ]);
                }

        }

        return response()->json($process_activities);
    }

    public function getSelectedActionActivity(Request $request,$process_id,$step_id)
    {
        //$request->session()->forget('actions_activities');

        return $request->all();

        $steps = Step::with(['activities' => function ($query) use ($step_id) {
            $query->where('step_id', $step_id);
        }])->where('process_id',$process_id)->get();
//dd($activities);
        $process_activities = array();
        //$selected = array();
        //dd($selected_steps);
        foreach($steps as $step){
            foreach($step["activities"] as $activity) {
                if($request->session()->has('actions_activities')) {
                    $activities = $request->session()->get('actions_activities');

                    if(in_array($activity->id,$activities)) {
                        array_push($process_activities, [
                            'id' => $activity->id,
                            'name' => $activity->name,
                            'selected' => '1'
                        ]);
                    } else {
                        array_push($process_activities, [
                            'id' => $activity->id,
                            'name' => $activity->name,
                            'selected' => '0'
                        ]);
                    }
                } else {
                    array_push($process_activities, [
                        'id' => $activity->id,
                        'name' => $activity->name,
                        'selected' => '0'
                    ]);
                }
            }
        }

        return response()->json($process_activities);
    }

    public function getSelectedAddActionActivity(Request $request,$process_id,$step_id)
    {
        //$request->session()->forget('actions_activities');

        $steps = Step::with(['activities' => function ($query) use ($step_id) {
            $query->where('step_id', $step_id);
        }])->where('process_id',$process_id)->get();
//dd($activities);
        $process_activities = array();
        //$selected = array();
        //dd($selected_steps);
        foreach($steps as $step){
            foreach($step["activities"] as $activity) {
                if($request->session()->has('addactions_activities')) {
                    $activities = $request->session()->get('addactions_activities');

                    if(in_array($activity->id,$activities)) {
                        array_push($process_activities, [
                            'id' => $activity->id,
                            'name' => $activity->name,
                            'selected' => '1'
                        ]);
                    } else {
                        array_push($process_activities, [
                            'id' => $activity->id,
                            'name' => $activity->name,
                            'selected' => '0'
                        ]);
                    }
                } else {
                    array_push($process_activities, [
                        'id' => $activity->id,
                        'name' => $activity->name,
                        'selected' => '0'
                    ]);
                }
            }
        }

        return response()->json($process_activities);
    }

    public function clearAddActionActivity(Request $request){
        if($request->session()->has('addactions_activities')) {
            $request->session()->forget('addactions_activities');
        }

        return response()->json('success');
    }

    public function setAddActionActivity(Request $request,$activityid){
        if($request->session()->has('addactions_activities')) {
            $request->session()->forget('addactions_activities');
        }
        $activities = array();
        array_push($activities,$activityid);
        $request->session()->put('addactions_activities',$activities);

        return response()->json('success');
    }

    public function getEditActionActivity(Request $request,$process_id,$step_id,$action_id)
    {
        $request->session()->forget('actions_activities');

        $steps = Step::with(['activities' => function ($query) use ($step_id){
            $query->where('step_id',$step_id);
        }])->where('process_id',$process_id)->get();
//dd($activities);
        $process_activities = array();
        //$selected = array();
        //dd($selected_steps);
        foreach($steps as $step){
            foreach($step["activities"] as $activity) {
                /*if($request->session()->has('actions_activities')) {*/
                $action = Actions::find($action_id);

                if(in_array($activity->id,explode(',',$action->activities))) {


                    if($request->session()->has('actions_activities')) {
                        $activities1 = $request->session()->get('actions_activities');

                        if(in_array($activity->id,$activities1)){
                            $posted_activity[0] = $activity->id;
                            $activities1 = array_diff($activities1, $posted_activity);
                        } else {
                            array_push($activities1,$activity->id);
                        }
                        $activities = array_values($activities1);
                        $request->session()->put('actions_activities', $activities);
                    } else {
                        $activities = array();
                        array_push($activities,$activity->id);
                        $request->session()->put('actions_activities', $activities);
                    }
                    array_push($process_activities, [
                        'id' => $activity->id,
                        'name' => $activity->name,
                        'selected' => '1'
                    ]);
                } else {
                    array_push($process_activities, [
                        'id' => $activity->id,
                        'name' => $activity->name,
                        'selected' => '0'
                    ]);
                }
            }
        }

        return response()->json($process_activities);
    }

    public function getActionActivities(Request $request,$process_id,$step_id,$action_id)
    {
        $request->session()->forget('actions_activities');

        $steps = Step::with(['activities' => function ($query) use ($step_id){
            $query->where('step_id',$step_id);
        }])->where('process_id',$process_id)->get();
//dd($activities);
        $process_activities = array();
        //$selected = array();
        //dd($selected_steps);
        foreach($steps as $step){
            foreach($step["activities"] as $activity) {
                /*if($request->session()->has('actions_activities')) {*/
                $action = Actions::find($action_id);

                if(in_array($activity->id,explode(',',$action->activities))) {


                    if($request->session()->has('actions_activities')) {
                        $activities1 = $request->session()->get('actions_activities');

                        if(in_array($activity->id,$activities1)){
                            $posted_activity[0] = $activity->id;
                            $activities1 = array_diff($activities1, $posted_activity);
                        } else {
                            array_push($activities1,$activity->id);
                        }
                        $activities = array_values($activities1);
                        $request->session()->put('actions_activities', $activities);
                    } else {
                        $activities = array();
                        array_push($activities,$activity->id);
                        $request->session()->put('actions_activities', $activities);
                    }
                    array_push($process_activities, [
                        'id' => $activity->id,
                        'name' => $activity->name,
                        'selected' => '1'
                    ]);
                } else {
                    array_push($process_activities, [
                        'id' => $activity->id,
                        'name' => $activity->name,
                        'selected' => '0'
                    ]);
                }
            }
        }

        return response()->json($process_activities);
    }

    public function searchAction(Request $request)
    {
        $action_id = $request->input('search');

        if($action_id != null) {
            $actions = Actions::where('id',$request->input('search'))->get();
        }

        $action_array = array();
        $recipients_array = array();
        $step_array = array();
        $process_array = array();

        foreach($actions as $action){


            $users = User::all();

            foreach ($users as $user) {
                if(in_array($user->id,explode(',',$action->users))){
                    array_push($recipients_array, [
                        'id' => $user->id,
                        'name' => $user->first_name . ' ' . $user->last_name,
                        'selected' => '1'
                    ]);
                } else {
                    array_push($recipients_array, [
                        'id' => $user->id,
                        'name' => $user->first_name . ' ' . $user->last_name,
                        'selected' => '0'
                    ]);
                };
            }

            $processes = Process::all();

            foreach ($processes as $process) {
                if($process->id = $action->process_id){
                    array_push($process_array, [
                        'id' => $process->id,
                        'name' => $process->name,
                        'selected' => '1'
                    ]);
                } else {
                    array_push($process_array, [
                        'id' => $process->id,
                        'name' => $process->name,
                        'selected' => '0'
                    ]);
                };
            }

            $steps = Step::all();

            foreach ($steps as $step) {
                if($step->id == $action->step_id){
                    array_push($step_array, [
                        'id' => $step->id,
                        'name' => $step->name,
                        'selected' => '1'
                    ]);
                } else {
                    array_push($step_array, [
                        'id' => $step->id,
                        'name' => $step->name,
                        'selected' => '0'
                    ]);
                };
            }

            array_push($action_array, [
                'id' => $action->id,
                'name' => $action->name,
                'description' => $action->description,
                'recipients' => $recipients_array,
                'process_id' => $process_array,
                'step_id' => $step_array
            ]);

        }

        return response()->json($action_array);
    }

    public function searchActionActivity(Request $request,$process_id,$step_id,$search)
    {
        if($search != null) {
            $steps = Step::with(['activities' => function ($query) use ($search,$step_id) {
                $query->where('step_id',$step_id);
                $query->where('name', 'like', '%' . $search . '%');
            }])->where('process_id', $process_id)->get();
        } else {
            $steps = Step::with(['activities'=>function ($query) use ($step_id){
                $query->where('step_id',$step_id);
            }])->where('process_id',$process_id)->get();
        }

        $process_activities = array();

        foreach($steps as $step){
            foreach($step["activities"] as $activity) {
                if($request->session()->has('actions_activities')) {
                    $activities = $request->session()->get('actions_activities');

                    if(in_array($activity->id,$activities)) {
                        array_push($process_activities, [
                            'id' => $activity->id,
                            'name' => $activity->name,
                            'selected' => '1'
                        ]);
                    } else {
                        array_push($process_activities, [
                            'id' => $activity->id,
                            'name' => $activity->name,
                            'selected' => '0'
                        ]);
                    }
                } else {
                    array_push($process_activities, [
                        'id' => $activity->id,
                        'name' => $activity->name,
                        'selected' => '0'
                    ]);
                }
            }
        }

        return response()->json($process_activities);
    }

    public function searchActionActivities(Request $request,$process_id,$step_id,$search)
    {
        /*$activity_name = $request->input('search');
        $step_id = $request->input('step_id');
        dd($step_id);*/

        if($search != null) {
            $steps = Step::with(['activities' => function ($query) use ($search,$step_id) {
                $query->where('step_id',$step_id);
                $query->where('name', 'like', '%' . $search . '%');
            }])->where('process_id', $process_id)->get();
        } else {
            $steps = Step::with(['activities'=>function ($query) use ($step_id){
                $query->where('step_id',$step_id);
            }])->where('process_id',$process_id)->get();
        }

        $process_activities = array();

        foreach($steps as $step){
            foreach($step["activities"] as $activity) {
                if($request->session()->has('actions_activities')) {
                    $activities = $request->session()->get('actions_activities');

                    if(in_array($activity->id,$activities)) {
                        array_push($process_activities, [
                            'id' => $activity->id,
                            'name' => $activity->name,
                            'selected' => '1'
                        ]);
                    } else {
                        array_push($process_activities, [
                            'id' => $activity->id,
                            'name' => $activity->name,
                            'selected' => '0'
                        ]);
                    }
                } else {
                    array_push($process_activities, [
                        'id' => $activity->id,
                        'name' => $activity->name,
                        'selected' => '0'
                    ]);
                }
            }
        }

        return response()->json($process_activities);
    }

    public function searchAddActionActivities(Request $request,$process_id,$step_id,$search)
    {
        /*$activity_name = $request->input('search');
        $step_id = $request->input('step_id');
        dd($step_id);*/

        if($search != null) {
            $steps = Step::with(['activities' => function ($query) use ($search,$step_id) {
                $query->where('step_id',$step_id);
                $query->where('name', 'like', '%' . $search . '%');
            }])->where('process_id', $process_id)->get();
        } else {
            $steps = Step::with(['activities'=>function ($query) use ($step_id){
                $query->where('step_id',$step_id);
            }])->where('process_id',$process_id)->get();
        }

        $process_activities = array();

        foreach($steps as $step){
            foreach($step["activities"] as $activity) {
                if($request->session()->has('addactions_activities')) {
                    $activities = $request->session()->get('addactions_activities');

                    if(in_array($activity->id,$activities)) {
                        array_push($process_activities, [
                            'id' => $activity->id,
                            'name' => $activity->name,
                            'selected' => '1'
                        ]);
                    } else {
                        array_push($process_activities, [
                            'id' => $activity->id,
                            'name' => $activity->name,
                            'selected' => '0'
                        ]);
                    }
                } else {
                    array_push($process_activities, [
                        'id' => $activity->id,
                        'name' => $activity->name,
                        'selected' => '0'
                    ]);
                }
            }
        }

        return response()->json($process_activities);
    }

    public function storeActionActivity(Request $request,$activity_id){

        if($request->session()->has('actions_activities')) {
            $activities = $request->session()->get('actions_activities');

            if(in_array($activity_id,$activities)){
                $posted_activity[0] = $activity_id;
                $activities = array_diff($activities, $posted_activity);
            } else {
                array_push($activities,$activity_id);
            }
            $activities = array_values($activities);
            $request->session()->put('actions_activities', $activities);
        } else {
            $activities = array();
            array_push($activities,$activity_id);
            $request->session()->put('actions_activities', $activities);
        }

        return response()->json('sucdcess');

    }

    public function storeAddActionActivity(Request $request,$activity_id){

        if($request->session()->has('addactions_activities')) {
            $activities = $request->session()->get('addactions_activities');

            if(in_array($activity_id,$activities)){
                $posted_activity[0] = $activity_id;
                $activities = array_diff($activities, $posted_activity);
            } else {
                array_push($activities,$activity_id);
            }
            $activities = array_values($activities);
            $request->session()->put('addactions_activities', $activities);
        } else {
            $activities = array();
            array_push($activities,$activity_id);
            $request->session()->put('addactions_activities', $activities);
        }

        return response()->json('success');

    }

    public function completeAssignedAction(Request $request,$clientid,$activityid){
        //ActionsAssigned::destroy($assignedactionid);
        DB::table('actions_activities')
            ->where("actions_activities.client_id", '=',  $clientid)
            ->where("actions_activities.activity_id", '=',  $activityid)
            ->update(['actions_activities.status'=> '1']);

        return redirect()->back()->with(['flash_success' => "Assigned Action Completed."]);
    }

    public function deleteAssignedAction(Request $request,$clientid,$activityid){
        //ActionsAssigned::destroy($assignedactionid);
        ActionActivities::where('client_id',$clientid)->where('activity_id',$activityid)->delete();

        return redirect()->back()->with(['flash_success' => "Assigned Action Deleted."]);
    }
}
