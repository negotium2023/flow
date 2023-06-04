<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskTemplate extends Model
{
    use SoftDeletes;

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function parent()
    {
        return $this->belongsTo(TaskTemplate::class, 'parent_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function subTasks()
    {
        return $this->hasMany(TaskTemplate::class, 'parent_id')
            ->whereNotNull('parent_id');
    }

    public function assigned()
    {
        return $this->belongsTo(User::class, 'assignee_id')
            ->select('id','first_name', 'last_name', 'email')
            ->withDefault(['id' => 0, 'first_name' => "Not", 'last_name' => "Assigned"]);
    }
}
