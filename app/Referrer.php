<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Referrer extends Model
{
    public function name()
    {
        if ($this->last_name == '') {
            return $this->first_name;
        } else {
            return $this->first_name . ' ' . $this->last_name;
        }
    }

    public function clients(){
        return $this->hasMany('App\Client', 'referrer_id');
    }

    public function referrer_typed(){
        return $this->belongsTo('App\ReferrenceType','referrer_type');
    }
}
