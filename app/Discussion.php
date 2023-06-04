<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discussion extends Model
{
    use SoftDeletes;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')
            ->select('id', 'first_name', 'last_name', 'email');
    }

    public function card()
    {
        return $this->belongsTo(Card::class, 'card_id');
    }
}
