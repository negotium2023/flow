<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessUnits extends Model
{
    use SoftDeletes;

    public function clients()
    {
        return $this->hasMany('App\Client','business_unit_id','id');
    }
}
