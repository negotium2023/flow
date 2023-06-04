<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RelatedPartyDropdown extends Model
{
    public function activity()
    {
        return $this->morphOne('App\Activity', 'actionable');
    }

    public function items(){
        return $this->hasMany('App\RelatedPartyDropdownItem');
    }

    public function data()
    {
        return $this->hasMany('App\RelatedPartyDropdownData');
    }

    public function valuess()
    {
        return $this->hasMany('App\RelatedPartyDropdownData','related_party_dropdown_id');
    }
}
