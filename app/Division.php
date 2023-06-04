<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Division extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function regions()
    {
        return $this->hasMany('App\Region');
    }
}
