<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormInputAmount extends Model
{
    public function form_section_input()
    {
        return $this->morphOne('App\FormSectionInputs', 'input');
    }

    public function data()
    {
        return $this->hasMany('App\FormInputAmountData');
    }
}
