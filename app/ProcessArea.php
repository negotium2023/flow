<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProcessArea extends Model
{
    public function office()
    {
        return $this->belongsTo('App\Office', 'office_id');
    }
}
