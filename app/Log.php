<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    public function activities()
    {
        return $this->hasMany('App\ActivityLog', 'log_id');
    }

    public function user(){
        return $this->hasOne('App\User', 'id', 'user_id')->withTrashed();
    }

    public function client(){
        return $this->hasOne('App\Client', 'id', 'client_id')->withTrashed();
    }

    public function relatedparty(){
        return $this->hasOne('App\RelatedParty', 'id', 'related_party_id')->withTrashed();
    }
}
