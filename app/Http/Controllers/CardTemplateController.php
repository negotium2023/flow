<?php

namespace App\Http\Controllers;

use App\Card;
use App\CardTemplate;
use App\Client;
use App\PriorityStatus;
use App\User;
use App\TaskTemplate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CardTemplateController extends Controller
{
    public function index(Request $request, $sectionid){
        $templates = CardTemplate::select('name')->where('section_id',$sectionid)->orderBy('id')->get();

        $template = [];

        foreach ($templates as $temp){
            array_push($template,$temp->name);
        }

        return $template;
    }

    public function store(Request $request){

        $assignee_id = User::select('id',DB::raw('CONCAT(first_name," ", last_name) AS full_name'))->where(DB::raw('CONCAT(first_name," ", last_name)'),$request->card_form["assignee_name"])->first()->id;
        if(isset($request->card_form["client_name"]) && $request->card_form["client_name"] != '') {
            $client_id = ($request->card_form["client_name"] != '' ? Client::select('id', DB::raw('CONCAT(first_name," ", last_name) AS full_name'))->where(DB::raw('CONCAT(first_name," ", last_name)'), $request->card_form["client_name"])->first()->id : null);
        }
        $card = new CardTemplate();
        $card->name = $request->card_form["name"];
        $card->due_date = Carbon::parse($request->due_date)->toDateString();
        $card->assignee_id = $assignee_id;
        $card->assignee_name = $request->card_form["assignee_name"];
        $card->team_names = implode(', ', $request->card_form["team_names"] ?? []);
        $card->status_id = $request->status_id ?? 1;
        $card->priority_id = $request->priority_id ?? 1;
        $card->section_id = $request->section["section_id"];
        $card->description = (isset($request->card_form["description"]) ? $request->card_form["description"] : '');
        $card->summary_description = (isset($request->card_form["summary_description"]) ? $request->card_form["summary_description"] : '');
        $card->insurer = (isset($request->card_form["insurer"]) ? $request->card_form["insurer"] : '');
        $card->policy = (isset($request->card_form["policy"]) ? $request->card_form["policy"] : '');
        $card->upfront_revenue = (isset($request->card_form["upfront_revenue"]) ? $request->card_form["upfront_revenue"] : '');
        $card->ongoing_revenue = (isset($request->card_form["ongoing_revenue"]) ? $request->card_form["ongoing_revenue"] : '');
        $card->client_id = (isset($client_id) ? $client_id : 0);
        $card->client_name = (isset($request->card_form["client_name"]) && $request->card_form["client_name"] != '' ? $request->card_form["client_name"] : '');
        $card->creator_id = Auth::id();
        $card->save();

        if (!empty($request->task)) {
            foreach ($request->task as $task) {
                $assignee_id2 = User::select('id',DB::raw('CONCAT(first_name," ", last_name) AS full_name'))->where(DB::raw('CONCAT(first_name," ", last_name)'),(isset($task["assignee_name"]) && $task["assignee_name"] != '' ? $task["assignee_name"] : $request->card_form["assignee_name"]))->first()->id;
                $tasks = new TaskTemplate();
                $tasks->name = $task["name"];
                $tasks->assignee_name = (isset($task["assignee_name"]) && $task["assignee_name"] != '' ? $task["assignee_name"] : $request->card_form["assignee_name"] );
                $tasks->assignee_id = $assignee_id2;
                $tasks->due_date = (isset($task["date"]) && $task["date"] != '' ? Carbon::parse($task["date"])->addDay()->toDateString() : Carbon::parse($request->due_date)->addDay()->toDateString());
                $tasks->parent_id = null;
                $tasks->creator_id = auth()->id();
                $tasks->card_template_id = $card->id;
                $tasks->status_id = 1;
                $tasks->save();

                if (!empty($task["subtasks"])) {
                    foreach ($task["subtasks"] as $sub_task_name) {
                        $assignee_id2 = User::select('id',DB::raw('CONCAT(first_name," ", last_name) AS full_name'))->where(DB::raw('CONCAT(first_name," ", last_name)'),(isset($tasks->assignee_name) && $tasks->assignee_name != '' ? $tasks->assignee_name : $card->assignee_name))->first()->id;
                        $sub_task = new TaskTemplate();
                        $sub_task->name = $sub_task_name["name"];
                        $sub_task->assignee_id = $assignee_id2;
                        $sub_task->assignee_name = (isset($tasks->assignee_name) && $tasks->assignee_name != '' ? $card->assignee_name : $tasks->assignee_name);
                        $sub_task->due_date = (isset($tasks->due_date) && $tasks->due_date != '' ? $tasks->due_date : Carbon::parse($request->due_date)->addDay()->toDateString());
                        $sub_task->parent_id = $tasks->id;
                        $sub_task->creator_id = $tasks->creator_id;
                        $sub_task->card_template_id = $card->id;
                        $sub_task->status_id = $tasks->status_id;
                        $sub_task->save();
                    }
                }
            }
        }

        return ['message' => 'Template successfully saved'];
    }

    public function show(Request $request, $templateid){
        $tmp = CardTemplate::with('tasks')->where('name',$templateid)->first();
        /*$template = $template->map(function ($temp){
            $temp['open'] = false;
            $temp['link_client'] = false;
            return $temp;
        });*/


        $tasks = [];

        foreach ($tmp["tasks"] as $task){

            $subtasks = [];

            foreach ($task["subTasks"] as $subtask){
                array_push($subtasks,[
                    'name' => $subtask["name"],
                    'assignee_name' => $subtask["assignee_name"],
                    'selected_assignee' => $subtask["assignee_name"],
                    'selected_duedate' => $subtask["due_date"],
                    'date' => $subtask["due_date"],
                    'add_deadline' => false,
                    'open3' => false,
                ]);
            }


            array_push($tasks,[
               'name' => $task["name"],
               'assignee_name' => $task["assignee_name"],
               'selected_assignee' => $task["assignee_name"],
               'selected_duedate' => $task["due_date"],
               'date' => $task["due_date"],
                'add_sub_task' => true,
                'open' => false,
                'assign_task' => false,
                'add_deadline' => false,
                'subtasks' => $subtasks
            ]);
        }

        $var = [];

        $var['saved'] = 0;
        $var['name'] = $tmp["name"];
        $var['deadline'] = $tmp["due_date"];
        $var['selected_deadline'] = $tmp["due_date"];
        $var['description'] = $tmp["description"];
        $var['upfront_revenue'] = $tmp["upfront_revenue"];
        $var['ongoing_revenue'] = $tmp["ongoing_revenue"];
        $var['insurer'] = $tmp["insurer"];
        $var['policy'] = $tmp["policy"];
        $var['assignee_name'] = $tmp["assignee_name"];
        /*$var['progress_status_id'] = $tmp["status_id"];*/
        $var['progress_status_id'] = 1;
        $var['priority_status_id'] = $tmp["priority_id"];
        $var['priority_status'] = PriorityStatus::get(['id', 'name','fcolor']);;
        $var['team_names'] = $tmp["team_names"];
        $var['summary_description'] = $tmp["summary_description"];
        $var['description'] = $tmp["description"];
        $var['open'] = false;
        $var['open2'] = false;
        $var['assign_user'] = false;
        $var['add_deadline'] = false;
        $var['add_sub_task'] = false;
        $var['editTask'] = false;
        $var['selected_assignee'] = $tmp["assignee_name"];
        $var['selected_duedate'] = $tmp["due_date"];
        $var['client_name'] = $tmp["client_name"];
        $var['tasks'] = $tasks;

        return $var;
    }
}
