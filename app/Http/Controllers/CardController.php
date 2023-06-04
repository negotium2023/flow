<?php

namespace App\Http\Controllers;

use App\Board;
use App\Card;
use App\Client;
use App\OfficeUser;
use App\PriorityStatus;
use App\Section;
use App\Status;
use App\Task;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CardController extends Controller
{
    public function __construct()
    {
        return $this->middleware('auth');
    }

    public function index()
    {
        //
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //$team_names = implode(', ', $request->card_form["team_names"] ?? []);

        if((isset($request->card_form["saved"]) && $request->card_form["saved"] != 0) && (isset($request->saved) && $request->saved != 0)){

            $assignee_id = ($request->card_form["assignee_name"] !== '' ? User::select('id', DB::raw('CONCAT(first_name," ", last_name) AS full_name'))->where(DB::raw('CONCAT(first_name," ", last_name)'), $request->card_form["assignee_name"])->first()->id : null);
            $client_id = ($request->client_name != '' ? Client::select('id', DB::raw('CONCAT(first_name," ", last_name) AS full_name'))->where(DB::raw('CONCAT(first_name," ", last_name)'), $request->client_name)->first()->id : null);
            $card = Card::find($request->card_form["saved"]);
            $card->name = $request->card_name;
            $card->insurer = (isset($request->card_form["insurer"]) ? $request->card_form["insurer"] : '');
            $card->policy = (isset($request->card_form["policy"]) ? $request->card_form["policy"] : '');
            $card->upfront_revenue = (isset($request->card_form["upfront_revenue"]) ? $request->card_form["upfront_revenue"] : '');
            $card->ongoing_revenue = (isset($request->card_form["ongoing_revenue"]) ? $request->card_form["ongoing_revenue"] : '');
            $card->dependency_id = (isset($request->card_form["dependency_id"]) ? $request->card_form["dependency_id"] : null);
            $card->due_date = (isset($request->card_form["selected_deadline"]) ? Carbon::parse($request->card_form["selected_deadline"])->toDateString() : Carbon::parse($request->card_form["due_date"])->toDateString());
            $card->assignee_id = $assignee_id;
            $card->assignee_name = $request->card_form["assignee_name"];
            $card->team_names = (is_array($request->card_form["team_names"]) ? implode(', ', $request->card_form["team_names"]) : $request->card_form["team_names"]);
            $card->status_id = $request->card_form["progress_status_id"] ?? 1;
            $card->priority_id = $request->priority_id ?? (isset($request->card_form["priority_status_id"]) ? $request->card_form["priority_status_id"] : 1);
            $card->section_id = $request->section["section_id"];
            $card->summary_description = (isset($request->card_form["summary_description"]) ? $request->card_form["summary_description"] : '');
            $card->description = (isset($request->card_form["description"]) ? $request->card_form["description"] : '');
            $card->client_id = $client_id;
            $card->client_name = $request->client_name;
            $card->creator_id = Auth::id();
            $card->enabled = 1;
            $card->save();

            if (!empty($request->task)) {
                foreach ($request->task as $task) {
                    $assignee_id2 = User::select('id', DB::raw('CONCAT(first_name," ", last_name) AS full_name'))->where(DB::raw('CONCAT(first_name," ", last_name)'), (isset($task["assignee_name"]) && $task["assignee_name"] != '' ? $task["assignee_name"] : $request->card_form["assignee_name"]))->first()->id;
                    $tasks = (isset($task["id"]) ? Task::find($task["id"]) : new Task());
                    $tasks->name = $task["name"];
                    $tasks->assignee_name = (isset($task["assignee_name"]) && $task["assignee_name"] != '' ? $task["assignee_name"] : $request->card_form["assignee_name"]);
                    $tasks->assignee_id = $assignee_id2;
                    $tasks->due_date = (isset($task["due_date"]) ? Carbon::parse($task["due_date"])->toDateString() : (isset($task["date"]) ? Carbon::parse($task["date"])->toDateString() : (isset($request->card_form["deadline"]) ? Carbon::parse($request->card_form["deadline"])->toDateString() : Carbon::parse($request->card_form["due_date"])->toDateString())));
                    $tasks->parent_id = null;
                    $tasks->creator_id = auth()->id();
                    $tasks->card_id = $card->id;
                    $tasks->status_id = 1;
                    $tasks->save();

                    if (!empty($task["subtasks"])) {
                        foreach ($task["subtasks"] as $sub_task_name) {
                            if ($sub_task_name["name"] != "") {
                                $assignee_id2 = User::select('id', DB::raw('CONCAT(first_name," ", last_name) AS full_name'))->where(DB::raw('CONCAT(first_name," ", last_name)'), (isset($task["assignee_name"]) && $task["assignee_name"] != '' ? $task["assignee_name"] : $request->card_form["assignee_name"]))->first()->id;
                                $sub_task = (isset($sub_task_name["id"]) ? Task::find($sub_task_name["id"]) : new Task());
                                $sub_task->name = $sub_task_name["name"];
                                $sub_task->assignee_id = $assignee_id2;
                                $sub_task->assignee_name = (isset($task["assignee_name"]) && $task["assignee_name"] != '' ? $task["assignee_name"] : $request->card_form["assignee_name"]);
                                $sub_task->due_date = (isset($sub_task_name["due_date"]) ? Carbon::parse($sub_task_name["due_date"])->toDateString() : (isset($sub_task_name["date"]) ? Carbon::parse($sub_task_name["date"])->toDateString() : (isset($request->card_form["deadline"]) ? Carbon::parse($request->card_form["deadline"])->toDateString() : Carbon::parse($request->card_form["due_date"])->toDateString())));
                                $sub_task->parent_id = $tasks->id;
                                $sub_task->creator_id = Auth::id();
                                $sub_task->card_id = $card->id;
                                $sub_task->status_id = 1;
                                $sub_task->save();
                            }
                        }
                    }

                    if (!empty($task["sub_tasks"])) {
                        foreach ($task["sub_tasks"] as $sub_task_name) {
                            $assignee_id2 = User::select('id', DB::raw('CONCAT(first_name," ", last_name) AS full_name'))->where(DB::raw('CONCAT(first_name," ", last_name)'), $tasks["assignee_name"])->first()->id;
                            $sub_task = (isset($sub_task_name["id"]) ? Task::find($sub_task_name["id"]) : new Task());
                            $sub_task->name = $sub_task_name["name"];
                            $sub_task->assignee_id = $assignee_id2;
                            $sub_task->assignee_name = $task["assignee_name"];
                            $sub_task->due_date = (isset($sub_task_name["due_date"]) ? Carbon::parse($sub_task_name["due_date"])->toDateString() : (isset($sub_task_name["date"]) ? Carbon::parse($sub_task_name["date"])->toDateString() : (isset($request->card_form["deadline"]) ? Carbon::parse($request->card_form["deadline"])->toDateString() : Carbon::parse($request->card_form["due_date"])->toDateString())));
                            $sub_task->parent_id = $tasks->id;
                            $sub_task->creator_id = Auth::id();
                            $sub_task->card_id = $card->id;
                            $sub_task->status_id = 1;
                            $sub_task->save();
                        }
                    }
                }
            }

            return response()->json([
                'Card' => $card->load([
                        'creator',
                        'tasks.subTasks',
                        'assignedUser',
                        'priorityStatus',
                        'status',
                        'document',
                        'recordings']
                )]);
        } else {

            $assignee_id = ($request->card_form["assignee_name"] != '' ? User::select('id', DB::raw('CONCAT(first_name," ", last_name) AS full_name'))->where(DB::raw('CONCAT(first_name," ", last_name)'), $request->card_form["assignee_name"])->first()->id : null);
            $client_id = ($request->client_name != '' ? Client::select('id', DB::raw('CONCAT(first_name," ", last_name) AS full_name'))->where(DB::raw('CONCAT(first_name," ", last_name)'), $request->client_name)->first()->id : null);
            $card = (isset($request->section["id"]) ? Card::find($request->section["id"]) : (isset($request->card_form["saved"]) && $request->card_form["saved"] > 0 ? Card::find($request->card_form["saved"]) : new Card()));
            $card->name = $request->card_name;
            $card->insurer = (isset($request->card_form["insurer"]) ? $request->card_form["insurer"] : '');
            $card->policy = (isset($request->card_form["policy"]) ? $request->card_form["policy"] : '');
            $card->upfront_revenue = (isset($request->card_form["upfront_revenue"]) ? $request->card_form["upfront_revenue"] : '');
            $card->ongoing_revenue = (isset($request->card_form["ongoing_revenue"]) ? $request->card_form["ongoing_revenue"] : '');
            $card->dependency_id = (isset($request->card_form["dependency_id"]) ? $request->card_form["dependency_id"] : null);
            $card->due_date = (isset($request->card_form["selected_deadline"]) ? Carbon::parse($request->card_form["selected_deadline"])->toDateString() : (isset($request->card_form["due_date"]) ? Carbon::parse($request->card_form["due_date"])->toDateString() : null));
            $card->assignee_id = $assignee_id;
            $card->assignee_name = $request->card_form["assignee_name"];
            $card->team_names = (is_array($request->card_form["team_names"]) ? implode(', ', $request->card_form["team_names"]) : $request->card_form["team_names"]);
            $card->status_id = $request->card_form["progress_status_id"] ?? 1;
            $card->priority_id = $request->priority_id ?? (isset($request->card_form["priority_status_id"]) ? $request->card_form["priority_status_id"] : 1);
            $card->section_id = $request->section["section_id"];
            $card->summary_description = (isset($request->card_form["summary_description"]) ? $request->card_form["summary_description"] : '');
            $card->description = (isset($request->card_form["description"]) ? $request->card_form["description"] : '');
            $card->client_id = $client_id;
            $card->client_name = $request->client_name;
            $card->creator_id = Auth::id();
            if($request->has('saved') && $request->input('saved') != 0) {
                $card->enabled = 1;
            }
            $card->save();

            $id = $card->id;

            if (!empty($request->task)) {
                foreach ($request->task as $task) {
                    $assignee_id2 = User::select('id', DB::raw('CONCAT(first_name," ", last_name) AS full_name'))->where(DB::raw('CONCAT(first_name," ", last_name)'), (isset($task["assignee_name"]) && $task["assignee_name"] != '' ? $task["assignee_name"] : $request->card_form["assignee_name"]))->first()->id;
                    $tasks = (isset($task["id"]) ? Task::find($task["id"]) : new Task());
                    $tasks->name = $task["name"];
                    $tasks->assignee_name = (isset($task["assignee_name"]) && $task["assignee_name"] != '' ? $task["assignee_name"] : $request->card_form["assignee_name"]);
                    $tasks->assignee_id = $assignee_id2;
                    $tasks->due_date = (isset($task["due_date"]) ? Carbon::parse($task["due_date"])->toDateString() : (isset($task["date"]) ? Carbon::parse($task["date"])->toDateString() : (isset($request->card_form["deadline"]) ? Carbon::parse($request->card_form["deadline"])->toDateString() : Carbon::parse($request->card_form["due_date"])->toDateString())));
                    $tasks->parent_id = null;
                    $tasks->creator_id = auth()->id();
                    $tasks->card_id = $card->id;
                    $tasks->status_id = 1;
                    $tasks->save();

                    if (!empty($task["subtasks"])) {
                        foreach ($task["subtasks"] as $sub_task_name) {
                            if ($sub_task_name["name"] != "") {
                                $assignee_id2 = User::select('id', DB::raw('CONCAT(first_name," ", last_name) AS full_name'))->where(DB::raw('CONCAT(first_name," ", last_name)'), (isset($task["assignee_name"]) && $task["assignee_name"] != '' ? $task["assignee_name"] : $request->card_form["assignee_name"]))->first()->id;
                                $sub_task = (isset($sub_task_name["id"]) ? Task::find($sub_task_name["id"]) : new Task());
                                $sub_task->name = $sub_task_name["name"];
                                $sub_task->assignee_id = $assignee_id2;
                                $sub_task->assignee_name = (isset($task["assignee_name"]) && $task["assignee_name"] != '' ? $task["assignee_name"] : $request->card_form["assignee_name"]);
                                $sub_task->due_date = (isset($sub_task_name["due_date"]) ? Carbon::parse($sub_task_name["due_date"])->toDateString() : (isset($sub_task_name["date"]) ? Carbon::parse($sub_task_name["date"])->toDateString() : (isset($request->card_form["deadline"]) ? Carbon::parse($request->card_form["deadline"])->toDateString() : Carbon::parse($request->card_form["due_date"])->toDateString())));
                                $sub_task->parent_id = $tasks->id;
                                $sub_task->creator_id = Auth::id();
                                $sub_task->card_id = $card->id;
                                $sub_task->status_id = 1;
                                $sub_task->save();
                            }
                        }
                    }

                    if (!empty($task["sub_tasks"])) {
                        foreach ($task["sub_tasks"] as $sub_task_name) {
                            $assignee_id2 = User::select('id', DB::raw('CONCAT(first_name," ", last_name) AS full_name'))->where(DB::raw('CONCAT(first_name," ", last_name)'), $tasks["assignee_name"])->first()->id;
                            $sub_task = (isset($sub_task_name["id"]) ? Task::find($sub_task_name["id"]) : new Task());
                            $sub_task->name = $sub_task_name["name"];
                            $sub_task->assignee_id = $assignee_id2;
                            $sub_task->assignee_name = $task["assignee_name"];
                            $sub_task->due_date = (isset($sub_task_name["due_date"]) ? Carbon::parse($sub_task_name["due_date"])->toDateString() : (isset($sub_task_name["date"]) ? Carbon::parse($sub_task_name["date"])->toDateString() : (isset($request->card_form["deadline"]) ? Carbon::parse($request->card_form["deadline"])->toDateString() : Carbon::parse($request->card_form["due_date"])->toDateString())));
                            $sub_task->parent_id = $tasks->id;
                            $sub_task->creator_id = Auth::id();
                            $sub_task->card_id = $card->id;
                            $sub_task->status_id = 1;
                            $sub_task->save();
                        }
                    }
                }
            }


            return response()->json([
                'Card' => $card->load([
                        'creator',
                        'tasks.subTasks',
                        'assignedUser',
                        'priorityStatus',
                        'status',
                        'document',
                        'recordings']
                )]);
        }
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        //
    }


    public function update(Request $request, Card $card)
    {
        if ($request->has('name'))
            $card->name = $request->name;

        if ($request->has('assignee_name'))
            $card->assignee_id = User::select('id',DB::raw('CONCAT(first_name," ", last_name) AS full_name'))->where(DB::raw('CONCAT(first_name," ", last_name)'),$request->assignee_name)->first()->id;
        $card->assignee_name = $request->assignee_name;

        if ($request->has('client_name'))
            $card->client_id = Client::select('id',DB::raw('CONCAT(first_name," ", last_name) AS full_name'))->where(DB::raw('CONCAT(first_name," ", last_name)'),$request->client_name)->first()->id;
        $card->client_name = $request->client_name;

        if ($request->has('due_date'))
            $card->due_date = Carbon::parse($request->due_date)->addDay()->toDateString();

        if ($request->has('status_id'))
            $card->status_id = $request->status_id;

        if ($request->has('priority_id'))
            $card->priority_id = $request->priority_id;

        if ($request->has('description'))
            $card->description = $request->description;

        if ($request->has('team_names')) {
            /*$card->team_ids = implode(', ', $request->team_ids);*/
            $card->team_names  = implode(', ', $request->team_names);
        }

        if ($request->has('section_id'))
            $card->section_id = $request->section_id;

        $card->save();
        return response()->json(['card' => $card->load(['assignedUser', 'status', 'priorityStatus'])]);
    }


    public function destroy(Request $request,$card_id)
    {
        Card::destroy($card_id);

        return response()->json(['message'=>"success"]);
    }

    public function archive(Request $request,$card_id)
    {
        $card = Card::find($card_id);
        $card->archived = 1;
        $card->save();

        return response()->json([]);
    }

    public function unarchive(Request $request,$card_id)
    {
        $card = Card::find($card_id);
        $card->archived = 0;
        $card->save();

        return response()->json([]);
    }

    public function getStatuses()
    {
        $priorityStatus = PriorityStatus::get(['id', 'name','fcolor']);
        $progessStatus = Status::get(['id', 'name']);

        return response()->json([
            'priority_status' => $priorityStatus,
            'progress_status' => $progessStatus
        ]);
    }

    public function getOfficeClients(Request $request){

        $offices = array();

        $user_offices = OfficeUser::where('user_id',Auth::id())->get();

        foreach ($user_offices as $user_office){
            array_push($offices,$user_office->office_id);
        }

        $office_clients = Client::whereIn('office_id', $offices)->get(['id']);
        $office_clients = $office_clients->map(function ($client){
            return Client::select(DB::raw('CONCAT(first_name," ", last_name) AS full_name'))->where('id',$client->id)->first()->full_name;
        })->filter();

        $ca = [];

        foreach ($office_clients as $office_client){
            array_push($ca,$office_client);
        }

        return response()->json(['office_clients' => $ca]);
    }

    public function completeTasks(Request $request,$cardid){

        $task = Task::where('card_id',$cardid)->update(['status_id' => '1']);

        $card = Card::find($cardid);

        return response()->json([
            'Card' => $card->load([
                    'creator',
                    'tasks.subTasks',
                    'assignedUser',
                    'priorityStatus',
                    'status']
            )]);
    }

    public function getCardsDropDown()
    {
        $cards = Card::where('creator_id',Auth::id())->get();

        return response()->json(['cards' => $cards]);
    }

    public function uploadDocument(Request $request){
        $uploadedFile = $request->file('documentFile');

        $filename = $request->card_id.time().'.'.$uploadedFile->getClientOriginalExtension();

        $store = Storage::disk('public')->putFileAs(
            'pipeline/documents',
            $uploadedFile,
            $filename
        );

        $card = Card::find($request->card_id);
        $card->document = $filename;
        $card->save();

        return response()->json(['filename'=>$filename]);
    }

    public function deleteDocument(Request $request){

        $card = Card::find($request->card_id);
        $card->document = null;
        $card->save();

        return response()->json(['filename'=>'null']);
    }

    public function copyCard(Request $request){
        $s = $request->input('section');

        $section = Section::where('id',$s["id"])->first();

        $card = Card::find($request->input('card'));
        $tasks = Task::where('card_id',$card->id)->get();

        $new_card = new Card();
        $new_card->name = $card->name;
        $new_card->due_date = $card->due_date;
        $new_card->team_ids = $card->team_ids;
        $new_card->assignee_id = $card->assignee_id;
        $new_card->status_id = $card->status_id;
        $new_card->priority_id = $card->priority_id;
        $new_card->section_id = $section["id"];
        $new_card->description = $card->description;
        $new_card->summary_description = $card->summary_description;
        $new_card->assignee_name = $card->assignee_name;
        $new_card->team_names = $card->team_names;
        $new_card->client_id = $card->client_id;
        $new_card->client_name = $card->client_name;
        $new_card->archived = $card->archived;
        $new_card->creator_id = Auth::id();
        $new_card->insurer = $card->insurer;
        $new_card->policy = $card->policy;
        $new_card->upfront_revenue = $card->upfront_revenue;
        $new_card->ongoing_revenue = $card->ongoing_revenue;
        $new_card->dependency_id = $card->dependency_id;
        $new_card->enabled = $card->enabled;
        $new_card->save();

        if(count($tasks) > 0){
            foreach ($tasks  as $task){
                $new_task = new Task();
                $new_task->name = $task->name;
                $new_task->due_date = $task->due_date;
                $new_task->parent_id = $task->parent_id;
                $new_task->creator_id = Auth::id();
                $new_task->status_id = $task->status_id;
                $new_task->assignee_id = $task->assignee_id;
                $new_task->card_id = $new_card->id;
                $new_task->assignee_name = $task->assignee_name;
                $new_task->deadline_param_id = $task->deadline_param_id;
                $new_task->allowed_days = $task->allowed_days;
                $new_task->deadline_type = $task->deadline_type;
                $new_task->save();
            }
        }

        return response()->json([
            'board_id' => $section["board_id"],
            'section_id' => $section["id"],
            'card' => $new_card->load([
                    'creator',
                    'tasks.subTasks',
                    'assignedUser',
                    'priorityStatus',
                    'status',
                    'document',
                    'recordings']
            )]);
    }

    public function moveCard(Request $request){
        $b = $request->input('board');
        $s = $request->input('section');

        $card = Card::find($request->input('card'));
        $card->section_id = $s["id"];
        $card->save();

        return response()->json([
            'board_id' => (string)$b["id"],
            'section_id' => $s["id"],
            'card' => $card->load([
                    'creator',
                    'tasks.subTasks',
                    'assignedUser',
                    'priorityStatus',
                    'status',
                    'document',
                    'recordings']
            )]);
    }
}
