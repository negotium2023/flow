<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Kyslik\ColumnSortable\Sortable;
// use AustinHeap\Database\Encryption\Traits\HasEncryptedAttributes;

class Client extends Model
{
    use SoftDeletes;
    use Sortable;
    // use HasEncryptedAttributes;

    protected $encrypted = ['company','first_name','last_name','email','contact','cif_code','id_number','çompany_registration_number'];
    protected $dates = ['completed_at','deleted_at'];

    /*public $hidden = ['hash_first_name','hash_last_name','hash_company','hash_id_number','hash_email','hash_contact','hash_company_registration_number'];*/
    public $sortable = ['cif_code','contact','case_number','instruction_date','process_id','completed_days','created_at','step_id', 'consultant_id'];


    /*public function __construct(array $attributes = array(), bool $unHide = false)
    {
        parent::__construct($attributes);
        if ($unHide) {
            $this->hidden = [];
        }
    }*/

    public function unHide(){
        $this->hidden = [];
    }


    /**
     * Returns the company name.
     *
     * Should the company name field be empty,
     * return the concatenated first and last name.
     *
     * @return string
     */
    public function company_name() {
        $company = $this->company;

        return ($company) ?: $this->first_name." ".$this->last_name;
    }

    public function parent() {
        return $this->belongsTo('App\Client','parent_id')
            ->select('*',DB::raw('IF(`company` != "",`company`,CONCAT(first_name," ",COALESCE(`last_name`,""))) as name') );
    }

    public function fund_consultant(){
        $id =  $this->id;

        //$fund_consultant = DB::select("select b.data as 'name' from form_input_texts a inner join form_input_text_data b on b.form_input_text_id = a.id where b.client_id = '" . $id . "' and b.form_input_text_id = '418' order by b.id desc limit 1");
        $fund_consultant = FormInputTextData::where('client_id',$id)->where('form_input_text_id','418')->orderBy('id','desc')->limit(1)->first();
//dd($fund_consultant);
        return (!$fund_consultant) ?: $fund_consultant["name"];

        //return $this->belongsTo(FormInputTextData::class, 'client_id')->where('client_id',$id)->where('form_input_text_id','418')->orderBy('id','desc')->limit(1)->first();
    }

    public function referrer()
    {
        return $this->belongsTo('App\Referrer', 'referrer_id');
    }

    public function trigger()
    {
        return $this->belongsTo('App\TriggerType', 'trigger_type_id');
    }

    public function introducer()
    {
        return $this->belongsTo(User::class, 'introducer_id')->withTrashed();
    }

    public function consultant()
    {
        return $this->belongsTo(User::class, 'consultant_id')->withTrashed();
    }

    public function completedby()
    {
        return $this->belongsTo(User::class, 'completed_by')->withTrashed();
    }

    public function committee()
    {
        return $this->belongsTo(Committee::class, 'committee_id')->withTrashed();
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id')->withTrashed();
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function step()
    {
        return $this->belongsTo(Step::class, 'step_id')->withTrashed();
    }

    public function process()
    {
        return $this->belongsTo(Process::class);
    }

    public function processes()
    {
        return $this->hasMany('App\ClientProcess','client_id','id');
    }

    public function documents()
    {
        return $this->hasMany('App\Document')->orderBy('name')->orderBy('created_at','desc');
    }

    public function forms(){
        return $this->hasMany('App\ClientForm');
    }

    public function crfforms(){
        return $this->hasMany('App\ClientCRFForm');
    }

    public function client_forms()
    {
        return $this->hasMany('App\ClientForm');
    }

    public function comments()
    {
        return $this->hasMany('App\ClientComment');
    }

    public function business_unit()
    {
        return $this->hasOne('App\BusinessUnits', 'id', 'business_unit_id');
    }

    public function messages()
    {
        return $this->hasMany('App\Whatsapp');
    }

    public function crm(){
        return $this->belongsTo(Forms::class, 'crm_id');
    }

    public function getProcessProgress(Step $step)
    {
        $id = $this->id;

        $step->load(['activities.actionable.data' => function ($query) use ($id) {
            $query->where('client_id', $id);
            $query->orderBy('created_at','desc');
        }]);

        $process_progress = [];

        $step_array = [
            'id' => $step->id,
            'name' => $step->name,
            'order' => $step->order,
            'stage' => 0,
            'activities' => []
        ];

        array_push($process_progress, $step_array);

        foreach ($step->activities /*$step->activities*/ as $activity) {
            $activity_array = [
                'id' => $activity->id,
                'name' => $activity->name,
                'order' => $activity->order,
                'type' => $activity->getTypeName(),
                'type_display' => $activity->getTypeDisplayName(),
                'stage' => 0,
                'dependant_activity_id' => $activity->dependant_activity_id,
                'kpi' => $activity->kpi,
                'comment' => $activity->comment,
                'currency' => ($activity->getTypeDisplayName() == "Amount" && $activity->currency_type > 0 ? Currency::where('id',$activity->currency_type)->first()->id : Config::first()->default_currency),
                'avalue' => $activity->value,
                'tooltip' => $activity->tooltip,
                'procedure' => $activity->procedure,
                'grouped' => $activity->grouped,
                'grouping' => $activity->grouping,
                'default_value' => $activity->default_value,
                'client_bucket' => $activity->client_bucket,
                'level' => ($activity->level != 0 ? 100-($activity->level*5) : '100'),
                'position' => ($activity->position != null ? $activity->position : '0'),
                'color' => $activity->color,
                'future_date' => $activity->future_date,
                'due_date' => 0,
                'height' => $activity->height,
                'width' => $activity->width,
                'alignment' => $activity->alignment,
                'text_content' => $activity->text_content,
                'multiple_selection' => $activity->multiple_selection,
                'styles' => ActivityStyle::where('activity_id',$activity->id)->first(),
                'mirror_value' => $activity->getActivityMirrorValue($activity->id,$id)
            ];

            if ($activity_array['type'] == 'dropdown') {
                $activity_array['dropdown_items'] = $activity->actionable->items->pluck('name', 'id')->toArray();
                $activity_array['dropdown_values'] = ActionableDropdownData::where('actionable_dropdown_id',$activity->actionable_id)->where('client_id',$id)->orderBy('created_at','asc')->pluck('actionable_dropdown_item_id', 'id')->toArray();
            }

            /*if (is_null($activity->dependant_activity_id)) {
                $activity_array['stage'] = 1;
            }*/

            if ($step->id <= $this->step_id) {
                $activity_array['stage'] = 1;
            }

            if (isset($activity->actionable['data'][0])) {
                $activity_array['stage'] = 2;
                $data_index = count($activity->actionable['data']) -1;
                //dd($activity->actionable['data']);
                switch ($activity_array['type']) {
                    //get last not zero
                    case 'boolean':
                        $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                        $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                    case 'notification':
                        $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                        $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                    case 'date':
                        $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                        $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                    case 'text':
                        $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                        $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                        break;
                    case 'percentage':
                        $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                        $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                        break;
                    case 'integer':
                        $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                        $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                        break;
                    case 'amount':
                        $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                        $activity_array['currency'] = Currency::where('id',$activity->currency_type)->first()->symbol;
                        $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                        break;
                    case 'template_email':
                        $activity_array['value'] = $activity->actionable['data'][$data_index]->template_id;
                        $activity_array['email'] = $activity->actionable['data'][$data_index]->email;
                        $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                        break;
                    case 'document_email':
                        $activity_array['value'] = $activity->actionable['data'][$data_index]->document_id;
                        $activity_array['email'] = $activity->actionable['data'][$data_index]->email;
                        $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                        break;
                    case 'document':
                        $activity_array['value'] = $activity->actionable['data'][$data_index]->document_id;
                        $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                        break;
                    case 'dropdown':
                        $activity_array['value'] = $activity->actionable['data'][$data_index]->actionable_dropdown_item_id;
                        $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                        break;
                }
            }
            $process_progress[0]['activities'][$activity->id] = $activity_array;
        }

        foreach ($step->activities/*$step['activities']*/ as $activity_key => $activity) {
            if (!is_null($activity['dependant_activity_id']) && $activity['stage'] == 0) {
                $working_dependancy_id = $activity['dependant_activity_id'];
                foreach ($process_progress as $searched_step_key => $searched_step) {
                    if (isset($searched_step['activities'][$working_dependancy_id])) {
                        if ($searched_step['activities'][$working_dependancy_id]['stage'] == 2) {
                            $process_progress[$step->id]['activities'][$activity_key]['stage'] = 1;
                        } else {
                            if (!is_null($searched_step['activities'][$working_dependancy_id]['dependant_activity_id'])) {
                                $working_dependancy_id = $searched_step['activities'][$working_dependancy_id]['dependant_activity_id'];
                            }
                        }
                    }
                }
            }
        }

        $completed = true;
        $started = false;
        foreach ($step->activities/*$step['activities']*/ as $activity_key => $activity) {
            /*if ($activity['stage'] == 1) {
                $started = true;
            }*/

            if ($step->id == $this->step_id) {
                $started = true;
                //dd($activity);
            }

            if ($activity['kpi'] && $activity['stage'] != 2) {
                $completed = false;
            }

        }

        if ($started) {
            $process_progress[0]['stage'] = 1;
        }

        if ($completed) {
            $process_progress[0]['stage'] = 2;
            //dd($activity);
        }

        return $process_progress;
    }

    public function getClientActivities()
    {
        $id = $this->id;


//dd($client_basket_activities);
        $steps = Step::with('activities')->get();

        //dd($steps);
        $client_basket = [];

        foreach ($steps as $step) {
            $process_progress = [];

            $step_array = [
                'id' => $step->id,
                'name' => $step->name,
                'order' => $step->order,
                'stage' => 0,
                'activities' => []
            ];

            array_push($process_progress, $step_array);

            foreach ($step->activities /*$step->activities*/ as $activity) {

                $activity_array = [
                    'id' => $activity->id,
                    'name' => $activity->name,
                    'order' => $activity->order,
                    'type' => $activity->getTypeName(),
                    'type_display' => $activity->getTypeDisplayName(),
                    'stage' => 0,
                    'due_date' => 0,
                    'dependant_activity_id' => $activity->dependant_activity_id,
                    'kpi' => $activity->kpi,
                    'avalue' => $activity->value,
                    'procedure' => $activity->procedure,
                    'client_bucket' => $activity->client_bucket,
                    'level' => $activity->level,
                    'grouped' => $activity->grouped,
                    'tooltip' => $activity->tooltip
                ];

                if ($activity_array['type'] == 'dropdown') {
                    $activity_array['dropdown_items'] = $activity->actionable->items->pluck('name', 'id')->toArray();
                    $activity_array['dropdown_values'] = ActionableDropdownData::where('actionable_dropdown_id',$activity->actionable_id)->where('client_id',$id)->orderBy('created_at','asc')->pluck('actionable_dropdown_item_id', 'id')->toArray();
                }

                /*if (is_null($activity->dependant_activity_id)) {
                    $activity_array['stage'] = 1;
                }*/

                if ($step->id <= $this->step_id) {
                    $activity_array['stage'] = 1;
                }

                if (isset($activity->actionable['data'][0])) {
                    $activity_array['stage'] = 2;
                    $data_index = count($activity->actionable['data']) - 1;
                    //dd($activity->actionable['data']);
                    switch ($activity_array['type']) {
                        //get last not zero
                        case 'boolean':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                        case 'notification':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                        case 'date':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                        case 'text':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                            break;
                        case 'percentage':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                            break;
                        case 'integer':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                            break;
                        case 'amount':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                            $activity_array['currency'] = Currency::where('id',$activity->currency_type)->first()->symbol;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                            break;
                        case 'template_email':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->template_id;
                            $activity_array['email'] = $activity->actionable['data'][$data_index]->email;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                            break;
                        case 'document_email':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->document_id;
                            $activity_array['email'] = $activity->actionable['data'][$data_index]->email;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                            break;
                        case 'document':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->document_id;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                            break;
                        case 'dropdown':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->actionable_dropdown_item_id;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                            break;
                    }
                }
                $process_progress[0]['activities'][$activity->id] = $activity_array;
            }

            foreach ($step->activities/*$step['activities']*/ as $activity_key => $activity) {
                if (!is_null($activity['dependant_activity_id']) && $activity['stage'] == 0) {
                    $working_dependancy_id = $activity['dependant_activity_id'];
                    foreach ($process_progress as $searched_step_key => $searched_step) {
                        if (isset($searched_step['activities'][$working_dependancy_id])) {
                            if ($searched_step['activities'][$working_dependancy_id]['stage'] == 2) {
                                $process_progress[$step->id]['activities'][$activity_key]['stage'] = 1;
                            } else {
                                if (!is_null($searched_step['activities'][$working_dependancy_id]['dependant_activity_id'])) {
                                    $working_dependancy_id = $searched_step['activities'][$working_dependancy_id]['dependant_activity_id'];
                                }
                            }
                        }
                    }
                }
            }

            $completed = true;
            $started = false;
            foreach ($step->activities/*$step['activities']*/ as $activity_key => $activity) {
                /*if ($activity['stage'] == 1) {
                    $started = true;
                }*/

                if ($step->id == $this->step_id) {
                    $started = true;
                    //dd($activity);
                }

                if ($activity['kpi'] && $activity['stage'] != 2) {
                    $completed = false;
                }
            }

            if ($started) {
                $process_progress[0]['stage'] = 1;
            }

            if ($completed) {
                $process_progress[0]['stage'] = 2;
                //dd($activity);
            }

            array_push($client_basket,$process_progress);
        }

        return $client_basket;
    }

    public function getClientBasketActivities($process_id)
    {
        $id = $this->id;


//dd($client_basket_activities);
        $steps = Step::with(['activities.actionable.data' => function ($query) use ($id) {
            $query->where('client_id', $id);
            $query->orderBy('created_at','desc');
        }])->where('process_id',$process_id)->get();

        //dd($steps);
        $client_basket = [];

        foreach ($steps as $step) {
            $process_progress = [];

            $step_array = [
                'id' => $step->id,
                'name' => $step->name,
                'order' => $step->order,
                'stage' => 0,
                'activities' => []
            ];

            array_push($process_progress,$step_array);

            foreach ($step->activities /*$step->activities*/ as $activity) {

                $mirrors = $activity->getActivityMirrorValue($activity->id,$id);

                $activity_array = [
                    'id' => $activity->id,
                    'name' => $activity->name,
                    'order' => $activity->order,
                    'type' => $activity->getTypeName(),
                    'type_display' => $activity->getTypeDisplayName(),
                    'stage' => 0,
                    'dependant_activity_id' => $activity->dependant_activity_id,
                    'kpi' => $activity->kpi,
                    'comment' => $activity->comment,
                    'currency' => ($activity->getTypeDisplayName() == "Amount" && $activity->currency_type > 0 ? Currency::where('id',$activity->currency_type)->first()->id : Config::first()->default_currency),
                    'avalue' => $activity->value,
                    'tooltip' => $activity->tooltip,
                    'procedure' => $activity->procedure,
                    'grouped' => $activity->grouped,
                    'grouping' => $activity->grouping,
                    'default_value' => $activity->default_value,
                    'client_bucket' => $activity->client_bucket,
                    'level' => ($activity->level != 0 ? 100-($activity->level*5) : '100'),
                    'position' => ($activity->position != null ? $activity->position : '0'),
                    'color' => $activity->color,
                    'future_date' => $activity->future_date,
                    'due_date' => 0,
                    'height' => $activity->height,
                    'width' => $activity->width,
                    'alignment' => $activity->alignment,
                    'text_content' => $activity->text_content,
                    'multiple_selection' => $activity->multiple_selection,
                    'styles' => ActivityStyle::where('activity_id',$activity->id)->first(),
                    'mirror_count' => $mirrors["count"],
                    'mirror_value' => $mirrors["val"]
                ];

                if ($activity_array['type'] == 'dropdown') {
                    $activity_array['dropdown_items'] = $activity->actionable->items->pluck('name', 'id')->toArray();
                    $activity_array['dropdown_values'] = ActionableDropdownData::where('actionable_dropdown_id',$activity->actionable_id)->where('client_id',$id)->orderBy('created_at','asc')->pluck('actionable_dropdown_item_id', 'id')->toArray();
                }

                /*if (is_null($activity->dependant_activity_id)) {
                    $activity_array['stage'] = 1;
                }*/

                if ($step->id <= $this->step_id) {
                    $activity_array['stage'] = 1;
                }

                if (isset($activity->actionable['data'][0])) {
                    $activity_array['stage'] = 2;
                    $data_index = count($activity->actionable['data']) - 1;
                    //dd($activity->actionable['data']);
                    switch ($activity_array['type']) {
                        //get last not zero
                        case 'boolean':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                        case 'notification':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                        case 'date':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                        case 'text':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                            break;
                        case 'percentage':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                            break;
                        case 'integer':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                            break;
                        case 'amount':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                            $activity_array['currency'] = (Currency::where('id',$activity->currency_type)->first() ? Currency::where('id',$activity->currency_type)->first()->symbol : '');
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                            break;
                        case 'template_email':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->template_id;
                            $activity_array['email'] = $activity->actionable['data'][$data_index]->email;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                            break;
                        case 'document_email':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->document_id;
                            $activity_array['email'] = $activity->actionable['data'][$data_index]->email;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                            break;
                        case 'document':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->document_id;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                            break;
                        case 'dropdown':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->actionable_dropdown_item_id;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                            break;
                    }
                }
                $process_progress[0]['activities'][$activity->id] = $activity_array;
            }

            foreach ($step->activities/*$step['activities']*/ as $activity_key => $activity) {
                if (!is_null($activity['dependant_activity_id']) && $activity['stage'] == 0) {
                    $working_dependancy_id = $activity['dependant_activity_id'];
                    foreach ($process_progress as $searched_step_key => $searched_step) {
                        if (isset($searched_step['activities'][$working_dependancy_id])) {
                            if ($searched_step['activities'][$working_dependancy_id]['stage'] == 2) {
                                $process_progress[$step->id]['activities'][$activity_key]['stage'] = 1;
                            } else {
                                if (!is_null($searched_step['activities'][$working_dependancy_id]['dependant_activity_id'])) {
                                    $working_dependancy_id = $searched_step['activities'][$working_dependancy_id]['dependant_activity_id'];
                                }
                            }
                        }
                    }
                }
            }

            $completed = true;
            $started = false;
            foreach ($step->activities/*$step['activities']*/ as $activity_key => $activity) {
                /*if ($activity['stage'] == 1) {
                    $started = true;
                }*/

                if ($step->id == $this->step_id) {
                    $started = true;
                    //dd($activity);
                }

                if ($activity['kpi'] && $activity['stage'] != 2) {
                    $completed = false;
                }
            }

            if ($started) {
                $process_progress[0]['stage'] = 1;
            }

            if ($completed) {
                $process_progress[0]['stage'] = 2;
                //dd($activity);
            }

            $client_basket[$step->id][$step->name] =$process_progress;
        }

        return $client_basket;
    }

    public function getClientBasketDetails($form_id)
    {
        $id = $this->id;


//dd($client_basket_activities);
        $steps = FormSection::with(['activities.actionable.data' => function ($query) use ($id) {
            $query->where('client_id', $id);
            $query->orderBy('created_at','desc');
        }])->where('form_id',$form_id)->get();

        //dd($steps);
        $client_basket = [];

        foreach ($steps as $step) {
            $process_progress = [];

            $step_array = [
                'id' => $step->id,
                'name' => $step->name,
                'order' => $step->order,
                'stage' => 0,
                'activities' => []
            ];

            array_push($process_progress,$step_array);

            foreach ($step->activities /*$step->activities*/ as $activity) {

                $mirrors = $activity->getActivityMirrorValue($activity->id,$id);

                $activity_array = [
                    'id' => $activity->id,
                    'name' => $activity->name,
                    'order' => $activity->order,
                    'type' => $activity->getTypeName(),
                    'type_display' => $activity->getTypeDisplayName(),
                    'stage' => 0,
                    'dependant_activity_id' => $activity->dependant_activity_id,
                    'kpi' => $activity->kpi,
                    'comment' => $activity->comment,
                    'currency' => ($activity->getTypeDisplayName() == "Amount" && $activity->currency_type > 0 ? Currency::where('id',$activity->currency_type)->first()->id : Config::first()->default_currency),
                    'avalue' => $activity->value,
                    'tooltip' => $activity->tooltip,
                    'procedure' => $activity->procedure,
                    'grouped' => $activity->grouped,
                    'grouping' => $activity->grouping,
                    'default_value' => $activity->default_value,
                    'client_bucket' => $activity->client_bucket,
                    'level' => ($activity->level != 0 ? 100-($activity->level*5) : '100'),
                    'position' => ($activity->position != null ? $activity->position : '0'),
                    'color' => $activity->color,
                    'future_date' => $activity->future_date,
                    'due_date' => 0,
                    'height' => $activity->height,
                    'width' => $activity->width,
                    'alignment' => $activity->alignment,
                    'text_content' => $activity->text_content,
                    'multiple_selection' => $activity->multiple_selection,
                    'styles' => ActivityStyle::where('activity_id',$activity->id)->first(),
                    'mirror_count' => $mirrors["count"],
                    'mirror_value' => $mirrors["val"]
                ];

                if ($activity_array['type'] == 'dropdown') {
                    $activity_array['dropdown_items'] = $activity->actionable->items->pluck('name', 'id')->toArray();
                    $activity_array['dropdown_values'] = ActionableDropdownData::where('actionable_dropdown_id',$activity->actionable_id)->where('client_id',$id)->orderBy('created_at','desc')->pluck('actionable_dropdown_item_id', 'id')->toArray();
                }

                /*if (is_null($activity->dependant_activity_id)) {
                    $activity_array['stage'] = 1;
                }*/

                if ($step->id <= $this->step_id) {
                    $activity_array['stage'] = 1;
                }

                if (isset($activity->actionable['data'][0])) {
                    $activity_array['stage'] = 2;
                    $data_index = count($activity->actionable['data']) - 1;
                    //dd($activity->actionable['data']);
                    switch ($activity_array['type']) {
                        //get last not zero
                        case 'boolean':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                        case 'notification':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                        case 'date':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                        case 'text':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                            break;
                        case 'percentage':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                            break;
                        case 'integer':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                            break;
                        case 'amount':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                            $activity_array['currency'] = Currency::where('id',$activity->currency_type)->first()->symbol;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                            break;
                        case 'template_email':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->template_id;
                            $activity_array['email'] = $activity->actionable['data'][$data_index]->email;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                            break;
                        case 'document_email':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->document_id;
                            $activity_array['email'] = $activity->actionable['data'][$data_index]->email;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                            break;
                        case 'document':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->document_id;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                            break;
                        case 'dropdown':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->actionable_dropdown_item_id;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                            break;
                    }
                }
                $process_progress[0]['activities'][$activity->id] = $activity_array;
            }

            foreach ($step->activities/*$step['activities']*/ as $activity_key => $activity) {
                if (!is_null($activity['dependant_activity_id']) && $activity['stage'] == 0) {
                    $working_dependancy_id = $activity['dependant_activity_id'];
                    foreach ($process_progress as $searched_step_key => $searched_step) {
                        if (isset($searched_step['activities'][$working_dependancy_id])) {
                            if ($searched_step['activities'][$working_dependancy_id]['stage'] == 2) {
                                $process_progress[$step->id]['activities'][$activity_key]['stage'] = 1;
                            } else {
                                if (!is_null($searched_step['activities'][$working_dependancy_id]['dependant_activity_id'])) {
                                    $working_dependancy_id = $searched_step['activities'][$working_dependancy_id]['dependant_activity_id'];
                                }
                            }
                        }
                    }
                }
            }

            $completed = true;
            $started = false;
            foreach ($step->activities/*$step['activities']*/ as $activity_key => $activity) {
                /*if ($activity['stage'] == 1) {
                    $started = true;
                }*/

                if ($step->id == $this->step_id) {
                    $started = true;
                    //dd($activity);
                }

                if ($activity['kpi'] && $activity['stage'] != 2) {
                    $completed = false;
                }
            }

            if ($started) {
                $process_progress[0]['stage'] = 1;
            }

            if ($completed) {
                $process_progress[0]['stage'] = 2;
                //dd($activity);
            }

            $client_basket[$step->id][$step->name] =$process_progress;
        }

        return $client_basket;
    }

    public function getClientBasketActivitiesClientView($process_id,$activities)
    {
        $id = $this->id;


//dd($client_basket_activities);
        $steps = Step::with('activities')->where('process_id',$process_id)->get();

        //dd($steps);
        $client_basket = [];

        foreach ($steps as $step) {
            $i = 0;
            $process_progress = [];

            $step_array = [
                'id' => $step->id,
                'name' => $step->name,
                'order' => $step->order,
                'stage' => 0,
                'activities' => []
            ];

            array_push($process_progress, $step_array);

            foreach ($step->activities /*$step->activities*/ as $activity) {
                if (in_array($activity->id, $activities))
                    $i++;
                $activity_array = [
                    'id' => $activity->id,
                    'name' => $activity->name,
                    'order' => $activity->order,
                    'type' => $activity->getTypeName(),
                    'type_display' => $activity->getTypeDisplayName(),
                    'stage' => 0,
                    'due_date' => 0,
                    'dependant_activity_id' => $activity->dependant_activity_id,
                    'kpi' => $activity->kpi,
                    'avalue' => $activity->value,
                    'procedure' => $activity->procedure,
                    'client_bucket' => $activity->client_bucket,
                    'level' => $activity->level,
                    'grouped' => $activity->grouped,
                    'tooltip' => $activity->tooltip
                ];

                if ($activity_array['type'] == 'dropdown') {
                    $activity_array['dropdown_items'] = $activity->actionable->items->pluck('name', 'id')->toArray();
                    $activity_array['dropdown_values'] = ActionableDropdownData::where('actionable_dropdown_id',$activity->actionable_id)->where('client_id',$id)->orderBy('created_at','desc')->pluck('actionable_dropdown_item_id', 'id')->toArray();
                }

                /*if (is_null($activity->dependant_activity_id)) {
                    $activity_array['stage'] = 1;
                }*/

                if ($step->id <= $this->step_id) {
                    $activity_array['stage'] = 1;
                }

                if (isset($activity->actionable['data'][0])) {
                    $activity_array['stage'] = 2;
                    $data_index = count($activity->actionable['data']) - 1;
                    //dd($activity->actionable['data']);
                    switch ($activity_array['type']) {
                        //get last not zero
                        case 'boolean':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                        case 'notification':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                        case 'date':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                        case 'text':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                            break;
                        case 'template_email':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->template_id;
                            $activity_array['email'] = $activity->actionable['data'][$data_index]->email;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                            break;
                        case 'document_email':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->document_id;
                            $activity_array['email'] = $activity->actionable['data'][$data_index]->email;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                            break;
                        case 'document':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->document_id;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                            break;
                        case 'dropdown':
                            $activity_array['value'] = $activity->actionable['data'][$data_index]->actionable_dropdown_item_id;
                            $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                            break;
                    }
                }
                $process_progress[0]['activities'][$activity->id] = $activity_array;
            }
        }

        foreach ($step->activities/*$step['activities']*/ as $activity_key => $activity) {
            if (!is_null($activity['dependant_activity_id']) && $activity['stage'] == 0) {
                $working_dependancy_id = $activity['dependant_activity_id'];
                foreach ($process_progress as $searched_step_key => $searched_step) {
                    if (isset($searched_step['activities'][$working_dependancy_id])) {
                        if ($searched_step['activities'][$working_dependancy_id]['stage'] == 2) {
                            $process_progress[$step->id]['activities'][$activity_key]['stage'] = 1;
                        } else {
                            if (!is_null($searched_step['activities'][$working_dependancy_id]['dependant_activity_id'])) {
                                $working_dependancy_id = $searched_step['activities'][$working_dependancy_id]['dependant_activity_id'];
                            }
                        }
                    }
                }
            }
        }

        $completed = true;
        $started = false;
        foreach ($step->activities/*$step['activities']*/ as $activity_key => $activity) {
            /*if ($activity['stage'] == 1) {
                $started = true;
            }*/

            if ($step->id == $this->step_id) {
                $started = true;
                //dd($activity);
            }

            if ($activity['kpi'] && $activity['stage'] != 2) {
                $completed = false;
            }
        }

        if ($started) {
            $process_progress[0]['stage'] = 1;
        }

        if ($completed) {
            $process_progress[0]['stage'] = 2;
            //dd($activity);
        }

        if($i == 0) {
            $client_basket[$step->name] = $process_progress;
        }


        return $client_basket;
    }

    public function getProcessStepProgress(Step $step)
    {
        $id = $this->id;

        $step->load(['activities.actionable.data' => function ($query) use ($id) {
            $query->where('client_id', $id);
        }]);

        // dd($step);

        $process_progress = [];

        $step_array = [
            'id' => $step->id,
            'name' => $step->name,
            'order' => $step->order,
            'stage' => 0,
            'group' => $step->group,
            'activities' => []
        ];

        array_push($process_progress, $step_array);

        foreach ($step->activities /*$step->activities*/ as $activity) {
            $mirrors = $activity->getActivityMirrorValue($activity->id,$id);

            $activity_array = [
                'id' => $activity->id,
                'name' => $activity->name,
                'order' => $activity->order,
                'type' => $activity->getTypeName(),
                'type_display' => $activity->getTypeDisplayName(),
                'stage' => 0,
                'dependant_activity_id' => $activity->dependant_activity_id,
                'kpi' => $activity->kpi,
                'comment' => $activity->comment,
                'currency' => ($activity->getTypeDisplayName() == "Amount" && $activity->currency_type > 0 ? Currency::where('id',$activity->currency_type)->first()->id : Config::first()->default_currency),
                'avalue' => $activity->value,
                'tooltip' => $activity->tooltip,
                'procedure' => $activity->procedure,
                'grouped' => $activity->grouped,
                'grouping' => $activity->grouping,
                'default_value' => $activity->default_value,
                'client_bucket' => $activity->client_bucket,
                'level' => ($activity->level != 0 ? 100-($activity->level*5) : '100'),
                'position' => ($activity->position != null ? $activity->position : '0'),
                'color' => $activity->color,
                'future_date' => $activity->future_date,
                'due_date' => 0,
                'height' => $activity->height,
                'width' => $activity->width,
                'alignment' => $activity->alignment,
                'text_content' => $activity->text_content,
                'multiple_selection' => $activity->multiple_selection,
                'styles' => ActivityStyle::where('activity_id',$activity->id)->first(),
                'mirror_count' => $mirrors["count"],
                'mirror_value' => $mirrors["val"]
            ];

            if ($activity_array['type'] == 'dropdown') {
                $activity_array['dropdown_items'] = $activity->actionable->items->pluck('name', 'id')->toArray();
                // $activity_array['dropdown_values'] = $activity->actionable->valuess->where('client_id',$id)->pluck('actionable_dropdown_item_id', 'id')->toArray();
                $activity_array['dropdown_values'] = ActionableDropdownData::where('actionable_dropdown_id',$activity->actionable_id)->where('client_id',$id)->orderBy('created_at','desc')->pluck('actionable_dropdown_item_id', 'id')->toArray();
            }

            /*if (is_null($activity->dependant_activity_id)) {
                $activity_array['stage'] = 1;
            }*/

            if ($step->id <= $this->step_id) {
                $activity_array['stage'] = 1;
            }

            if (isset($activity->actionable['data'][0])) {
                $activity_array['stage'] = 2;
                $data_index = count($activity->actionable['data']) -1;
                switch ($activity_array['type']) {
                    //get last not zero
                    case 'boolean':
                        $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                        $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                    case 'notification':
                        $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                        $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                    case 'date':
                        $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                        $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                    case 'text':
                        $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                        $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                        break;
                    case 'percentage':
                        $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                        $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                        break;
                    case 'integer':
                        $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                        $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                        break;
                    case 'amount':
                        $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                        $activity_array['currency'] = (Currency::where('id',$activity->actionable['data'][$data_index]->currency_type)->first() ? Currency::where('id',$activity->actionable['data'][$data_index]->currency_type)->first()->id : '');
                        $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                        break;
                    case 'textarea':
                        $activity_array['value'] = $activity->actionable['data'][$data_index]->data;
                        $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                        break;
                    case 'template_email':
                        $activity_array['value'] = $activity->actionable['data'][$data_index]->template_id;
                        $activity_array['email'] = $activity->actionable['data'][$data_index]->email;
                        $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                        break;
                    case 'document_email':
                        $activity_array['value'] = $activity->actionable['data'][$data_index]->document_id;
                        $activity_array['email'] = $activity->actionable['data'][$data_index]->email;
                        $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                        break;
                    case 'document':
                        $activity_array['value'] = $activity->actionable['data'][$data_index]->document_id;
                        $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][$data_index]->created_at)->format('Y-m-d');
                        break;
                    case 'dropdown':
                        $activity_array['value'] = $activity->actionable['data'][0]->actionable_dropdown_item_id;
                        $activity_array['crdate'] = Carbon::parse($activity->actionable['data'][0]->created_at)->format('Y-m-d');
                        break;
                }
            }
            $process_progress[0]['activities'][$activity->id] = $activity_array;

        }

        foreach ($step->activities/*$step['activities']*/ as $activity_key => $activity) {
            if (!is_null($activity['dependant_activity_id']) && $activity['stage'] == 0) {
                $working_dependancy_id = $activity['dependant_activity_id'];
                foreach ($process_progress as $searched_step_key => $searched_step) {
                    if (isset($searched_step['activities'][$working_dependancy_id])) {
                        if ($searched_step['activities'][$working_dependancy_id]['stage'] == 2) {
                            $process_progress[$step->id]['activities'][$activity_key]['stage'] = 1;
                        } else {
                            if (!is_null($searched_step['activities'][$working_dependancy_id]['dependant_activity_id'])) {
                                $working_dependancy_id = $searched_step['activities'][$working_dependancy_id]['dependant_activity_id'];
                            }
                        }
                    }
                }
            }
        }

        $completed = true;
        $started = false;
        foreach ($step->activities/*$step['activities']*/ as $activity_key => $activity) {
            /*if ($activity['stage'] == 1) {
                $started = true;
            }*/

            if ($step->id == $this->step_id) {
                $started = true;
                //dd($activity);
            }

            if ($activity['kpi'] && $activity['stage'] != 2) {
                $completed = false;
            }

        }

        if ($started) {
            $process_progress[0]['stage'] = 1;
        }

        if ($completed) {
            $process_progress[0]['stage'] = 2;
            //dd($activity);
        }

        // dd($process_progress);

        return $process_progress;
    }

    function getClientHighestStep(Process $process){

        $id = $this->id;
//dd($process);
        $highest_step_id = Step::with(['activities.actionable.data' => function ($query) use ($id) {
            $query->where('client_id', $id);
        }])->where('process_id',$process->id)->get();
//dd($highest_step_id);
        $process_activities=array();
        foreach ($highest_step_id as $highest_step) {
            if($this->isStepActivitiesCompleted(Step::find($highest_step->id))) {
                foreach ($highest_step->process->activities as $activity){
                    //get the step ids of the process if the client was previously in the selected process
                    foreach ($activity->actionable['data'] as $data) {
                        //push the step id into the array
                        array_push($process_activities, ["step_id" => $highest_step->id,"name" => $highest_step->name,"order" => $highest_step->order]);
                    }
                }
            }
        }
        //sort the array in descending order
        rsort($process_activities);
        return $process_activities[0]["step_id"];
        //return (isset($process_activities[0]) ? $process_activities[0] : []);
    }

    function getClientHighestStepOrder(Process $process){

        $id = $this->id;
//dd($process);
        $highest_step_id = Step::with(['activities.actionable.data' => function ($query) use ($id) {
            $query->where('client_id', $id);
        }])->where('process_id',$process->id)->get();

        $process_activities=array();
        if(count($highest_step_id) > 0) {

            foreach ($highest_step_id as $highest_step) {
                if($this->isStepActivitiesCompleted(Step::find($highest_step->id))) {
                    foreach ($highest_step->process->activities as $activity){
                        //get the step ids of the process if the client was previously in the selected process
                        foreach ($activity->actionable['data'] as $data) {
                            //push the step id into the array
                            array_push($process_activities,["step_id" => $highest_step->id,"name" => $highest_step->name,"order" => $highest_step->order]);
                        }
                    }
                }
            }


            //sort the array in descending order
            usort($process_activities, function ($item1, $item2) {
                return $item2['order'] <=> $item1['order'];
            });

        }
        $process_first_step = Step::where('process_id',$process->id)->orderBy('order','asc')->first();

        return ( isset($process_activities[0]) ? $process_activities[0]["order"] : $process_first_step->order );
        //return (isset($process_activities[0]) ? $process_activities[0] : []);
    }

    function getCurrentStep()
    {
        if ($this->completed_at != null) {
            $step = new Step();
            $step->name = "Converted";
            return $step;
        }

        $step = Step::withTrashed()->find($this->step_id);

        return $step;
    }

    function getCurrentStepToBeRemoved()
    {
        if ($this->completed_at != null) {
            $step = new Step();
            $step->name = "Converted";
            return $step;

            //return $this->process->steps->last();
        }

        foreach ($this->process->steps as $step) {
            foreach ($step->activities as $activity) {
                if ($activity->kpi == 1) {
                    if (isset($activity->actionable['data'][0])) {
                        $found = false;
                        foreach ($activity->actionable['data'] as $datum) {
                            if ($datum->client_id == $this->id) {
                                $found = true;
                                break;
                            }
                        }
                        if(!$found){
                            return $step;
                        }
                    } else {
                        return $step;
                    }
                }
            }
        }
    }

    function isStepActivitiesCompleted(Step $step)
    {
        //$step = Step::find($request->input('step_id'));
        $id = $this->id;
        $step->load(['activities.actionable.data' => function ($query) use ($id) {
            $query->where('client_id', $id);
        }]);

        $found = false;
        foreach ($step->activities as $activity) {
            $found = false;
            if ($activity->kpi == 1) {
                if (isset($activity->actionable['data'][0])) {
                    foreach ($activity->actionable['data'] as $datum) {
                        if ($datum->client_id == $this->id) {
                            $found = true;
                            break;
                        }
                    }
                    if(!$found){
                        return $found;
                    }
                } else {
                    return $found;
                }
            }
            $found = true;
        }
        return $found;
    }

    function isActivitieCompleted(Activity $activity)
    {
        $found = false;
        if ($activity->kpi == 1) {
            if (isset($activity->actionable['data'][0])) {
                foreach ($activity->actionable['data'] as $datum) {
                    if ($datum->client_id == $this->id) {
                        $found = true;
                        break;
                    }
                }
                if(!$found){
                    return $found;
                }
            } else {
                return $found;
            }
        }

        return $found;
    }

    //Check if activities for this client exists
    function clientProcessIfActivitiesExist()
    {
        $id = $this->id;

        /*$steps = Step::withTrashed()->with(['activities.actionable.data' => function ($query) use ($id) {
            $query->where('client_id', $id);
        }])->whereHas('process',function($q){
            $q->where('process_type_id','1');
        })->get();*/
        $processes = [];
        /*array_push($processes, [
            "id" => '0',
            "name" => 'Please Select'
        ]);*/


        $cps = ClientProcess::with('process','process.pgroup')->select('process_id')->whereHas('process')->where('client_id',$id)->distinct()->get();

        //dd($cps);
        foreach($cps as $cp){
            if(isset($cp->process->pgroup)) {
                $processes[$cp->process->pgroup->name][] = [
                    "id" => $cp->process_id,
                    "name" => $cp->process->name
                ];
            } else {
                $processes['None'][] = [
                    "id" => $cp->process_id,
                    "name" => $cp->process->name
                ];
            }
        }
        //dd($processes);

        ksort($processes);

        $processes += array_splice($processes,array_search('None',array_keys($processes)),1);

        return $processes;
    }

    function startNewProcessDropdown()
    {
        $id = $this->id;

        //$signature_processes = Step::select('process_id')->whereHas('process')->where('signature',1)->distinct()->get();

        $offices = OfficeUser::select('office_id')->where('user_id',Auth::id())->get();

        $office_processes = ProcessArea::select('process_id')->whereIn('office_id',collect($offices)->toArray())->get();

        $processes = [];
        $existing_array = [];

        $existings = ClientProcess::with('process')->whereHas('process',function ($q){
            $q->where('form',0);
        })->select('process_id')->where('client_id',$id)->get();

        foreach($existings as $existing){
            array_push($existing_array,$existing->process_id);
        }

        $cps = Process::with('pgroup','steps2')
            ->where('form',0)
            ->whereIn('id',collect($office_processes)->toArray())
            ->orWhere('global',1)
            ->where('process_type_id',1)
            ->whereNotIn('id',$existing_array)
            //->whereNotIn('id',collect($signature_processes)->toArray())
            ->whereHas('steps2')->get();

        foreach ($cps as $cp) {
            if($cp->form == 0) {
                /*if(in_array($cp->id,collect($signature_processes)->toArray())) {*/
                if (in_array($cp->id, $existing_array)) {
                } else {
                    if (isset($cp->pgroup)) {
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
            }
        }

        ksort($processes);

        $processes += array_splice($processes,array_search('None',array_keys($processes)),1);

        return $processes;
    }

    function startNewFormDropdown()
    {
        $id = $this->id;

        //$signature_processes = Step::select('process_id')->whereHas('process')->where('signature',1)->distinct()->get();

        $offices = OfficeUser::select('office_id')->where('user_id',Auth::id())->get();

        $office_processes = ProcessArea::select('process_id')->whereIn('office_id',collect($offices)->toArray())->get();

        $processes = [];
        $existing_array = [];

        $existings = ClientProcess::with('process')->whereHas('process',function ($q){
            $q->where('form',1);
        })->select('process_id')->where('client_id',$id)->get();

        foreach($existings as $existing){
            array_push($existing_array,$existing->process_id);
        }

        $cps = Process::with('pgroup','steps2')
            ->where('form',1)
                    ->whereHas('steps2')
                    ->whereIn('id',collect($office_processes)->toArray())
                    ->orWhere('global',1)
                    ->where('process_type_id',1)
                    //->whereIn('id',collect($signature_processes)->toArray())
                    ->get();

        foreach ($cps as $cp) {
            if($cp->form == 1) {
                /*if(in_array($cp->id,collect($signature_processes)->toArray())) {*/
                if (in_array($cp->id, $existing_array)) {
                } else {
                    if (isset($cp->pgroup)) {
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
            }
        }

        ksort($processes);

        $processes += array_splice($processes,array_search('None',array_keys($processes)),1);

        return $processes;
    }

    //Move process step to the next step if all activities completed
    function moveClientToNextStepIfActivitiesCompleted(Step $step){
        $load_next_step = false;

        $max_step = Step::orderBy('order','desc')->where('process_id', $this->process_id)->first();

        $c_step_order = Step::where('id',$this->step_id)->first();

        $n_step = Step::select('id')->orderBy('order','asc')->where('process_id', $this->process_id)->where('order','>',$step->order)->whereNull('deleted_at')->first();

        if($this->isStepActivitiesCompleted($step) && $step->order < $max_step->order && $step->order == $c_step_order->order){
            $this->step_id = $n_step->id;
            $this->save();
            $load_next_step = true;
        }

        if($this->isStepActivitiesCompleted($step) && $step->order == $max_step->order && $step->order == $c_step_order->order){
            $this->step_id = $step->id;
            $this->save();
        }

        if($this->isStepActivitiesCompleted($step) && $step->order < $c_step_order->order){
            $load_next_step = true;
        }

        return $load_next_step;
    }

    function users(){
        return $this->hasManyThrough('App\User','App\ClientUser', 'client_id', 'id', 'id', 'user_id');
    }

    function groupCompletedActivities(Step $step,$client_id){

        $step->load(['activities.actionable.data' => function ($query) use ($client_id) {
            $query->where('client_id', $client_id);
        }]);

        $group = 1;
        foreach($step["activities"] as $activity) {
            //dd($activity);
            if(isset($activity["actionable"]["data"][0])){
                if($activity["actionable"]["data"][0]->data != null) {
                    $group = $activity->grouping;
                }
            }
        }

        return $group;
    }

    public function detailedClientBasket($client, $cc = false)
    {
        $form = Forms::find(2);
        if (!$form){
            return null;
        }

        if($form) {
            $forms = $form->getClientDetailsInputValues($client->id, $form->id);
            $sections = $tmp = FormSection::with('form_section_inputs')->where('form_id', 2)->get();

            $helper = new ClientHelper();
            $cd = $helper->clientBucketDetailIds($sections ,$client);
            if ($cc){
                return [
                    'forms' => $forms,
                    'cd' => $cd
                ];
            }
            return $sections->keyBy('name')->map(function ($step) use ($cd){
                return $step->form_section_inputs->filter(function ($activ) use ($cd){
                    return in_array($activ->id, array_unique($cd));
                })->values();
            });
        }
    }

    public function getNextVisibleStep($client,$process,$step){

        $step_order = Step::where('id',$step)->first()->order;

        //get invisible steps
        $client_invisible_steps = ActivityStepVisibilityRule::selectRaw('activity_step as step_id')->get()->map(function ($step){
            return $step->step_id;
        })->values()->toArray();
//return $client_invisible_steps;
        //get visible steps
        $client_visible_steps = ClientVisibleStep::select('step_id')->where('client_id',$client)->get()->map(function ($step){
            return $step->step_id;
        })->values()->toArray();

        $a = array_diff($client_invisible_steps,$client_visible_steps);


        $current_process_steps = Step::select('id')->where('process_id',$process)->whereNotIn('id',$a)->where('order','>',$step_order)->orderBy('order')->first();

        return $current_process_steps->id;
    }
}