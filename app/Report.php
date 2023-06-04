<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    public function activity()
    {
        return $this->belongsTo('App\Activity');
    }

    public function user()
    {
        return $this->belongsTo('App\User')->withTrashed();
    }
}
