<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActionActivities extends Model
{
    use SoftDeletes;

    protected $table = 'actions_activities';

    public function name(){
        return $this->belongsTo('App\Activity', 'activity_id','id');
    }

    public function user(){
        return $this->belongsTo('App\User', 'user_id','id');
    }

    public function users(){
        return $this->belongsTo('App\User', 'user_id','id');
    }

    public function process(){
        return $this->belongsTo('App\Process', 'process_id','id');
    }
}
