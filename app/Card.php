<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Card extends Model
{
    use SoftDeletes;

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function client(){
        return $this->belongsTo(Client::class,'client_id','id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assignee_id')
            ->select('id', 'first_name', 'last_name')->withDefault(['id' => 0, 'first_name' => "Not", 'last_name' => "Assigned"]);
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->select('id','name','fcolor');
    }

    public function priorityStatus()
    {
        return $this->belongsTo(PriorityStatus::class, 'priority_id')
            ->select('id','name','fcolor');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'card_id')->whereNull('parent_id')
            ->with(['assigned', 'subTasks','selected_assignee'])
            ->withCount('subTasks');
    }

    public function discussions()
    {
        return $this->hasMany(Discussion::class, 'card_id')
            ->with('user');
    }

    public function creator(){
        return $this->hasOne(User::class,'id','creator_id')
            ->select('id','first_name','last_name');
    }

    public function document()
    {
        return $this->belongsTo(CardAttachment::class, 'card_id');
    }

    public function recordings()
    {
        return $this->hasMany(Recording::class, 'card_id');
    }

    public function dependency(){
        return $this->belongsTo(Card::class,'dependency_id');
    }

    public function statuss(){
        return $this->belongsTo(Status::class,'status_id');
    }

    public function priority(){
        return $this->belongsTo(PriorityStatus::class,'priority_id');
    }
}
