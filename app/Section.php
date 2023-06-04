<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
    use SoftDeletes;

    public function board()
    {
        return $this->belongsTo(Board::class, 'board_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function cards()
    {
        return $this->hasMany(Card::class, 'section_id')
            ->with(['assignedUser', 'tasks.subTasks', 'priorityStatus', 'status'])
            ->withCount(['tasks']);
    }

    public function cards_templates()
    {
        return $this->hasMany(CardTemplate::class, 'section_id')
            ->with(['assignedUser', 'tasks.subTasks', 'priorityStatus', 'status'])
            ->withCount(['tasks']);
    }
}
