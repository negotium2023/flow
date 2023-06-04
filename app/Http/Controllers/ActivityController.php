<?php

namespace App\Http\Controllers;

use App\ActionableBooleanData;
use App\ActionableDropdownItem;
use App\Activity;
use App\ActivityRule;
use App\ActivityVisibilityRule;
use App\ClientProcess;
use App\Currency;
use App\Step;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function getRules(Request $request){

        $message = array();

        $activeprocesses = ClientProcess::select('process_id')->where('client_id',$request->input('client_id'))->get();

        $steps = Step::with('activities')->where('id',$request->input('step_id'))->get();



        foreach ($steps as $step){
            foreach ($step['activities'] as $activity){
                if(($request->has($activity->id) && $request->input($activity->id) != '') && ($request->input('old_'.$activity->id) != $request->input($activity->id))) {
                    $rules = ActivityRule::where('activity_id', $activity->id)->whereNotIn('activity_process',$activeprocesses)->get();

                    switch($activity->actionable_type){
                        case 'App\ActionableDropdown':
                            $datas = ActionableDropdownItem::whereIn('id',$request->input($activity->id))->get();

                            foreach ($datas as $data) {
                                $val = $data->name;

                                foreach ($rules as $rule) {
                                    if ($val == $rule->activity_value) {
                                        array_push($message, [$rule->id => $rule->process->name]);
                                    }
                                }
                            }
                            break;
                        case 'App\ActionableBoolean':

                                $val = $request->input($activity->id);

                                foreach ($rules as $rule) {
                                    if ($val == $rule->activity_value) {
                                        array_push($message, [$rule->id => $rule->process->name]);
                                    }
                                }
                            break;
                        default:
                            $val = $request->input($activity->id);

                            foreach ($rules as $rule) {
                                if($val == $rule->activity_value) {
                                    array_push($message, [$rule->id => $rule->process->name]);
                                }
                            }
                            break;
                    }


                }

            }
        }

        if(count($message) > 0) {
            return response()->json(['result' => 'success', 'message' => $message]);
        } else {
            return response()->json(['result' => 'error', 'message' => $message]);
        }
    }

    public function getRule(Request $request){

        $rule = ActivityRule::where('id',$request->input('id'))->first();

        return response()->json(['result'=>'success','process_id' => $rule->activity_process,'step_id' => $rule->activity_step]);
    }

    public function getActivities(Request $request){
        $activities = Activity::select('order')->where('id',$request->input('activity_id'))->first();
        $prec_activities = Activity::where('step_id',$request->input('step_id'))->where('id','!=',$request->input('activity_id'))->where('order','<',$activities->order)->orderBy('order')->pluck('name','id');

        return response()->json(['result'=>'success','activities' => $prec_activities]);
    }

    public function getStepActivities(Request $request){


        $activities = Activity::where('step_id',$request->input('step_id'))->where('actionable_type',$this->getActionableType($request->input('atype')))->orderBy('order')->get();

        $activity = [];

        foreach ($activities as $p){
            $activity[$p->id] = $p->name;
        }
        return $activity;
    }

    public function getActivityType(Request $request){
        $activity = Activity::find($request->input('activity_id'));
        $type = $activity->getTypeName();

        return response()->json(['result'=>'success','activity_type' => $type]);
    }

    public function getDependantActivities(Request $request){

        $activity = Activity::find($request->input('activity_id'));

        if(strpos($request->input('activity_value'),',') !== false && strlen($request->input('activity_value') > 1)){
            if(isset($activity) && $activity->getTypeName() == 'dropdown') {
                $vars = explode(',', $request->input('activity_value'));

                $dropdownitems = ActionableDropdownItem::select('name')->whereIn('id', $vars)->get();

                $activities = ActivityVisibilityRule::select('activity_id')->where('preceding_activity', $request->input('activity_id'))->whereIn('activity_value', collect($dropdownitems)->toArray())->get();
                if (count($activities) > 0) {
                    $nonactivities = ActivityVisibilityRule::select('activity_id')->where('preceding_activity', $request->input('activity_id'))->whereNotIn('activity_value', collect($dropdownitems)->toArray())->whereNotIn('activity_id', collect($activities)->toArray())->get();
                } else {
                    $nonactivities = ActivityVisibilityRule::select('activity_id')->where('preceding_activity', $request->input('activity_id'))->whereNotIn('activity_value', collect($dropdownitems)->toArray())->get();
                }
            }
        } else {
            if(isset($activity) && $activity->getTypeName() == 'dropdown') {
                $dropdownitems = ActionableDropdownItem::where('id', $request->input('activity_value'))->first();
            }

            if(isset($dropdownitems)) {
                $activities = ActivityVisibilityRule::select('activity_id')->where('preceding_activity', $request->input('activity_id'))->where('activity_value', $dropdownitems->name)->get();
                if(count($activities) > 0) {
                    $nonactivities = ActivityVisibilityRule::select('activity_id')->where('preceding_activity', $request->input('activity_id'))->where('activity_value','!=', $dropdownitems->name)->whereNotIn('activity_id', collect($activities)->toArray())->get();
                } else {
                    $nonactivities = ActivityVisibilityRule::select('activity_id')->where('preceding_activity', $request->input('activity_id'))->where('activity_value','!=',$dropdownitems->name)->get();
                }
            } else {
                $activities = ActivityVisibilityRule::select('activity_id')->where('preceding_activity',$request->input('activity_id'))->where('activity_value',$request->input('activity_value'))->get();
                if(count($activities) > 0) {
                    $nonactivities = ActivityVisibilityRule::select('activity_id')->where('preceding_activity', $request->input('activity_id'))->where('activity_value','!=', $request->input('activity_value'))->whereNotIn('activity_id', collect($activities)->toArray())->get();
                } else {
                    $nonactivities = ActivityVisibilityRule::select('activity_id')->where('preceding_activity', $request->input('activity_id'))->where('activity_value','!=', $request->input('activity_value'))->get();
                }
            }
        }


        $prec_activities = [];
        if(isset($activities)) {
            foreach ($activities as $activity) {
                if(!in_array($activity->activity_id,$prec_activities,true)) {
                    array_push($prec_activities, $activity->activity_id);
                }
                /*ActivityVisibilityRule::where('id',$activity->id)->update(['visible'=>1]);*/
            }
        }

        $prec_nonactivities = [];
        if($nonactivities) {
            foreach ($nonactivities as $nonactivity) {
                if(!in_array($nonactivity->activity_id,$prec_nonactivities,true)) {
                    array_push($prec_nonactivities, $nonactivity->activity_id);
                }
                /*ActivityVisibilityRule::where('id',$nonactivity->id)->update(['visible'=>0]);*/
            }
        }

        return response()->json(['result'=>'success','activities' => $prec_activities,'nonactivities' => $prec_nonactivities]);
    }

    public function getDropdownItems(Request $request){
        $activity = Activity::find($request->input('activity_id'));

        return response()->json(['result'=>'success','dropdownitems' => $activity->actionable->items->pluck('name')->toArray()]);
    }

    public function getDropdownText(Request $request){
        $activity = ActionableDropdownItem::where('id',$request->input('option'))->first();

        return response()->json($activity->name);
    }

    public function getActionableType($type)
    {
        //activity type hook
        switch ($type) {
            case 'heading':
                return 'App\ActionableHeading';
                break;
            case 'subheading':
                return 'App\ActionableSubheading';
                break;
            case 'content':
                return 'App\ActionableContent';
                break;
            case 'text':
                return 'App\ActionableText';
                break;
            case 'textarea':
                return 'App\ActionableTextarea';
                break;
            case 'percentage':
                return 'App\ActionablePercentage';
                break;
            case 'integer':
                return 'App\ActionableInteger';
                break;
            case 'amount':
                return 'App\ActionableAmount';
                break;
            case 'videoyoutube':
                return 'App\ActionableVideoYoutube';
                break;
            case 'videoupload':
                return 'App\ActionableVideoUpload';
                break;
            case 'imageupload':
                return 'App\ActionableImageUpload';
                break;
            case 'template_email':
                return 'App\ActionableTemplateEmail';
                break;
            case 'document_email':
                return 'App\ActionableDocumentEmail';
                break;
            case 'document':
                return 'App\ActionableDocument';
                break;
            case 'dropdown':
                return 'App\ActionableDropdown';
                break;
            case 'date':
                return 'App\ActionableDate';
                break;
            case 'boolean':
                return 'App\ActionableBoolean';
                break;
            case 'notification':
                return 'App\ActionableNotification';
                break;
            case 'multiple_attachment':
                return 'App\ActionableMultipleAttachment';
            default:
                abort(500, 'Error');
                break;
        }
    }

    public function getCurrencies(){
        //
        $currencies = Currency::select('id','symbol','currency')->get();

        $currency_array = array();

        /*foreach ($currencies as $currency){
            array_push($currency_array,[
                'id' => $currency->id,
                'symbol' => $currency->symbol
            ]);
        }*/
        return Currency::select('id','symbol','currency')->where('status','1')->pluck('symbol','id');
    }
}
