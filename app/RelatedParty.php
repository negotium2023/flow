<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use AustinHeap\Database\Encryption\Traits\HasEncryptedAttributes;

class RelatedParty extends Model
{
    use HasEncryptedAttributes;
    use SoftDeletes;
    private $related_parties = [];

    protected $encrypted = ['description','company','first_name','last_name','email','contact','cif_code','id_number','çompany_registration_number'];
    protected $dates = ['deleted_at'];
    //protected $hidden = ['hash_first_name','hash_last_name','hash_cif_code','hash_company','hash_id_number','hash_email','hash_contact','hash_company_registration_number'];

    public function unHide(){
        $this->hidden = [];
    }

    public function actionable()
    {
        return $this->morphTo();
    }

       /* public function getRelatedParties($client_id, $level_id){
            return $this->where('client_id', '', $client_id)->where('level_id', '=', $level_id)->get();
        }*/

    public function documents()
    {
        return $this->hasMany('App\Document');
    }

    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    public function completedby()
    {
        return $this->belongsTo(User::class, 'completed_by')->withTrashed();
    }

    public function referrer()
    {
        return $this->belongsTo('App\Referrer', 'referrer_id');
    }

    public function introducer()
    {
        return $this->belongsTo(User::class, 'introducer_id')->withTrashed();
    }

    public function process()
    {
        return $this->belongsTo(Process::class);
    }

    public function business_unit()
    {
        return $this->belongsTo(BusinessUnits::class, 'business_unit_id');
    }

    public function committee()
    {
        return $this->belongsTo(Committee::class, 'committee_id')->withTrashed();
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id')->withTrashed();
    }

    public function step()
    {
        return $this->belongsTo(Step::class, 'step_id')->withTrashed();
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function comments()
    {
        return $this->hasMany('App\RelatedPartyComment');
    }

    public function trigger()
    {
        return $this->belongsTo('App\TriggerType', 'trigger_type_id');
    }

    function groupCompletedActivities(Step $step,$client_id,$related_party_id){

        $step->load(['activities.actionable.data' => function ($query) use ($client_id,$related_party_id) {
            $query->where('client_id', $client_id)->where('related_party_id',$related_party_id);
        }]);

        $group = 0;
        foreach($step["activities"] as $activity) {
            //dd($activity);
            if(isset($activity["actionable"]["data"][0]) && $activity["actionable"]["data"][0]['data'] != null){
                $group = $activity->grouping;
            }
        }

        return $group;
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
}
