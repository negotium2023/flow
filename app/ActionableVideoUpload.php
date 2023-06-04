<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActionableVideoUpload extends Model
{
    public function activity()
    {
        return $this->morphOne('App\Activity', 'actionable');
    }

    public function data()
    {
        return $this->hasMany('App\ActionableVideoUploadData');
    }
}
