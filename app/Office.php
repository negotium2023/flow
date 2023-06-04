<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Office extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function area()
    {
        return $this->belongsTo('App\Area');
    }

    public function processes()
    {
        return $this->hasMany('App\Process');
    }
}
