<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Config;
use App\Http\Requests\StoreConfigRequest;
use App\Step;
use App\Process;
use http\Env\Request;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\Theme;

class ConfigController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        //$d = Activity::with('actionable.data')->orderBy('id','asc')->get();

        $parameters = [
            'config'=>Config::first(),
            'process' => Process::orderBy('name','asc')->where('process_type_id','1')->pluck('name','id'),
            'related_party_process' => Process::orderBy('name','asc')->where('process_type_id','2')->pluck('name','id'),
            'activities' => Activity::with('actionable.data')->orderBy('id','asc')->pluck('name','id')->prepend('Please Select','0'),
            'steps' => array(),
        ];



        return view('configs.index')->with($parameters);
    }

    public function createTheme(){
        $theme =  app('App\Theme')->whereUserId(auth()->id())->first();
        return view('configs.theme')->with(['theme' => $theme]);
    }

    public function storeTheme(){

        $theme = new \App\Theme();
        $theme->user_id = auth()->id();
        $theme->primary = \request()->primary;
        $theme->secondary = \request()->secondary;
        $theme->active = \request()->active;
        $theme->sidebar_background = \request()->sidebar_background;
        $theme->sidebar_text = \request()->sidebar_text;
        $theme->save();

        return redirect()->back()->with('flash_success', 'Your theme has been changed successfully');
    }

    public function updateTheme(\App\Theme $theme){

        $theme->user_id = auth()->id();
        $theme->primary = \request()->primary;
        $theme->secondary = \request()->secondary;
        $theme->active = \request()->active;
        $theme->sidebar_background = \request()->sidebar_background;
        $theme->sidebar_text = \request()->sidebar_text;
        $theme->save();

        return redirect()->back()->with('flash_success', 'Your theme has been changed successfully');
    }

    public function update(StoreConfigRequest $request)
    {
        $config = Config::first();
        $config->onboard_days = $request->input('onboard_days');
        $config->onboards_per_day = $request->input('onboards_per_day');
        $config->client_target_data = $request->input('client_target_data');
        $config->client_converted = $request->input('client_converted');
        $config->client_conversion = $request->input('client_conversion');
        $config->action_threshold = $request->input('action_threshold');
        $config->default_onboarding_process = $request->input('default_onboarding_process');
        $config->message_subject = $request->input('message_subject');
        $config->enable_support = $request->input('enable_support');
        $config->support_email = $request->input('support_email');
        $config->absolute_path = $request->input('absolute_path');
        $config->dashboard_process = $request->input('dashboard_process');
        $config->related_party_process = $request->input('related_party_process');
        if($request->has('dashboard_regions')) {
            $config->dashboard_regions = '';
        }
        if($request->has('outstanding_activities')) {
            $config->dashboard_outstanding_activities = '';
        }
        if($request->has('dashboard_avg_step_lead')) {
            $config->dashboard_avg_step = '';
        }
        $config->dashboard_outstanding_step = $request->input('outstanding_step');
        $config->dashboard_activities_step_for_age = $request->input('dashboard_activities_step_for_age');
        $config->save();

        $config = Config::first();
        if($request->has('dashboard_regions')) {
            foreach($request->input('dashboard_regions') as $pref):
                if($config->dashboard_regions == '')
                    $config->dashboard_regions = $pref;
                else
                    $config->dashboard_regions .= ",".$pref;
            endforeach;
        }
        if($request->has('outstanding_activities')) {
            foreach($request->input('outstanding_activities') as $pref):
                if($config->dashboard_outstanding_activities == '')
                    $config->dashboard_outstanding_activities = $pref;
                else
                    $config->dashboard_outstanding_activities .= ",".$pref;
            endforeach;
        }
        if($request->has('dashboard_avg_step_lead')) {
            foreach ($request->input('dashboard_avg_step_lead') as $pref):
                if ($config->dashboard_avg_step == '')
                    $config->dashboard_avg_step = $pref;
                else
                    $config->dashboard_avg_step .= "," . $pref;
            endforeach;
        }
        $config->save();

        return redirect()->back()->with('flash_success', 'Configs updated successfully');
    }

    public function getProcessSteps($process_id){

        $configs = Config::first();

        $step_ids = explode(',',$configs->dashboard_regions);

        $steps = Step::where('process_id',$process_id)->orderBy('order','asc');
        $selected_steps = Step::select('id')->where('process_id',$process_id)->whereIn('id',$step_ids)->orderBy('order','asc');

        $steps = $steps->get();
        //$selected_steps = $selected_steps->get()->toArray();

        $process_steps = array();
        //$selected = array();
        //dd($selected_steps);
        foreach($steps as $step){
            if(($key = array_search($step->id, $step_ids)) === false) {
                //if(in_array($step->id,$selected_steps)) {
                array_push($process_steps, [
                    'id' => $step->id,
                    'name' => $step->name,
                    'selected' => '0'
                ]);
            } else {
                array_push($process_steps, [
                    'id' => $step->id,
                    'name' => $step->name,
                    'selected' => '1'
                ]);
            }
        }

        return response()->json($process_steps);
    }

    public function getProcessAvgSteps($process_id){

        $configs = Config::first();

        $step_ids = explode(',',$configs->dashboard_avg_step);

        $steps = Step::where('process_id',$process_id)->orderBy('order','asc');
        $selected_steps = Step::select('id')->where('process_id',$process_id)->whereIn('id',$step_ids)->orderBy('order','asc');

        $steps = $steps->get();
        //$selected_steps = $selected_steps->get()->toArray();

        $process_steps = array();
        //$selected = array();
        //dd($selected_steps);
        foreach($steps as $step){
            if(($key = array_search($step->id, $step_ids)) === false) {
                //if(in_array($step->id,$selected_steps)) {
                array_push($process_steps, [
                    'id' => $step->id,
                    'name' => $step->name,
                    'selected' => '0'
                ]);
            } else {
                array_push($process_steps, [
                    'id' => $step->id,
                    'name' => $step->name,
                    'selected' => '1'
                ]);
            }
        }

        return response()->json($process_steps);
    }

    public function getOutstandingStep($process_id){

        $configs = Config::first();

        $steps = Step::where('process_id',$process_id)->orderBy('order','asc')->get();


        $process_steps = array();

        foreach($steps as $step){
            if($step->id == $configs->dashboard_outstanding_step) {

                array_push($process_steps, [
                    'id' => $step->id,
                    'name' => $step->name,
                    'selected' => '1'
                ]);

            } else {

                array_push($process_steps, [
                    'id' => $step->id,
                    'name' => $step->name,
                    'selected' => '0'
                ]);

            }
        }

        return response()->json($process_steps);
    }

    public function getOutstandingActivities($step_id){

        $configs = Config::first();

        $activity_ids = explode(',',$configs->dashboard_outstanding_activities);

        $activities = Activity::where('step_id',$step_id)->orderBy('order','asc')->get();


        $step_activities = array();

        foreach($activities as $activity){
            if(($key = array_search($activity->id, $activity_ids)) === false) {

                array_push($step_activities, [
                    'id' => $activity->id,
                    'name' => $activity->name,
                    'selected' => '0'
                ]);

            } else {

                array_push($step_activities, [
                    'id' => $activity->id,
                    'name' => $activity->name,
                    'selected' => '1'
                ]);

            }
        }

        return response()->json($step_activities);
    }

    public function getProcessStepToCalculateAgeFrom($process_id){

        $configs = Config::first();

        $steps = Step::where('process_id',$process_id)->orderBy('order','asc')->get();


        $process_steps = array();

        foreach($steps as $step){
            if($step->id == $configs->dashboard_activities_step_for_age) {

                array_push($process_steps, [
                    'id' => $step->id,
                    'name' => $step->name,
                    'selected' => '1'
                ]);

            } else {

                array_push($process_steps, [
                    'id' => $step->id,
                    'name' => $step->name,
                    'selected' => '0'
                ]);

            }
        }

        return response()->json($process_steps);
    }
}
