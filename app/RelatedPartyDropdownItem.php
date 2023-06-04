<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RelatedPartyDropdownItem extends Model
{
    use SoftDeletes;

    public function activity()
    {
        return $this->morphOne('App\Activity', 'actionable');
    }

    public function items(){
        return $this->hasMany('App\RelatedPartyDropdownItem');
    }
}
