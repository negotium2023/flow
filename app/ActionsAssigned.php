<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActionsAssigned extends Model
{
    use SoftDeletes;

    protected $table = 'actions_assigned';

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'clients')->withTrashed();
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
