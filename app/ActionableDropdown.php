<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActionableDropdown extends Model
{
    public function activity()
    {
        return $this->morphOne('App\Activity', 'actionable');
    }

    public function items(){
        return $this->hasMany('App\ActionableDropdownItem');
    }

    public function data()
    {
        return $this->hasMany('App\ActionableDropdownData');
    }

    public function valuess()
    {
        return $this->hasMany('App\ActionableDropdownData','actionable_dropdown_id');
    }
}
