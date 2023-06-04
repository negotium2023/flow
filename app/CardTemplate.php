<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CardTemplate extends Model
{
    use SoftDeletes;

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assignee_id')
                    ->select('id', 'first_name', 'last_name')->withDefault(['id' => 0, 'first_name' => "Not", 'last_name' => "Assigned"]);
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->select('id','name');
    }

    public function priorityStatus()
    {
        return $this->belongsTo(PriorityStatus::class, 'priority_id')
            ->select('id','name');
    }

    public function tasks()
    {
        return $this->hasMany(TaskTemplate::class, 'card_template_id')->whereNull('parent_id')
            ->with(['assigned', 'subTasks'])
            ->withCount('subTasks');
    }

    public function discussions()
    {
        return $this->hasMany(Discussion::class, 'card_id')
            ->with('user');
    }
}
