<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityComment extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'client_activities_comments';

    public function client()
    {
        return $this->belongsTo('App\Client', 'client_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function activity()
    {
        return $this->belongsTo('App\Activity', 'activity_id');
    }
}
