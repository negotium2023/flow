<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientProcess extends Model
{
    public function client(){
        return $this->belongsTo('App\Client','client_id')->withTrashed();
    }

    public function process(){
        return $this->belongsTo('App\Process','process_id');
    }

    public function step(){
        return $this->belongsTo('App\Step','step_id')->withTrashed();
    }
}
