<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientUser extends Model
{
    function client()
    {
        return $this->belongsTo('App\Client', 'client_id');
    }

    function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
