<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Recording extends Model
{
    protected $table = 'recordings';

    public function cards(){
        return $this->belongsTo('App\Card','card_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')
            ->select('id', 'first_name', 'last_name', 'email');
    }
}
