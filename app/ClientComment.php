<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientComment extends Model
{
    public function client()
    {
        return $this->belongsTo('App\Client', 'client_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
