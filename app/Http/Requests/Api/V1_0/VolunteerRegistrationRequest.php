<?php

namespace App\Http\Requests\Api\V1_0;

use App\Http\Requests\JsonRequest;

class VolunteerRegistrationRequest extends JsonRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username' => 'required|unique:volunteers|max:255',
            'password' => 'required|between:6,255',
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'birth_year' => 'numeric',
            'gender' => 'required|in:male,female,other',
            'city' => 'required',
            'city.id' => 'required',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|max:255',
            'email' => 'required|email|unique:volunteers',
            'emergency_contact' => 'required|max:255',
            'emergency_phone' => 'required|string|max:255',
        ];
    }
}