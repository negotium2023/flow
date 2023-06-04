<?php

namespace App\Http\Controllers;

use App\ActionableBooleanData;
use App\ActionableDateData;
use App\ActionableDocumentEmailData;
use App\ActionableTemplateEmailData;
use App\ActionableDropdownData;
use App\ActionableDropdownItem;
use App\ActionableTextData;
use App\ActionableTextareaData;
use App\Activity;
use App\Client;
use App\ClientProcess;
use App\Committee;
use App\Config;
use App\Currency;
use App\Document;
use App\EmailLogs;
use App\HelperFunction;
use App\Process;
use App\Referrer;
use App\RelatedPartiesTree;
use App\RelatedParty;
use App\Step;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\BusinessUnits;

class HomeController extends Controller
{
    public function __construct()
    {
        return $this->middleware('auth');
    }

    public function index()
    {
        if (auth()->check()) {
            return redirect(route('dashboard'));
        } else {
            return view('welcome');
        }
    }

    public function recents()
    {

        $parameters = [
            'clients' => Client::orderBy('created_at','DESC')->take(7)->get(),
            'referrers' => Referrer::orderBy('created_at','DESC')->take(5)->get(),
            'documents' => Document::orderBy('created_at','DESC')->take(5)->get(),
            'emails' => EmailLogs::orderBy('date','DESC')->take(5)->get()
        ];

        return view('recents')->with($parameters);
    }

    public function dashboard(Request $request)
        {
            $config = Config::first();

            $first_client = Client::orderBy('created_at','asc')->first()->created_at;

            $processes = Process::where('key_process','1')->orderBy('id','asc')->pluck('name', 'id');

            /*$dashboard_regions = explode(',',$config->dashboard_regions);*/

            //if (!$request->has('l') || !$request->has('r') || !$request->has('f') || !$request->has('t') || !$request->has('p')) {
            if (!$request->has('r') || !$request->has('f') || !$request->has('t') || !$request->has('p')) {

                $default_process = Process::where('default_process','1')->first();
                return redirect(route('dashboard', ['r' => 'week', 'f' => Carbon::parse($first_client)->format("Y-m-d"), 't' => Carbon::now()->toDateString(), 'p' => ($request->has('p') ? $request->input('p') : $default_process->id)]));
            }

            if ($request->has('r')) {
                $range = $request->input('r');
            } else {
                $range = 'day';
            }
            if ($request->has('f')) {
                $from = Carbon::parse($request->input('f'));
            } else {
                //$from = Carbon::now()->subWeek();
                $from = Carbon::createFromFormat('Y-m-d', '2010-01-01');
            }

            if ($request->has('t')) {
                $to = Carbon::parse($request->input('t'));
            } else {
                $to = Carbon::now();
            }

            $to->addHours(23)->addMinutes(59);

            if ($request->has('p') && $request->input('p') != '') {
                $process = Process::where('id', $request->input('p'))->first();
            } else {
                $process = Process::where('default','1')->first();
            }

            $outstanding_step = Step::where('process_id',$process->id)->where('dashboard_outstanding','1')->first();
            $outstanding_activities = [];
            if(isset($outstanding_step->dashboard_outstanding) && $outstanding_step->dashboard_outstanding == '1'){
                $outstanding_activities = $this->getOutstandingActivities($process->id,$outstanding_step->id);
            }
            $bck_outstanding1 = null;
            $bck_outstanding2 = null;
            if($request->has('p') && $request->input('p') == '7'){
                $bck_outstanding1 = $this->getBCKOutstandingActivities1('7','31');
                $bck_outstanding2 = $this->getBCKOutstandingActivities2('7','32');
            }
            $david_outstanding1 = null;
            $david_outstanding2 = null;
            if($request->has('p') && $request->input('p') == '8'){
                $david_outstanding1 = $this->getDavidOutstandingActivities1('8','38');
                $david_outstanding2 = $this->getDavidOutstandingActivities2('8','39');
            }

            $clients = Client::where('is_progressing', '=', 1);

            $parameters = [
                'client_step_counts' => $this->getClientStepCounts($clients, $process, $from, $to),
                'client_converted_count' => ((int)$process->key_process == 1 ? $this->getConvertedCount($process) : -1),
                /*'client_onboard_times' => $this->getClientOnboardTimes($process, $from, $to),*/
                'client_onboards' => $this->getClientOnboards($process, $range),
                /*'process_average_times' => $this->getProcessAverageTimes($process, $from, $to),*/
                'process_outstanding_activities' => $outstanding_activities,
                'bck_outstanding1' => $bck_outstanding1,
                'bck_outstanding2' => $bck_outstanding2,
                'david_outstanding1' => $david_outstanding1,
                'david_outstanding2' => $david_outstanding2,
                'outstanding_step_id' => (isset($outstanding_step->id) ? $outstanding_step->id : ''),
                'outstanding_activity_name' => (isset($outstanding_step->name) ? $outstanding_step->name : ''),
                'config' => $config,
                'process_id' =>$process->id,
                'processes' => $processes,
                'regions' => Step::select(['id', 'name', 'colour', 'process_id'])->where('process_id',$process->id)->where('dashboard_region','1')->orderBy('order','asc')->get()->toArray(),
                'converted_value' => $this->getConvertedValue($config, $clients)
            ];
            //dd($parameters["client_converted_count"]);
            return view('dashboard')->with($parameters);
        }

        public function getConvertedCount(Process $process)
        {
            $client_step_counts = Client::where('process_id', $process->id)->where('is_progressing', '=', 1)->where('updated_at', '!=', 'completed_at')->where('completed_at','!=', null)->where('completed_at','>=',Carbon::parse(now())->startOfYear()->format('Y-m-d'))->where('completed_at','<=',Carbon::parse(now())->endOfYear()->format('Y-m-d'))->count();

            return $client_step_counts;
        }

        public function getClientStepCounts($clients, Process $process, Carbon $from, Carbon $to)
        {
            //$config = Config::first();

            //$dashboard_regions = explode(',',$config->dashboard_regions);

            $clients = Client::selectRaw('DISTINCT step_id')
                ->where('is_progressing','=',1)
            ->where('process_id', $process->id)
            ->where(function ($query) use ($from) {
                $query->where('created_at', '>=', $from)
                    ->orWhere('completed_at', '>=', $from)
                    ->orWhere('updated_at', '>=', $from);
            })
            ->where(function ($query) use ($to) {
                $query->where('created_at', '<=', $to)
                    ->orWhere('completed_at', '<=', $to)
                    ->orWhere('updated_at', '<=', $to);
            })->get();


            $client_step_counts = [];

           foreach ($clients as $res) {
               if($res->step_id == '5'){
                   $client_step_counts[$res->step_id] = Client::where('process_id',$process->id)->where('is_progressing','=',1)->where('updated_at','>=',$from)->where('updated_at','<=',$to)->where('step_id',$res->step_id)->where('completed_at',null)->count();
               } else {
                   $client_step_counts[$res->step_id] = Client::where('process_id', $process->id)->where('is_progressing', '=', 1)->where('step_id', $res->step_id)->where('completed_at', null)->count();
               }
           }

            return $client_step_counts;
        }

        public function getClientOnboardTimes(Process $process, Carbon $from, Carbon $to)
        //public function getClientOnboardTimes($process_id, $step_id)
        {
            $config = Config::first();
            $step = $config->dashboard_activities_step_for_age;

            $clients = Client::with('process.activities.actionable.data')
                            ->where('process_id',$process->id)
                            ->whereNotNull('created_at')
                ->where(function ($query) use ($from) {
                    $query->where('completed_at', '>=', $from);
                })
                ->where(function ($query) use ($to) {
                    $query->where('completed_at', '<=', $to);
                })
                            ->get();


            $client_array = array();
            $client_array["days"] = array();

            $cnt = 0;


            foreach ($clients as $client){

                foreach ($client->process->activities as $activity){
    //dd($activity);
                    if($activity->step_id == $step) {
                        foreach ($activity->actionable['data'] as $data) {
                            $max = 0;
                            if ($data["created_at"] != null) {

                                $max = $max + Carbon::parse($data["created_at"])->diffInDays($client->completed_at);
                                array_push($client_array["days"], $max);

                            }
    //
                            $cnt++;

                        }

                    }

                }

            }
            sort($client_array['days']);
            $min = $client_array['days'][0];
            rsort($client_array['days']);
            $max = $client_array['days'][0];

            $avg = round(array_sum($client_array['days']) / $cnt,0);

            return ['minimum'=>$min,'average'=>$avg,'maximum'=>$max];
        }

        public function getClientOnboards(Process $process, String $range)
        {
            $from = Carbon::parse(now())->startOfYear();
            $to = Carbon::parse(now())->endOfYear();

            $process->load(['steps2']);

            $last_step = $process->steps2->sortByDesc('order')->first();

            switch ($range) {
                default:
                case 'day':
                    $date_diff = $from->diffInDays($to);

                    $client_query = Client::where('step_id',$last_step->id)->where('process_id', $process->id)->where('is_progressing', '=', 1)->whereDate('completed_at','>=',$from)->whereDate('completed_at','<=',$to)->where('completed_at','!=', null)->select(DB::raw('DATE_FORMAT(completed_at, "%e %M %Y") as date'), DB::raw('count(*) as onboarded'))->groupBy('date')->pluck('onboarded', 'date')->toArray();

                    $client_onboards = [];
                    for ($i = 0; $i <= $date_diff; $i++) {
                        $working_date = $from->copy()->addDays($i)->format('j F Y');
                        if (isset($client_query[$working_date])) {
                            $client_onboards[$working_date] = $client_query[$working_date];
                        } else {
                            $client_onboards[$working_date] = 0;
                        }
                    }

                    break;
                case 'week':
                    $date_diff = $from->diffInWeeks($to->addDay(1));
    //dd($date_diff);
                    $client_query = Client::where('step_id',$last_step->id)->where('process_id', $process->id)->where('is_progressing', '=', 1)->whereDate('completed_at','>=',$from)->whereDate('completed_at','<=',$to)->where('completed_at','!=', null)->select(DB::raw('DATE_FORMAT(completed_at, "%u %x") as date'), DB::raw('count(*) as onboarded'))->groupBy('date')->pluck('onboarded', 'date')->toArray();
                    //dd($client_query);
                    $client_onboards = [];
                    for ($i = 0; $i <= $date_diff; $i++) {

                        $readable_date = $from->copy()->startOfWeek()->addWeeks($i)->format('j F Y');
                        $working_date = $from->copy()->startOfWeek()->addWeeks($i)->format('W Y');
                        if (isset($client_query[$working_date])) {
                            $client_onboards[$readable_date] = $client_query[$working_date];
                        } else {
                            $client_onboards[$readable_date] = 0;
                        }
                    }

                    break;
                case 'month':
                    $date_diff = $from->diffInMonths($to);

                    $client_query = Client::where('step_id',$last_step->id)->where('process_id', $process->id)->where('is_progressing', '=', 1)->whereDate('completed_at','>=',$from)->whereDate('completed_at','<=',$to)->where('completed_at','!=', null)->select(DB::raw('DATE_FORMAT(updated_at, "%e %M %Y") as date'), DB::raw('count(*) as onboarded'))->groupBy('date')->pluck('onboarded', 'date')->toArray();

                    $client_onboards = [];
                    for ($i = 0; $i <= $date_diff; $i++) {
                        $working_date = $from->copy()->addMonths($i)->format('F Y');
                        if (isset($client_query[$working_date])) {
                            $client_onboards[$working_date] = $client_query[$working_date];
                        } else {
                            $client_onboards[$working_date] = 0;
                        }
                    }

                    break;
                case 'year':
                    $date_diff = $from->diffInYears($to);

                    $client_query = Client::where('step_id',$last_step->id)->where('process_id', $process->id)->where('is_progressing', '=', 1)->whereDate('completed_at','>=',$from)->whereDate('completed_at','<=',$to)->where('completed_at','!=', null)->select(DB::raw('DATE_FORMAT(updated_at, "%e %M %Y") as date'), DB::raw('count(*) as onboarded'))->groupBy('date')->pluck('onboarded', 'date')->toArray();

                    $client_onboards = [];
                    for ($i = 0; $i <= $date_diff; $i++) {
                        $working_date = $from->copy()->addYears($i)->format('Y');
                        if (isset($client_query[$working_date])) {
                            $client_onboards[$working_date] = $client_query[$working_date];
                        } else {
                            $client_onboards[$working_date] = 0;
                        }
                    }
                    break;
            }
            return $client_onboards;
        }

        public function getProcessAverageTimes(Process $process, Carbon $from, Carbon $to)
        {
            $configs = Config::first();

            $step_ids = explode(',',$configs->dashboard_avg_step);

            $client_array = new Collection();

            $clients = Client::select('id','created_at')
                                        ->where('process_id', $process->id)
                                        //->whereNotNull('completed_at')
                                        ->where(function ($query) use ($from) {
                                            $query->where('created_at', '>=', $from)
                                                ->orWhere('updated_at', '>=', $from)
                                                ->orWhere('completed_at', '>=', $from);
                                        })
                                        ->where(function ($query) use ($to) {
                                            $query->where('created_at', '<=', $to)
                                                ->orWhere('updated_at', '<=', $to)
                                                ->orWhere('completed_at', '<=', $to);
                                        })->get()->toArray();
    //dd($clients);
            foreach($clients as $client){
                $client_array->push([
                    'id' => $client["id"],
                    'created_at' => $client["created_at"]
                ]);
            }
            //dd($client_array);
            $process_average_times = [];
            foreach ($process->steps as $step) {
                if(in_array($step->id,$step_ids)) {
                    $process_average_times[$step->name] = 0;
                    $step_duration = 0;
                    $data_count = 0;

                    $cnt = 0;
                    $activity_array = collect($step->activities)->toArray();

                    //remove array values where created_at = null
                    foreach ($activity_array as $key => $value){
                        if(empty($activity_array[$key]['created_at'])){
                            unset($activity_array[$key]);
                        }
                    }

                    foreach ($step->activities as $activity) {

                        $cnt++;

                            if (isset($activity->actionable['data'])) {
                                foreach($activity->actionable['data'] as $key => $value) {
                                    if (empty($activity->actionable['data'][$key]['created_at'])) {
                                        unset($activity->actionable['data'][$key]);
                                    }
                                }


                                foreach ($activity->actionable['data'] as $data) {

                                    if (isset($data["created_at"]) && $data["created_at"] >= $from && $data["created_at"] <= $to) {

                                        $search = array();

                                        foreach ($client_array as $client) {
                                            if ($client['id'] == $data["client_id"]) {

                                                array_push($search, $client['created_at']);
                                            }
                                        }

                                        if (count($search) > 0) {

                                            $step_duration += (isset($data['created_at']) ? Carbon::parse($search[0])->diffInDays(Carbon::parse($data['created_at'])) : 0);
                                            $data_count++;

                                        } else {

                                            $step_duration += 0;
                                            $data_count++;

                                        }
                                    }
                                }
                            }
                    }
                    $process_average_times[$step->name] = round($step_duration / (($data_count > 0) ? $data_count : 1));
                }
            }
            return $process_average_times;
        }

        public function getOutstandingActivities($process_id,$step_id)
        {
            //$configs = Config::first();
            $step = Step::where('id',$step_id)->first();

            $activity_ids = explode(',',$step->dashboard_outstanding_default);

            $process = Process::with(['steps.activities.actionable', 'clients', 'steps2'])->where('id',$process_id)->first();

            $clients = $process->clients->where('is_progressing',1)->where('step_id',$step_id)->pluck('id');

            $outstanding_activities = [];
            foreach ($process->steps as $step) {
                foreach ($step->activities as $activity) {
                    if(($key = array_search($activity->id, $activity_ids)) === false) {

                    } else {

                        if ($activity->step_id == $step_id) {

                            $outstanding_activities[$activity->name] = [
                                'user' => 0
                            ];
                            foreach ($clients as $client_id) {
                                $has_data = false;
                                if (isset($activity->actionable->data[0])) {

                                    foreach ($activity->actionable->data as $data) {
                                        //dd($data);
                                        if ($data->client_id == $client_id) {
                                            if (isset($data->actionable_boolean_id)) {
                                                if ($data->actionable_boolean_id > 0) {
                                                    //$data2 = ActionableBooleanData::where('client_id',$data->client_id)->where('actionable_boolean_id', $data->actionable_boolean_id)->orderBy('id','desc')->take(1)->first();

                                                    if ($data->data == "1") {
                                                        $has_data = true;
                                                    }
                                                } else {
                                                    $has_data = false;
                                                }
                                            }

                                            if (isset($data->actionable_dropdown_id)) {
                                                //dd($data);
                                                if (isset($data->actionable_dropdown_item_id) && $data->actionable_dropdown_item_id > 0) {
                                                    $data->load('item');
                                                    $data2 = $data->item;
                                                    //$data2 = ActionableDropdownItem::where('id', $data->actionable_dropdown_item_id)->first();

                                                    if ($data2->name === "N/A" || $data2->name === "Yes") {
                                                        $has_data = true;
                                                    }
                                                } else {
                                                    $has_data = false;
                                                }
                                            }


                                        }
                                    }
                                }

                                if (!$has_data) {
                                    if ($activity->client_activity) {
                                        //$outstanding_activities[$activity->name]['client']++;
                                    } else {
                                        $outstanding_activities[$activity->name]['user']++;
                                    }
                                }
                            }
                        }
                        //}
                    }
                }
            }

            return $outstanding_activities;
        }

    public function getBCKOutstandingActivities1($process_id,$step_id)
    {
        //$configs = Config::first();
        $step = Step::where('id',$step_id)->first();

        $activity_ids = explode(',',$step->dashboard_outstanding_default);

        $process = Process::with(['steps.activities.actionable', 'clients'])->where('id',$process_id)->first();

        $clients = $process->clients->where('is_progressing',1)->where('step_id',$step_id)->pluck('id');

        $outstanding_activities = [];
        foreach ($process->steps as $step) {
            foreach ($step->activities as $activity) {
                if(($key = array_search($activity->id, $activity_ids)) === false) {

                } else {

                    if ($activity->step_id == $step_id) {

                        $outstanding_activities[$activity->name] = [
                            'user' => 0
                        ];
                        foreach ($clients as $client_id) {
                            $has_data = false;
                            if (isset($activity->actionable['data'][0])) {

                                foreach ($activity->actionable['data'] as $data) {
                                    //dd($data);
                                    if ($data->client_id == $client_id) {
                                        if (isset($data->actionable_boolean_id)) {
                                            if ($data->actionable_boolean_id > 0) {
                                                //$data2 = ActionableBooleanData::where('client_id',$data->client_id)->where('actionable_boolean_id', $data->actionable_boolean_id)->orderBy('id','desc')->take(1)->first();

                                                if ($data->data == "1") {
                                                    $has_data = true;
                                                }
                                            } else {
                                                $has_data = false;
                                            }
                                        }

                                        if (isset($data->actionable_dropdown_id)) {
                                            //dd($data);
                                            if (isset($data->actionable_dropdown_item_id) && $data->actionable_dropdown_item_id > 0) {
                                                //$data2 = ActionableDropdownItem::where('id', $data->actionable_dropdown_item_id)->first();
                                                $data->load('item');
                                                $data2 = $data->item;
                                                if ($data2->name === "N/A" || $data2->name === "Yes") {
                                                    $has_data = true;
                                                }
                                            } else {
                                                $has_data = false;
                                            }
                                        }


                                    }
                                }
                            }

                            if (!$has_data) {
                                if ($activity->client_activity) {
                                    //$outstanding_activities[$activity->name]['client']++;
                                } else {
                                    $outstanding_activities[$activity->name]['user']++;
                                }
                            }
                        }
                    }
                    //}
                }
            }
        }

        return $outstanding_activities;
    }

    public function getBCKOutstandingActivities2($process_id,$step_id)
    {
        //$configs = Config::first();
        $step = Step::where('id',$step_id)->first();

        $activity_ids = explode(',',$step->dashboard_outstanding_default);

        $process = Process::with(['steps.activities', 'clients'])->where('id',$process_id)->first();

        $clients = $process->clients->where('is_progressing',1)->where('step_id',$step_id)->pluck('id');

        $outstanding_activities = [];
        foreach ($process->steps as $step) {
            foreach ($step->activities as $activity) {
                if(($key = array_search($activity->id, $activity_ids)) === false) {

                } else {

                    if ($activity->step_id == $step_id) {

                        $outstanding_activities[$activity->name] = [
                            'user' => 0
                        ];
                        foreach ($clients as $client_id) {
                            $has_data = false;
                            if (isset($activity->actionable['data'][0])) {

                                foreach ($activity->actionable['data'] as $data) {
                                    //dd($data);
                                    if ($data->client_id == $client_id) {
                                        if (isset($data->actionable_boolean_id)) {
                                            if ($data->actionable_boolean_id > 0) {
                                                //$data2 = ActionableBooleanData::where('client_id',$data->client_id)->where('actionable_boolean_id', $data->actionable_boolean_id)->orderBy('id','desc')->take(1)->first();

                                                if ($data->data == "1") {
                                                    $has_data = true;
                                                }
                                            } else {
                                                $has_data = false;
                                            }
                                        }

                                        if (isset($data->actionable_text_id)) {
                                            if ($data->actionable_text_id > 0) {
                                                //$data2 = ActionableTextData::where('client_id',$data->client_id)->where('actionable_text_id', $data->actionable_text_id)->orderBy('id','desc')->take(1)->first();

                                                if ($data->data == "1") {
                                                    $has_data = true;
                                                }
                                            } else {
                                                $has_data = false;
                                            }
                                        }

                                        if (isset($data->actionable_dropdown_id)) {
                                            //dd($data);
                                            if (isset($data->actionable_dropdown_item_id) && $data->actionable_dropdown_item_id > 0) {
                                                //$data2 = ActionableDropdownItem::where('id', $data->actionable_dropdown_item_id)->first();
                                                $data->load('item');
                                                $data2 = $data->item;
                                                if ($data2->name === "N/A" || $data2->name === "Yes") {
                                                    $has_data = true;
                                                }
                                            } else {
                                                $has_data = false;
                                            }
                                        }


                                    }
                                }
                            }

                            if (!$has_data) {
                                if ($activity->client_activity) {
                                    //$outstanding_activities[$activity->name]['client']++;
                                } else {
                                    $outstanding_activities[$activity->name]['user']++;
                                }
                            }
                        }
                    }
                    //}
                }
            }
        }

        return $outstanding_activities;
    }

    public function getDavidOutstandingActivities1($process_id,$step_id)
    {
        //$configs = Config::first();
        $step = Step::where('id',$step_id)->first();

        $activity_ids = explode(',',$step->dashboard_outstanding_default);

        $process = Process::with(['steps.activities', 'clients'])->where('id',$process_id)->first();

        $clients = $process->clients->where('step_id',$step_id)->where('process_id', $process_id)->pluck('id');

        $outstanding_activities = [];
        foreach ($process->steps as $step) {
            foreach ($step->activities as $activity) {
                if(($key = array_search($activity->id, $activity_ids)) === false) {

                } else {

                    if ($activity->step_id == $step_id) {

                        $outstanding_activities[$activity->name] = [
                            'user' => 0
                        ];
                        foreach ($clients as $client_id) {
                            $has_data = false;
                            if (isset($activity->actionable['data'][0])) {

                                foreach ($activity->actionable['data'] as $data) {
                                    //dd($data);
                                    if ($data->client_id == $client_id) {
                                        if (isset($data->actionable_boolean_id)) {
                                            if ($data->actionable_boolean_id > 0) {
                                                //$data2 = ActionableBooleanData::where('client_id',$data->client_id)->where('actionable_boolean_id', $data->actionable_boolean_id)->orderBy('id','desc')->take(1)->first();

                                                if ($data->data == "1") {
                                                    $has_data = true;
                                                }
                                            } else {
                                                $has_data = false;
                                            }
                                        }

                                        if (isset($data->actionable_dropdown_id)) {
                                            //dd($data);
                                            if (isset($data->actionable_dropdown_item_id) && $data->actionable_dropdown_item_id > 0) {
                                                $data->load('item');
                                                $data2 = $data->item;
                                                //$data2 = ActionableDropdownItem::where('id', $data->actionable_dropdown_item_id)->first();

                                                if ($data2->name === "N/A" || $data2->name === "Yes") {
                                                    $has_data = true;
                                                }
                                            } else {
                                                $has_data = false;
                                            }
                                        }


                                    }
                                }
                            }

                            if (!$has_data) {
                                if ($activity->client_activity) {
                                    //$outstanding_activities[$activity->name]['client']++;
                                } else {
                                    $outstanding_activities[$activity->name]['user']++;
                                }
                            }
                        }
                    }
                    //}
                }
            }
        }

        return $outstanding_activities;
    }

    public function getDavidOutstandingActivities2($process_id,$step_id)
    {
        //$configs = Config::first();
        $step = Step::where('id',$step_id)->first();

        $activity_ids = explode(',',$step->dashboard_outstanding_default);

        $process = Process::with(['steps.activities', 'clients'])->where('id',$process_id)->first();

        $clients = $process->clients->where('is_progressing',1)->where('step_id',$step_id)->pluck('id');

        $outstanding_activities = [];
        foreach ($process->steps as $step) {
            foreach ($step->activities as $activity) {
                if(($key = array_search($activity->id, $activity_ids)) === false) {

                } else {

                    if ($activity->step_id == $step_id) {

                        $outstanding_activities[$activity->name] = [
                            'user' => 0
                        ];
                        foreach ($clients as $client_id) {
                            $has_data = false;
                            if (isset($activity->actionable['data'][0])) {

                                foreach ($activity->actionable['data'] as $data) {
                                    //dd($data);
                                    if ($data->client_id == $client_id) {
                                        if (isset($data->actionable_boolean_id)) {
                                            if ($data->actionable_boolean_id > 0) {
                                                //$data2 = ActionableBooleanData::where('client_id',$data->client_id)->where('actionable_boolean_id', $data->actionable_boolean_id)->orderBy('id','desc')->take(1)->first();

                                                if ($data->data == "1") {
                                                    $has_data = true;
                                                }
                                            } else {
                                                $has_data = false;
                                            }
                                        }

                                        if (isset($data->actionable_text_id)) {
                                            if ($data->actionable_text_id > 0) {
                                               // $data2 = ActionableTextData::where('client_id',$data->client_id)->where('actionable_text_id', $data->actionable_text_id)->orderBy('id','desc')->take(1)->first();

                                                if ($data->data == "1") {
                                                    $has_data = true;
                                                }
                                            } else {
                                                $has_data = false;
                                            }
                                        }

                                        if (isset($data->actionable_dropdown_id)) {
                                            //dd($data);
                                            if (isset($data->actionable_dropdown_item_id) && $data->actionable_dropdown_item_id > 0) {
                                                $data->load('item');
                                                $data2 = $data->item;

                                                if ($data2->name === "N/A" || $data2->name === "Yes") {
                                                    $has_data = true;
                                                }
                                            } else {
                                                $has_data = false;
                                            }
                                        }


                                    }
                                }
                            }

                            if (!$has_data) {
                                if ($activity->client_activity) {
                                    //$outstanding_activities[$activity->name]['client']++;
                                } else {
                                    $outstanding_activities[$activity->name]['user']++;
                                }
                            }
                        }
                    }
                    //}
                }
            }
        }

        return $outstanding_activities;
    }

    public function calendar()
    {
        return view('calendar');
    }

    public function getConvertedValue(Config $config, $clients){
        if($config->show_converted_currency_total != null && $config->show_converted_currency_total == '1') {
            $currency = Currency::where('id', $config->default_currency)->first()->symbol;

            $clients = $clients->get();
//dd($clients);
            $amount = 0;

            foreach ($clients as $client) {
                $client_id = $client->id;

                $activity = Activity::with(['actionable.data' => function ($q) use ($client_id) {
                    $q->where('client_id', $client_id);
                }])->where('id', $config->converted_currency_total_activity)->first();
//dd($activity);
                if (count($activity->actionable->data) > 0) {
                    //dd($activity->actionable->data);
                    foreach ($activity->actionable->data as $data) {
                        if($data["data"] != null) {
                            //dd($data["data"]);
                            $amount = $amount + $data["data"];

                        }
                    }
                }

            }
            $amount = $this->formatMoney($amount,true);

            return $currency . ' ' . $amount;
        }
    }

    public function formatMoney($number, $fractional=true) {
        if ($fractional) {
            $number = sprintf('%.2f', $number);
        }
        while (true) {
            $replaced = preg_replace('/(-?\d+)(\d\d\d)/', '$1,$2', $number);
            if ($replaced != $number) {
                $number = $replaced;
            } else {
                break;
            }
        }
        return $number;
    }

    public function getCompletedClientsAjax(Request $request)
    {
        $process_id = ($request->has('process_id')) ? $request->get('process_id') : 0;
        $process = Process::find($process_id);

        $range = ($request->has('range')) ? $request->get('range') : 'week';

        if($request->get('months') == 'custom') {
            $from_string = Carbon::parse($request->get('f'))->format('Y-m-d');
            $to_string =Carbon::parse($request->get('t'))->format('Y-m-d');
        }

        if($request->get('months') == 'current') {
            $from_string = Carbon::parse(now())->startOfYear()->format('Y-m-d');
            $to_string = Carbon::parse(now())->endOfYear()->format('Y-m-d');
        }

        if($request->get('months') == 'all') {
            $first_client = Client::where('process_id',$process_id)->orderBy('created_at','asc')->first()->created_at;

            $from_string = Carbon::parse($first_client)->format('Y-m-d');
            $to_string = Carbon::parse(now())->format('Y-m-d');
        }

        if($request->get('months') != 'all' && $request->get('months') != 'current' && $request->get('months') != 'custom' ) {
            $from_string = Carbon::parse(now())->subMonth($request->get('months'))->format('Y-m-d');
            $to_string = Carbon::parse(now())->format('Y-m-d');
        }

        $from = Carbon::parse($from_string);
        $to = Carbon::parse($to_string);

        switch ($range) {
            default:
            case 'day':
                $date_diff = $from->diffInDays($to);

                $client_query = Client::where('step_id',5)->where('process_id', $process->id)->where('is_progressing', '=', 1)->whereDate('completed_at','>=',$from)->whereDate('completed_at','<=',$to)->where('completed_at','!=', null)->select(DB::raw('DATE_FORMAT(completed_at, "%e %M %Y") as date'), DB::raw('count(*) as onboarded'))->groupBy('date')->pluck('onboarded', 'date')->toArray();

                $client_onboards = [];
                for ($i = 0; $i <= $date_diff; $i++) {
                    $working_date = $from->copy()->addDays($i)->format('j F Y');
                    if (isset($client_query[$working_date])) {
                        $client_onboards[$working_date] = $client_query[$working_date];
                    } else {
                        $client_onboards[$working_date] = 0;
                    }
                }

                break;
            case 'week':
                $date_diff = $from->diffInWeeks($to->addDay(1));

                $client_query = Client::where('step_id',5)->where('process_id', $process->id)->where('is_progressing', '=', 1)->whereDate('completed_at','>=',$from)->whereDate('completed_at','<=',$to)->where('completed_at','!=', null)->select(DB::raw('DATE_FORMAT(completed_at, "%u %x") as date'), DB::raw('count(*) as onboarded'))->groupBy('date')->pluck('onboarded', 'date')->toArray();

                $client_onboards = [];
                for ($i = 0; $i <= $date_diff; $i++) {

                    $readable_date = $from->copy()->startOfWeek()->addWeeks($i)->format('j F Y');
                    $working_date = $from->copy()->startOfWeek()->addWeeks($i)->format('W Y');
                    if (isset($client_query[$working_date])) {
                        $client_onboards[$readable_date] = $client_query[$working_date];
                    } else {
                        $client_onboards[$readable_date] = 0;
                    }
                }

                break;
            case 'month':
                $date_diff = $from->diffInMonths($to);

                $client_query = Client::where('step_id',5)->where('process_id', $process->id)->where('is_progressing', '=', 1)->whereDate('completed_at','>=',$from)->whereDate('completed_at','<=',$to)->where('completed_at','!=', null)->select(DB::raw('DATE_FORMAT(updated_at, "%e %M %Y") as date'), DB::raw('count(*) as onboarded'))->groupBy('date')->pluck('onboarded', 'date')->toArray();

                $client_onboards = [];
                for ($i = 0; $i <= $date_diff; $i++) {
                    $working_date = $from->copy()->addMonths($i)->format('F Y');
                    if (isset($client_query[$working_date])) {
                        $client_onboards[$working_date] = $client_query[$working_date];
                    } else {
                        $client_onboards[$working_date] = 0;
                    }
                }

                break;
            case 'year':
                $date_diff = $from->diffInYears($to);

                $client_query = Client::where('step_id',5)->where('process_id', $process->id)->where('is_progressing', '=', 1)->where('is_qa','0')->whereDate('completed_at','>=',$from)->whereDate('completed_at','<=',$to)->where('completed_at','!=', null)->select(DB::raw('DATE_FORMAT(updated_at, "%e %M %Y") as date'), DB::raw('count(*) as onboarded'))->groupBy('date')->pluck('onboarded', 'date')->toArray();

                $client_onboards = [];
                for ($i = 0; $i <= $date_diff; $i++) {
                    $working_date = $from->copy()->addYears($i)->format('Y');
                    if (isset($client_query[$working_date])) {
                        $client_onboards[$working_date] = $client_query[$working_date];
                    } else {
                        $client_onboards[$working_date] = 0;
                    }
                }
                break;
        }

        return json_encode($client_onboards);
    }

}
