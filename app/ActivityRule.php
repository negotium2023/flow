<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivityRule extends Model
{
    public function process(){
        return $this->hasOne('App\Process','id','activity_process');
    }
}
