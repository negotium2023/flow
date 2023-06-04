<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Region extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function division()
    {
        return $this->belongsTo('App\Division');
    }

    public function areas()
    {
        return $this->hasMany('App\Area');
    }
}
