<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Actions extends Model
{
    protected $table = 'actions';

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function activity()
    {
        return $this->hasMany('App\ActionActivities', 'action_id');
    }

    public function users()
    {
        return $this->hasMany('App\ActionActivities', 'action_id');
    }
}
