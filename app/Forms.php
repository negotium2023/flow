<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Forms extends Model
{
    use SoftDeletes;
    protected $fillable = ['id'];

    protected $dates = ['deleted_at'];

    public function sections()
    {
        return $this->hasMany('App\FormSection', 'form_id')->orderBy('order');
    }

    public function tabs()
    {
        return $this->hasMany('App\FormTab', 'form_id');
    }

    public function getClientDetails($client_id,$form_id)
    {
        $tabs = FormTab::where('form_id',$form_id)->get();
        //$form = Forms::find(2);

        $sections = FormSection::with(['tabs','form_section_inputs.input.data' => function ($query) use ($client_id) {
            $query->where('client_id', $client_id);
        }])->where('form_id',$form_id)->orderBy('order')->get();

        //dd($sections);
        $client_detail = [];

        foreach ($tabs as $tab) {
            $process_progress = [];
            foreach ($sections as $section) {


                $section_array = [
                    'id' => $section->id,
                    'name' => $section->name,
                    'order' => $section->order,
                    'group' => $section->group,
                    'show_name_in_tabs' => $section->show_name_in_tabs,
                    // 'tab' => $section->tabs["name"],
                    // 'primary_tab' => $section->tabs["primary_tab"],
                    'stage' => 0,
                    'last_updated' => $this->getClientDetailsSectionLastUpdate($client_id,$section->id),
                    'total_groups' => FormSectionInputs::where('form_section_id',$section->id)->orderBy('grouping','DESC')->first()->grouping,
                    'max_group'=> ($section->group == null || $section->group > 0 ? $this->groupCompletedInputs($section->id,$client_id) : 1),
                    'group_label' => ($section->group != null || $section->group_label > 0 ? $section->group_label : null),
                    'inputs' => []
                ];
                //$process_progress[($section->tabs["name"] != null ? $section->tabs["order"] : '1000')][($section->tabs["name"] != null ? $section->tabs["name"] : 'Questionnaires')]['primary_tab'] = $section->tabs["primary_tab"];
                $process_progress[$section->order][$section->name]['data'][$section->id] = $section_array;

                //$process_progress[($section->tabs["name"] != null ? $section->tabs["order"] : '1000')][($section->tabs["name"] != null ? $section->tabs["name"] : 'Questionnaires')]['data'][$section->id]['group'] = $section_array;
                //array_push($process_progress, $section_array);

                foreach ($section->form_section_inputs as $input) {

                    $input_array = [
                        'id' => $input->id,
                        'kpi' => $input->kpi,
                        'name' => $input->name,
                        'order' => $input->order,
                        'type' => $input->getFormTypeName(),
                        'type_display' => $input->getFormTypeDisplayName(),
                        'stage' => 0,
                        'due_date' => 0,
                        'grouping' => $input->grouping,
                        'dependant_activity_id' => $input->dependant_activity_id,
                        'tooltip' => $input->tooltip,
                        'client_bucket' => $input->client_bucket,
                        'level' => ($input->level != 0 ? 100 - ($input->level * 5) : '100'),
                        'color' => $input->color,
                    ];

                    if ($input_array['type'] == 'dropdown') {

                        $input_array['dropdown_items'] = $input->input->items->pluck('name', 'id')->toArray();
                        $input_array['dropdown_values'] = $input->input->valuess->where('client_id', $client_id)->pluck('form_input_dropdown_item_id', 'id')->toArray();
                        //dd($activity_array);
                    }

                    if (isset($input->input['data'][0])) {
                        // dd($input->input['data']);
                        $data_index = count($input->input['data']) - 1;
//                dd($activity->input['data'][$data_index]->data);
                        switch ($input_array['type']) {
                            //get last not zero
                            case 'boolean':
                                $input_array['value'] = $input->input['data'][$data_index]->data;
                                $input_array['crdate'] = Carbon::parse($input->input['data'][$data_index]->created_at)->format('Y-m-d');
                                break;
                            case 'date':
                                $input_array['value'] = $input->input['data'][$data_index]->data;
                                $input_array['crdate'] = Carbon::parse($input->input['data'][$data_index]->created_at)->format('Y-m-d');
                                break;
                            case 'text':
                                $input_array['value'] = $input->input['data'][$data_index]->data;
                                $input_array['crdate'] = Carbon::parse($input->input  ['data'][$data_index]->created_at)->format('Y-m-d');
                                break;
                            case 'amount':
                                $input_array['value'] = $input->input['data'][$data_index]->data;
                                $input_array['crdate'] = Carbon::parse($input->input  ['data'][$data_index]->created_at)->format('Y-m-d');
                                break;
                            case 'percentage':
                                $input_array['value'] = $input->input['data'][$data_index]->data;
                                $input_array['crdate'] = Carbon::parse($input->input  ['data'][$data_index]->created_at)->format('Y-m-d');
                                break;
                            case 'integer':
                                $input_array['value'] = $input->input['data'][$data_index]->data;
                                $input_array['crdate'] = Carbon::parse($input->input  ['data'][$data_index]->created_at)->format('Y-m-d');
                                break;
                            case 'textarea':
                                $input_array['value'] = $input->input['data'][$data_index]->data;
                                $input_array['crdate'] = Carbon::parse($input->input  ['data'][$data_index]->created_at)->format('Y-m-d');
                                break;
                            case 'dropdown':
                                $input_array['value'] = $input->input['data'][$data_index]->form_input_dropdown_item_id;
                                $input_array['crdate'] = Carbon::parse($input->input['data'][$data_index]->created_at)->format('Y-m-d');
                                break;
                        }
                    }

                    if($section->group == 1) {
                        $process_progress[$section->order][$section->name]['data'][$section->id]['grouping'][$input->grouping]['inputs'][$input->id] = $input_array;
                    }

                    $process_progress[$section->order][$section->name]['data'][$section->id]['inputs'][$input->id] = $input_array;

                }
                ksort($process_progress);
                $client_detail = $process_progress;
            }
        }
        //dd($client_detail);
        return $client_detail;
    }

    public function getClientEmails($client_id,$form_id)
    {

        $sections = FormSection::where('form_id',$form_id)->orderBy('order')->get();

        //dd($sections);
        $client_emails = [];

            $process_progress = [];
            $emails = [];
            foreach ($sections as $section) {
                $inputs = FormSectionInputs::where('form_section_id',$section->id)->where('email',1)->orderBy('email')->orderBy('email_group')->get();
                foreach ($inputs as $input) {

                    $input_array = [
                        'name' => $input->name,
                        'email' => $input->email,
                        'email_group' => $input->email_group,
                        'type' => $input->getFormTypeName(),
                        'type_display' => $input->getFormTypeDisplayName(),
                    ];

                    if ($input_array['type'] == 'dropdown') {

                        $input_array['dropdown_items'] = $input->input->items->pluck('name', 'id')->toArray();
                        $input_array['dropdown_values'] = $input->input->valuess->where('client_id', $client_id)->pluck('form_input_dropdown_item_id', 'id')->toArray();
                        //dd($activity_array);
                    }

                    if (isset($input->input['data'][0])) {
                        // dd($input->input['data']);
                        $data_index = count($input->input['data']) - 1;
//                dd($activity->input['data'][$data_index]->data);
                        switch ($input_array['type']) {
                            //get last not zero
                            case 'boolean':
                                $input_array['value'] = $input->input['data'][$data_index]->data;
                                $input_array['crdate'] = Carbon::parse($input->input['data'][$data_index]->created_at)->format('Y-m-d');
                                break;
                            case 'date':
                                $input_array['value'] = $input->input['data'][$data_index]->data;
                                $input_array['crdate'] = Carbon::parse($input->input['data'][$data_index]->created_at)->format('Y-m-d');
                                break;
                            case 'text':
                                $input_array['value'] = $input->input['data'][$data_index]->data;
                                $input_array['crdate'] = Carbon::parse($input->input  ['data'][$data_index]->created_at)->format('Y-m-d');
                                break;
                            case 'amount':
                                $input_array['value'] = $input->input['data'][$data_index]->data;
                                $input_array['crdate'] = Carbon::parse($input->input  ['data'][$data_index]->created_at)->format('Y-m-d');
                                break;
                            case 'percentage':
                                $input_array['value'] = $input->input['data'][$data_index]->data;
                                $input_array['crdate'] = Carbon::parse($input->input  ['data'][$data_index]->created_at)->format('Y-m-d');
                                break;
                            case 'integer':
                                $input_array['value'] = $input->input['data'][$data_index]->data;
                                $input_array['crdate'] = Carbon::parse($input->input  ['data'][$data_index]->created_at)->format('Y-m-d');
                                break;
                            case 'textarea':
                                $input_array['value'] = $input->input['data'][$data_index]->data;
                                $input_array['crdate'] = Carbon::parse($input->input  ['data'][$data_index]->created_at)->format('Y-m-d');
                                break;
                            case 'dropdown':
                                $input_array['value'] = $input->input['data'][$data_index]->form_input_dropdown_item_id;
                                $input_array['crdate'] = Carbon::parse($input->input['data'][$data_index]->created_at)->format('Y-m-d');
                                break;
                        }
                    }

                    $process_progress[]  = $input_array;

                }
                ksort($process_progress);
                //dd($process_progress);
                $client_emails = $process_progress;
            }

        return $client_emails;
    }

    public function getClientDetailsInputs($form_id)
    {
        $sections = FormSection::with('form_section_inputs')->where('form_id',$form_id)->orderBy('order')->get();

        //dd($sections);
        $client_detail = [];

        foreach ($sections as $section) {
            $process_progress = [];

            $section_array = [
                'id' => $section->id,
                'name' => $section->name,
                'order' => $section->order,
                'stage' => 0,
                'total_groups' => FormSectionInputs::where('form_section_id',$section->id)->orderBy('grouping','DESC')->first()->grouping,
                'max_group'=> 1,
                'group_label' => ($section->group != null || $section->group_label > 0 ? $section->group_label : null),
                'inputs' => []
            ];

            array_push($process_progress,$section_array);

            foreach ($section->form_section_inputs as $input) {

                $input_array = [
                    'id' => $input->id,
                    'kpi' => $input->kpi,
                    'name' => $input->name,
                    'order' => $input->order,
                    'type' => $input->getFormTypeName(),
                    'type_display' => $input->getFormTypeDisplayName(),
                    'stage' => 0,
                    'due_date' => 0,
                    'grouping' => $input->grouping,
                    'dependant_activity_id' => $input->dependant_activity_id,
                    'tooltip' => $input->tooltip,
                    'client_bucket' => $input->client_bucket,
                    'level' => ($input->level != 0 ? 100-($input->level*5) : '100'),
                    'color' => $input->color,
                ];

                if ($input_array['type'] == 'dropdown') {
                    $input_array['dropdown_items'] = $input->input->items->pluck('name', 'id')->toArray();
                    $input_array['dropdown_values'] = [];
                }

                $process_progress[0]['inputs'][$input->id] = $input_array;
            }

            $client_detail[$section->id][$section->name] = $process_progress;
        }

        return $client_detail;
    }

    public function getClientDetailsInputValues($client_id,$form_id)
    {
        $sections = FormSection::with(['tabs','form_section_inputs.input.data' => function ($query) use ($client_id) {
            $query->where('client_id', $client_id);
        }])->where('form_id',$form_id)->orderBy('order')->get();

        //dd($sections);
        $client_detail = [];

        foreach ($sections as $section) {
            $process_progress = [];
            $update = [];

            $section_array = [
                'id' => $section->id,
                'name' => $section->name,
                'order' => $section->order,
                'tab' => (isset($section->tabs["id"]) ? $section->tabs["id"] : ''),
                'details-tab' => (isset($section->tabs["details"]) ? $section->tabs["details"] : ''),
                'stage' => 0,
                'inputs' => []
            ];

            array_push($process_progress,$section_array);

            foreach ($section->form_section_inputs as $input) {

                $input_array = [
                    'id' => $input->id,
                    'kpi' => $input->kpi,
                    'name' => $input->name,
                    'order' => $input->order,
                    'type' => $input->getFormTypeName(),
                    'type_display' => $input->getFormTypeDisplayName(),
                    'stage' => 0,
                    'due_date' => 0,
                    'dependant_activity_id' => $input->dependant_activity_id,
                    'tooltip' => $input->tooltip,
                    'client_bucket' => $input->client_bucket,
                    'level' => ($input->level != 0 ? 100-($input->level*5) : '100'),
                    'color' => $input->color,
                ];

                if ($input_array['type'] == 'dropdown') {

                    $input_array['dropdown_items'] = $input->input->items->pluck('name', 'id')->toArray();
                    $input_array['dropdown_values'] = $input->input->valuess->where('client_id',$client_id)->pluck('form_input_dropdown_item_id', 'id')->toArray();
                    //dd($activity_array);
                }

                if (isset($input->input['data'][0])) {
                    // dd($input->input['data']);
                    $data_index = count($input->input['data']) -1;
//                dd($activity->input['data'][$data_index]->data);
                    switch ($input_array['type']) {
                        //get last not zero
                        case 'boolean':
                            $input_array['value'] = $input->input['data'][$data_index]->data;
                            $input_array['crdate'] = Carbon::parse($input->input['data'][$data_index]->created_at)->format('Y-m-d');
                            break;
                        case 'date':
                            $input_array['value'] = $input->input['data'][$data_index]->data;
                            $input_array['crdate'] = Carbon::parse($input->input['data'][$data_index]->created_at)->format('Y-m-d');
                            break;
                        case 'text':
                            $input_array['value'] = $input->input['data'][$data_index]->data;
                            $input_array['crdate'] = Carbon::parse($input->input  ['data'][$data_index]->created_at)->format('Y-m-d');
                            break;
                        case 'amount':
                            $input_array['value'] = $input->input['data'][$data_index]->data;
                            $input_array['crdate'] = Carbon::parse($input->input  ['data'][$data_index]->created_at)->format('Y-m-d');
                            break;
                        case 'percentage':
                            $input_array['value'] = $input->input['data'][$data_index]->data;
                            $input_array['crdate'] = Carbon::parse($input->input  ['data'][$data_index]->created_at)->format('Y-m-d');
                            break;
                        case 'integer':
                            $input_array['value'] = $input->input['data'][$data_index]->data;
                            $input_array['crdate'] = Carbon::parse($input->input  ['data'][$data_index]->created_at)->format('Y-m-d');
                            break;
                        case 'textarea':
                            $input_array['value'] = $input->input['data'][$data_index]->data;
                            $input_array['crdate'] = Carbon::parse($input->input  ['data'][$data_index]->created_at)->format('Y-m-d');
                            break;
                        case 'dropdown':
                            $input_array['value'] = $input->input['data'][$data_index]->form_input_dropdown_item_id;
                            $input_array['crdate'] = Carbon::parse($input->input['data'][$data_index]->created_at)->format('Y-m-d');
                            break;
                    }
                    array_push($update,Carbon::parse($input->input['data'][$data_index]->updated_at)->format('Y-m-d'));
                }
                $process_progress[0]['inputs'][$input->id] = $input_array;
                $process_progress[0]['updated'] = rsort($update);
            }

            $client_detail[$section->id][$section->name] = $process_progress;
        }
//dd($client_detail);
        return $client_detail;
    }

    public function getClientDetailsSectionLastUpdate($client_id,$section_id)
    {
        $sections = FormSection::with(['form_section_inputs.input.data' => function ($query) use ($client_id) {
            $query->where('client_id', $client_id);
        }])->where('id',$section_id)->get();

        //dd($sections);
        $client_detail = [];

        foreach ($sections as $section) {
            $update = [];
            $process_progress = [];

            foreach ($section->form_section_inputs as $input) {

                $input_array = [
                    'id' => $input->id,
                    'kpi' => $input->kpi,
                    'name' => $input->name,
                    'order' => $input->order,
                    'type' => $input->getFormTypeName(),
                    'type_display' => $input->getFormTypeDisplayName(),
                    'stage' => 0,
                    'due_date' => 0,
                    'dependant_activity_id' => $input->dependant_activity_id,
                    'tooltip' => $input->tooltip,
                    'client_bucket' => $input->client_bucket,
                    'level' => ($input->level != 0 ? 100-($input->level*5) : '100'),
                    'color' => $input->color,
                ];

                if ($input_array['type'] == 'dropdown') {

                    $input_array['dropdown_items'] = $input->input->items->pluck('name', 'id')->toArray();
                    $input_array['dropdown_values'] = $input->input->valuess->where('client_id',$client_id)->pluck('form_input_dropdown_item_id', 'id')->toArray();
                    //dd($activity_array);
                }

                if (isset($input->input['data'][0])) {
                    $data_index = count($input->input['data']) -1;
                    array_push($update,Carbon::parse($input->input['data'][$data_index]->updated_at)->format('Y-m-d'));
                }
                rsort($update);
                $process_progress[0] = $update;
            }

            $client_detail = $process_progress[0];
        }

        return $client_detail;
    }

    function groupCompletedInputs($form,$client_id){

        $form = FormSection::with(['form_section_inputs.input.data' => function ($query) use ($client_id) {
            $query->where('client_id', $client_id);
        }])->where('id',$form)->first();
        $group = 1;

        //dd($form);
        foreach($form["form_section_inputs"] as $activity) {
            //dd($activity);
            if(isset($activity["input"]["data"][0])){
                if($activity["input"]["data"][0]->data != null) {
                    $group = $activity->grouping;
                }
            }
        }

        return $group;
    }
}
