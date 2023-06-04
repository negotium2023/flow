<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientActivity extends Model
{
    public function client()
    {
        return $this->belongsTo('App\Client', 'client_id');
    }

    public function activity()
    {
        return $this->belongsTo('App\Activity', 'activity_id');
    }
}
