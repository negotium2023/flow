<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormSectionInputs extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function input()
    {
        return $this->morphTo();
    }

        public function getFormTypeName()
    {
        //activity type hook
        switch ($this->input_type) {
            case 'App\FormInputText':
                return 'text';
                break;
            case 'App\FormInputHeading':
                return 'heading';
                break;
            case 'App\FormInputSubheading':
                return 'subheading';
                break;
            case 'App\FormInputAmount':
                return 'amount';
                break;
            case 'App\FormInputPercentage':
                return 'percentage';
                break;
            case 'App\FormInputInteger':
                return 'integer';
                break;
            case 'App\FormInputVideo':
                return 'video';
                break;
            case 'App\FormInputTextarea':
                return 'textarea';
                break;
            case 'App\FormInputDropdown':
                return 'dropdown';
                break;
            case 'App\FormInputRadio':
                return 'radio';
                break;
            case 'App\FormInputCheckbox':
                return 'checkbox';
                break;
            case 'App\FormInputDate':
                return 'date';
                break;
            case 'App\FormInputBoolean':
                return 'boolean';
                break;
            default:
                return 'error';
                break;
        }
    }

    public function getFormTypeDisplayName()
    {
        //activity type hook
        switch ($this->input_type) {
            case 'App\FormInputText':
                return 'Free text';
                break;
            case 'App\FormInputAmount':
                return 'Amount';
                break;
            case 'App\FormInputPercentage':
                return 'Percentage';
                break;
            case 'App\FormInputInteger':
                return 'Integer';
                break;
            case 'App\FormInputVideo':
                return 'Video';
                break;
            case 'App\FormInputTextarea':
                return 'Textarea';
                break;
            case 'App\FormInputBoolean':
            case 'App\FormInputDropdown':
                return 'Dropdown';
                break;
            case 'App\FormInputRadio':
                return 'Radio';
                break;
            case 'App\FormInputCheckbox':
                return 'Checkbox';
                break;
            case 'App\FormInputDate':
                return 'Date';
                break;
            default:
                return 'error';
                break;
        }
    }

    public function form(){
        return $this->belongsTo(FormSection::class,'form_section_id');
    }
}
