<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Step extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function activity()
    {
        return $this->hasMany('App\Activity', 'step_id')->orderBy('order');
    }

    public function activities()
    {
        return $this->hasMany('App\Activity', 'step_id')->orderBy('order');
    }

    public function getHex()
    {
        return '#' . $this->colour;
    }

    public function duration(){
        return $this->activities()->sum('threshold');
    }

    public function process(){
        return $this->belongsTo(Process::class);
    }

    public function actionProcess(){
        return $this->belongsTo(Process::class)->withTrashed();
    }
}
