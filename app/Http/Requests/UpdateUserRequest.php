<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$this->user->id,
            'avatar' => 'nullable|image|dimensions:ratio=1',
            'role.*' => 'integer|exists:roles,id',
            'division.*' => 'integer|exists:divisions,id',
            'region.*' => 'integer|exists:regions,id',
            'area.*' => 'integer|exists:areas,id',
            'office.*' => 'integer|exists:offices,id'
        ];
    }
}
