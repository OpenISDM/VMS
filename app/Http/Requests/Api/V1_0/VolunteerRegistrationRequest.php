<?php

namespace App\Http\Requests\Api\V1_0;

use App\Http\Requests\AbstractJsonRequest;

/**
 * The class is responsible for validating the volunteer data on registration
 */
class VolunteerRegistrationRequest extends AbstractJsonRequest
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
            'password' => 'required|between:6,255|regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$/',
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'birth_year' => 'date_format:Y',
            'gender' => 'required|in:male,female,other',
            'city' => 'required',
            'city.id' => 'required|exists:cities,id',
            'address' => 'sometimes|required|string|max:255',
            'phone_number' => 'sometimes|required|max:255',
            'email' => 'required|email|unique:volunteers',
            'emergency_contact' => 'sometimes|required|max:255',
            'emergency_phone' => 'sometimes|required|string|max:255',
            'introduction' => 'max:255',
            'avatar' => 'sometimes'
        ];
    }
}
