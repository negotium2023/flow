<?php


namespace App;


class ClientHelper
{
    public function clientBucketActivityIds($steps, $client, $process_id)
    {
        $tmp_act = $steps->where('process_id', $process_id)->map(function ($step) {
            return $step->activities->map(function ($activ) {
                if ($activ->client_bucket) {
                    return $activ->id;
                }
            });
        })->flatten()->toArray();

        $tmp_act = array_values(array_filter($tmp_act));

        $parent_activities_in_client_basket = ActivityInClientBasket::has('activity')
            ->where('client_id', $client->id)
            ->select('activity_id', 'in_client_basket')
            ->get();

        $flag = $parent_activities_in_client_basket->map(function ($activity_id) {
            return $activity_id->activity_id;
        })->toArray();

        foreach ($tmp_act as $key => $item) {
            if (in_array($item, $flag)) {
                unset($tmp_act[$key]);
            }
        }

        $active_activities = $parent_activities_in_client_basket->where('in_client_basket', 1)->map(function ($activity) {
            return $activity->activity_id;
        })->values()->toArray();

        return array_merge($tmp_act, $active_activities);
    }

    public function clientBucketDetailIds($sections, $client)
    {

        $tmp_act = $sections->map(function ($step) {
            return $step->form_section_inputs->map(function ($activ) {
                if ($activ->client_bucket) {
                    return $activ->id;
                }
            });
        })->flatten()->toArray();

        $tmp_act = array_values(array_filter($tmp_act));

        $parent_activities_in_client_basket = FormSectionInputInClientBasket::where('client_id', $client->id)->select('input_id', 'in_client_basket')
            ->get();

        $flag = $parent_activities_in_client_basket->map(function ($activity_id) {
            return $activity_id->input_id;
        })->toArray();

        foreach ($tmp_act as $key => $item) {
            if (in_array($item, $flag)) {
                unset($tmp_act[$key]);
            }
        }

        $active_activities = $parent_activities_in_client_basket->where('in_client_basket', 1)->map(function ($activity) {
            return $activity->input_id;
        })->values()->toArray();

        return array_merge($tmp_act, $active_activities);
    }

    public function detailedClientBasket($client, $cc = false)
    {
        $form = Forms::find($client->crm_id);
        if (!$form) {
            return null;
        }

        if ($form) {
            $forms = $form->getClientDetailsInputValues($client->id, $form->id);
            $sections = $tmp = FormSection::with('form_section_inputs')->where('form_id', $client->crm_id)->get();

            $cd = $this->clientBucketDetailIds($sections, $client);
            if ($cc) {
                return [
                    'forms' => $forms,
                    'cd' => $cd,
                    'çount' => $sections->map(function ($step) use ($cd) {
                        return [
                            'inputs' => $step->form_section_inputs->filter(function ($activ) use ($cd) {
                                return in_array($activ->id, array_unique($cd));
                            })->values(),
                            'heading' => $step->name
                        ];
                    })
                ];
            }
            return $sections->map(function ($step) use ($cd) {
                return [
                    'inputs' => $step->form_section_inputs->filter(function ($activ) use ($cd) {
                        return in_array($activ->id, array_unique($cd));
                    })->values(),
                    'heading' => $step->name
                ];
            });
        }
    }

    public function clientDetails($client, $cc = false)
    {
        $form = Forms::find($client->crm_id);
        if (!$form) {
            return null;
        }

        if ($form) {
            $forms = $form->getClientDetails($client->id, $form->id);
            $sections = $tmp = FormSection::with('form_section_inputs')->where('form_id', $client->crm_id)->get();

            $cd = $this->clientBucketDetailIds($sections, $client);
            if ($cc) {
                return [
                    'forms' => $forms,
                    'cd' => $cd
                ];
            }
            return $sections->map(function ($step) use ($cd) {
                return [
                    'inputs' => $step->form_section_inputs->filter(function ($activ) use ($cd) {
                        return in_array($activ->id, array_unique($cd));
                    })->values(),
                    'heading' => $step->name
                ];
            });
        }
    }

    public function clientEmails($client, $cc = false)
    {
        $form = Forms::find($client->crm_id);
        if (!$form) {
            return null;
        }

        if ($form) {
            $emails = $form->getClientEmails($client->id, $form->id);

            if ($cc) {
                return [
                    'emails' => $emails
                ];
            }
        }
    }

    public function steps_data($client, $process)
    {
        $client_process = ClientProcess::where('client_id', $client->id)->where('process_id', $process->id)->first();
        //dd($process);
        $steps = Step::where('process_id', $process->id)->orderBy('order', 'asc')->get();
        $c_step_order = Step::where('id', $client_process->step_id)->withTrashed()->first();
        $step_data = [];
        foreach ($steps as $a_step):
            if ($a_step->deleted_at == null) {
                $progress_color = $process->getStageHex(0);
                $step_stage = 0;

                if ($c_step_order->order == $a_step->order) {
                    $progress_color = $process->getStageHex(1);
                    $step_stage = 1;
                }

                if ($c_step_order->order > $a_step->order) {
                    $progress_color = $process->getStageHex(2);
                    $step_stage = 2;
                }


                $tmp_step = [
                    'id' => $a_step->id,
                    'name' => $a_step->name,
                    'progress_color' => $progress_color,
                    'process_id' => $a_step->process_id,
                    'order' => $a_step->order,
                    'stage' => $step_stage
                ];

                array_push($step_data, $tmp_step);
            }
        endforeach;

        return $step_data;
    }

    public function getClientBasketActivities($client, $process)
    {
        $steps = Step::where('process_id', $process->id)->orderBy('order','asc')->get();

        $activities_in_client_basket = $this->clientBucketActivityIds($steps, $client, $process->id);

        return $steps->map(function ($step) use ($activities_in_client_basket){
            return [
                'activity' => $step->activities->filter(function ($activ) use ($activities_in_client_basket){
                    return in_array($activ->id, array_unique($activities_in_client_basket));
                })->values(),
                'heading' => $step->name,
            ];
        });
    }
}