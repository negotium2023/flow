<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function region()
    {
        return $this->belongsTo('App\Region');
    }

    public function offices()
    {
        return $this->hasMany('App\Office','area_id','id');
    }
}
