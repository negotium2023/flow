<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormInputVideo extends Model
{
    public function form_section_input()
    {
        return $this->morphOne('App\FormSectionInputs', 'input');
    }

    public function data()
    {
        return $this->hasMany('App\FormInputVideoData');
    }
}
