<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormInputDropdownItem extends Model
{
    use SoftDeletes;

    public function form_section_input()
    {
        return $this->morphOne('App\FormSectionInputs', 'input');
    }

    public function items()
    {
        return $this->hasMany('App\FormInputDropdownItem');
    }
}
