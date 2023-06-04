<?php

namespace App\Http\Controllers;

use App\OfficeUser;
use App\Task;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Card;
use App\Recording;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function __construct()
    {
        return $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $office_users = OfficeUser::where('office_id', auth()->user()->office()->id??0)->get(['user_id']);
        $office_users = $office_users->map(function ($user){
            return User::select(DB::raw('CONCAT(first_name," ", last_name) AS full_name'))->where('id',$user->user_id)->first()->full_name;
        })->filter();
        return response()->json(['office_users' => $office_users]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $task = new Task();
        $task->name = $request->task["name"];
        $task->parent_id = null;
        $task->creator_id = auth()->id();
        $task->card_id = $request->card_id;
        $task->status_id = 0;
        $task->save();

        return response()->json(['task' => $task->load('assigned')]);
    }

    public function show(Task $task)
    {
        //
    }

    public function edit(Task $task)
    {
        //
    }


    public function update(Request $request, $task_id)
    {
        if($task_id > 0) {
            $task = Task::find($task_id);
            if ($request->has("assignee_name")) {
                $task->assignee_name = $request->assignee_name;
            }
            if ($request->has("due_date")) {
                $task->due_date = Carbon::parse($request->due_date)->addDay()->toDateString();
            }

            if ($request->has('name')) {
                $task->name = $request->name;
            }
            $task->save();

            $task->load(['assigned', 'subTasks']);
            $task->load(['assigned', 'subTasks']);
        } else {
            $task = new Task();

            if ($request->has("assignee_name")) {
                $task->assignee_name = $request->assignee_name;
            }
            if ($request->has("cardid")) {
                $task->card_id = $request->cardid;
            }
            if ($request->has("due_date")) {
                $task->due_date = Carbon::parse($request->due_date)->addDay()->toDateString();
            }

            if ($request->has('name')) {
                $task->name = $request->name;
            }
            $task->save();

            $task->load(['assigned', 'subTasks']);
            $task->load(['assigned', 'subTasks']);
        }


        return response()->json(['task' => $task]);
    }

    public function delete(Request $request, $task)
    {
        Task::destroy($task);

        return response()->json(['message'=>"success"]);
    }

    public function storeSubtask(Request $request,Task $task)
    {
        $subtask = new Task();
        $subtask->name = $request->name;
        $subtask->assignee_id = $task->assignee_id;
        $subtask->due_date = $task->due_date;
        $subtask->parent_id = $task->id;
        $subtask->creator_id = auth()->id();
        $subtask->card_id = $task->card_id;
        $subtask->status_id = $task->status_id;
        $subtask->save();
        return response()->json(['subtask' => $subtask]);
    }

    public function updateDueDate(Request $request,$task_id){
        $task = Task::find($task_id);
        $task->due_date = Carbon::parse($request->due_date)->addDay()->toDateString();
        $task->save();

        return response()->json(['task' => $task]);
    }

    public function updatestatus(Request $request,$task_id){
        $task = Task::find($task_id);
        $task->status_id = $request->status;
        $task->save();

        return response()->json(['task' => $task]);
    }

    public function getAdvisor(Request $request){
        $office_users = OfficeUser::where('office_id', auth()->user()->office()->id??0)->get(['user_id']);
        $office_users = $office_users->map(function ($user){
            return User::select(DB::raw('CONCAT(first_name," ", last_name) AS full_name'))->where('id',$user->user_id)->get();
        })->filter();

        return response()->json(['office_users' => $office_users]);
    }

    public function recordVoice(Request $request,$card_id){
        // dd($card_id);
        $r = file_get_contents($request->audio);
        $file_name = $request->card_id.'-'.Carbon::parse(now())->format('YmdHis').'.mp3';
        $user = Auth::user();
        $user_name = $user->first_name.' '.$user->last_name;
        Storage::disk('public')->put('recording/'.$file_name, $r);
        // $card = Card::find($request->card_id);
        // $card->recording = $file_name;
        // $card->save();
        $recording = new Recording();
        $recording->card_id = $card_id;
        $recording->recording = $file_name;
        $recording->user = $user_name;
        $recording->save();

        return response()->json(['recording' => $recording]);
    }
}
