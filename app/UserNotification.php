<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    public function notification(){
        return $this->belongsTo('App\Notification','notification_id','id');
    }
}
