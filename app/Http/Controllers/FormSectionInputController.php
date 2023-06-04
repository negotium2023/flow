<?php

namespace App\Http\Controllers;

use App\FormSection;
use App\FormSectionInputs;
use Illuminate\Http\Request;

class FormSectionInputController extends Controller
{
    public function getInputType($type)
    {
        //activity type hook
        switch ($type) {
            case 'text':
                return 'App\FormInputText';
                break;
            case 'heading':
                return 'App\FormInputHeading';
                break;
            case 'subheading':
                return 'App\FormInputSubheading';
                break;
            case 'amount':
                return 'App\FormInputAmount';
                break;
            case 'percentage':
                return 'App\FormInputPercentage';
                break;
            case 'integer':
                return 'App\FormInputInteger';
                break;
            case 'video':
                return 'App\FormInputVideo';
                break;
            case 'textarea':
                return 'App\FormInputTextarea';
                break;
            case 'dropdown':
                return 'App\FormInputDropdown';
                break;
            case 'radio':
                return 'App\FormInputRadio';
                break;
            case 'checkbox':
                return 'App\FormInputCheckbox';
                break;
            case 'date':
                return 'App\FormInputDate';
                break;
            case 'boolean':
                return 'App\FormInputBoolean';
                break;
            case 'document':
                return 'App\FormInputDocument';
                break;
            default:
                abort(500, 'Error');
                break;
        }
    }

    public function getInputs(Request $request){

        $section = FormSection::find($request->input('form_section_id'));

        $inputs = FormSectionInputs::where('form_section_id',$section->id)->where('input_type',$this->getInputType($request->input('aatype')))->orderBy('order')->get();

        $input = [];
        foreach ($inputs as $p){
            $input[$p->id] = $p->name;
        }
        return $input;
    }
}
