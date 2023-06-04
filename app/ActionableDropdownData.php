<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActionableDropdownData extends Model
{
    protected $table = 'actionable_dropdown_data';

    public function item()
    {
        return $this->belongsTo('App\ActionableDropdownItem', 'actionable_dropdown_item_id');
    }
}
