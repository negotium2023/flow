<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReferrerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('maintain_referrer');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'referrer_type' => 'required',
            'first_name' => 'required|string|max:255',
            'last_name' => 'max:255',
            'email' => 'required|email|max:255',
            'contact' => 'required|string|max:255'
        ];

    }
}
