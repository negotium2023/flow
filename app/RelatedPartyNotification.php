<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RelatedPartyNotification extends Model
{
    protected $table = 'related_party_notifications';

    public function activity()
    {
        return $this->morphOne('App\Activity', 'actionable');
    }

    public function data()
    {
        return $this->hasMany('App\RelatedPartyNotificationData');
    }
}
