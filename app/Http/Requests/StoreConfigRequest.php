<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConfigRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'onboard_days' => 'required|integer',
            'onboards_per_day' => 'required|integer',
            'enable_support' => 'required|boolean',
            'support_email' => 'required|email|max:255'
        ];
    }
}
