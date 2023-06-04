<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormInputDropdownData extends Model
{
    protected $table = 'form_input_dropdown_data';

    public function item()
    {
        return $this->hasOne('App\FormInputDropdownItem', 'id', 'form_input_dropdown_item_id');
    }
}
