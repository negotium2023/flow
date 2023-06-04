<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormInputDropdown extends Model
{
    public function form_section_input()
    {
        return $this->morphOne('App\FormSectionInputs', 'input');
    }

    public function items(){
        return $this->hasMany('App\FormInputDropdownItem');
    }

    public function data()
    {
        return $this->hasMany('App\FormInputDropdownData');
    }

    public function valuess()
    {
        return $this->hasMany('App\FormInputDropdownData','form_input_dropdown_id');
    }
}
