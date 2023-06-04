<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormSection extends Model
{
    use SoftDeletes;

    protected $table = 'form_sections';
    protected $dates = ['deleted_at'];

    public function form_section_input()
    {
        return $this->hasMany('App\FormSectionInputs', 'form_section_id')->orderBy('order');
    }

    public function form_section_inputs()
    {
        return $this->hasMany('App\FormSectionInputs', 'form_section_id')->orderBy('order');
    }

    public function form()
    {
        return $this->belongsTo(Forms::class)->withTrashed();
    }

    public function tabs()
    {
        return $this->belongsTo('App\FormTab', 'tab_id');
    }

}
