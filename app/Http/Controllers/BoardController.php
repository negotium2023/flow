<?php

namespace App\Http\Controllers;

use App\Board;
use App\CardTemplate;
use App\OfficeUser;
use App\Section;
use App\User;
use App\Recording;
use Illuminate\Http\Request;
use App\Task;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BoardController extends Controller
{
    public function __construct()
    {
        return $this->middleware('auth');
    }

    public function workflows(Request $request, $board_id = null)
    {
        $offices = OfficeUser::where('user_id',Auth::id())->first();

        if($board_id == null){
            $board_id = Board::select('id')->where('office_id',$offices->office_id)->first()->id??0;
        }
        $sections_array = [];



        $sections = Section::with(['cards' => function ($q){
            $q->where('enabled',1);
        },'cards.creator','cards.discussions','cards_templates','cards.recordings','cards.document'])
            ->where('board_id', $board_id)->orderBy('id')->get();
        /*$sections = $sections->map(function ($section){
            $section["editFlag"] = 0;
            $section["updateFlag"] = 0;
            return $section;
        });*/
        //dd($sections);
        foreach($sections as $section){

            $values = [];
            $cards_array = [];

            $cards = $section["cards"];
            $cards_templates = $section["cards_templates"];
            $voicenotes = $section["recordings"];

            $template_dd = [];

            $cts = CardTemplate::select('name')->where('section_id', $section->id)->get();

            foreach ($cts as $ct){
                array_push($template_dd,$ct->name);
            }

            /*$dd_string = '';

            foreach ($template_dd as $td){
                if($dd_string == ''){
                    $dd_string =$td;
                } else {
                    $dd_string = $dd_string.','.$td;
                }
            }*/

            $values = [
                'id' => $section->id,
                'name' => $section->name,
                'board_id' => $section->board_id,
                'creator_id' => $section->creator_id,
                'status_id' => $section->status_id,
                'created_at' => $section->created_at,
                'updated_at' => $section->updated_at,
                'deleted_at' => $section->deleted_at,
                'open' => false,
                'open1' => false,
                'open2' => false,
                'open3' => false,
                'open4' => false,
                'open5' => false,
                'open6' => false,
                'open7' => false,
                'open14' => false,
                'open15' => false,
                'editFlag' => 0,
                'updateFlag' => 0,
                'updateClient' => 0,
                'cards' => $cards,
                'cards_templates' => $cards_templates,
                'cards_templates_dropdown' => $template_dd,
                'createTemplateModal' => false,
                'copyCardModal' => false,
                'moveCardModal' => false,
                'showArchived' => 0,
                'voicenotes' => $voicenotes,
                'selected_board'  => '',
                'boards_dropdown' => [],
                'selected_section' => '',
                'section_dropdown' => []
                ];

            $upfront_revenue_total = 0.0;
            $ongoing_revenue_total = 0.0;
            for($i = 0; $i < count($cards); $i++){
                // Calculate the revenues
                $upfront_revenue_total += (double)$cards[$i]->upfront_revenue;
                $ongoing_revenue_total += (double)$cards[$i]->ongoing_revenue;

                $values["cards"][$i]['open'] = false;
                $values["cards"][$i]['link_client'] = false;
                $values["cards"][$i]['files'] = [];
                $values["cards"][$i]['saved'] = 0;
                $values["cards"][$i]['selected_deadline'] = '';
                for($j = 0; $j < count($values["cards"][$i]['tasks']); $j++){
                    $values["cards"][$i]['tasks'][$j]['open6'] = false;
                    $values["cards"][$i]['tasks'][$j]['assign_user'] = false;
                    $values["cards"][$i]['tasks'][$j]['add_deadline'] = false;
                    $values["cards"][$i]['tasks'][$j]['add_sub_task'] = false;
                    $values["cards"][$i]['tasks'][$j]['status'] = ($values["cards"][$i]['tasks'][$j]['status_id'] == 1 ? true : false);
                    $values["cards"][$i]['tasks'][$j]['editTask'] = false;
                    $values["cards"][$i]['tasks'][$j]['assignee'] = $values["cards"][$i]['tasks'][$j]['assignee_name'];
                    $values["cards"][$i]['tasks'][$j]['selected_assignee'] = $values["cards"][$i]['tasks'][$j]['assignee_name'];
                    $values["cards"][$i]['tasks'][$j]['selected_duedate'] = $values["cards"][$i]['tasks'][$j]['due_date'];
                    $values["cards"][$i]['tasks'][$j]['duedate'] = $values["cards"][$i]['tasks'][$j]['due_date'];

                    for($k = 0; $k < count($values["cards"][$i]['tasks'][$j]['subTasks']); $k++){
                        $values["cards"][$i]['tasks'][$j]['subTasks'][$k]['open'] = false;
                        $values["cards"][$i]['tasks'][$j]['subTasks'][$k]['add_deadline'] = false;
                        $values["cards"][$i]['tasks'][$j]['subTasks'][$k]['editSubtask'] = false;
                        $values["cards"][$i]['tasks'][$j]['subTasks'][$k]['selected_assignee'] = $values["cards"][$i]['tasks'][$j]['subTasks'][$k]['assignee_name'];
                        $values["cards"][$i]['tasks'][$j]['subTasks'][$k]['selected_duedate'] = $values["cards"][$i]['tasks'][$j]['subTasks'][$k]['due_date'];
                    }
                }
            }

            for($i = 0; $i < count($cards_templates); $i++){
                $values["cards_templates"][$i]['open'] = false;
                $values["cards_templates"][$i]['link_client'] = false;
                for($j = 0; $j < count($values["cards_templates"][$i]['tasks']); $j++){
                    $values["cards_templates"][$i]['tasks'][$j]['open'] = false;
                    $values["cards_templates"][$i]['tasks'][$j]['assign_user'] = false;
                    $values["cards_templates"][$i]['tasks'][$j]['add_deadline'] = false;
                    $values["cards_templates"][$i]['tasks'][$j]['add_sub_task'] = false;
                    $values["cards_templates"][$i]['tasks'][$j]['editTask'] = false;
                    $values["cards_templates"][$i]['tasks'][$j]['selected_assignee'] = $values["cards_templates"][$i]['tasks'][$j]['assignee_name'];
                    $values["cards_templates"][$i]['tasks'][$j]['selected_duedate'] = $values["cards_templates"][$i]['tasks'][$j]['due_date'];

                    for($k = 0; $k < count($values["cards_templates"][$i]['tasks'][$j]['subTasks']); $k++){
                        $values["cards_templates"][$i]['tasks'][$j]['subTasks'][$k]['open'] = false;
                        $values["cards_templates"][$i]['tasks'][$j]['subTasks'][$k]['add_deadline'] = false;
                        $values["cards_templates"][$i]['tasks'][$j]['subTasks'][$k]['editSubtask'] = false;
                        $values["cards_templates"][$i]['tasks'][$j]['subTasks'][$k]['selected_assignee'] = $values["cards_templates"][$i]['tasks'][$j]['subTasks'][$k]['assignee_name'];
                        $values["cards_templates"][$i]['tasks'][$j]['subTasks'][$k]['selected_duedate'] = $values["cards_templates"][$i]['tasks'][$j]['subTasks'][$k]['due_date'];
                    }
                }
            }
            // dd($values);

            $values['upfront_revenue'] = number_format($upfront_revenue_total, 2, '.', ',');
            $values['ongoing_revenue'] = number_format($ongoing_revenue_total, 2, '.', ',');

            //dd($values);
            array_push($sections_array, $values);
        }


        array_push($sections_array,[
                'id' => 0,
                'name' => 'Add Section',
                'board_id' => $board_id,
                'creator_id' => 1,
                'status_id' => 1,
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
                'editFlag' => 0,
                'updateFlag' => 0,
                'cards' => []
        ]);

        // dd($sections_array);
        $vueParameters = [
            'sections' => $sections_array,
            'board_id' => $board_id,
        ];
        // dd($vueParameters);

        $parameters = [
            'vueParameters' => $vueParameters,
            'board_id' => $board_id
        ];

        //return $parameters;

        return view('workflows.index')->with($parameters);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_offices = OfficeUser::select('office_id')->where('user_id',Auth::id())->get();

        $boards = Board::with('sections.cards')->orderBy('id')->whereIn('office_id',collect($user_offices)->toArray())->get();
        $boards = $boards->map(function ($board){
            $board["hover"] = 0;
            $board["editBoardFlag"] = false;
            return $board;
        });

        return $boards;
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
        $board = new Board();
        $board->name = $request->name;
        $board->status_id = 1;
        $board->creator_id = Auth::id();
        $board->created_at = now();
        $board->office_id = OfficeUser::where('user_id',Auth::id())->first()->office_id;
        $board->save();

        return ['message' => 'Board successfully saved', 'board' => $board];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Board  $board
     * @return \Illuminate\Http\Response
     */
    public function show(Board $board)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Board  $board
     * @return \Illuminate\Http\Response
     */
    public function edit(Board $board)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Board  $board
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Board $board)
    {
        $board = Board::find($request->input('board_id'));
        $board->name = $request->name;
        $board->save();

        $board['editBoardFlag'] = false;

        return ['message' => 'Board successfully updated', 'board' => $board];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Board  $board
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $section = Board::destroy($request->input('board_id'));

        return ['message' => 'Board successfully deleted'];
    }

    public function getBoards(Request $request){
        $offices = OfficeUser::where('user_id',Auth::id())->first();

        $board_array = [];

        $boards = Board::whereHas('sections')->where('office_id',$offices->office_id)->get();

        foreach ($boards as $board){
            array_push($board_array,['id'=>$board->id,'name'=>$board->name]);
        }

        return $board_array;
    }

    public function getBoardSections(Request $request){

        $section_array = [];

        $b = $request->board;

        $sections = Section::where('board_id',$b["id"])->get();

        foreach ($sections as $section){
            array_push($section_array,["id"=>$section->id,"name"=>$section->name]);
        }

        return $section_array;
    }
}
