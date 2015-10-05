<?php

namespace App\Http\Requests\Api\V1_0;

use App\Http\Requests\JsonRequest;

class UpdateProfileRequest extends JsonRequest
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
            'first_name' => 'sometimes|required|max:255',
            'last_name' => 'sometimes|required|max:255',
            'birth_year' => 'sometimes|required|date_format:Y',
            'gender' => 'sometimes|required|in:male,female,other',
            'city' => 'sometimes|required',
            'city.id' => 'sometimes|required|exists:cities,id',
            'address' => 'sometimes|required|string|max:255',
            'phone_number' => 'sometimes|required|max:255',
            'email' => 'sometimes|required|email|unique:volunteers',
            'emergency_contact' => 'sometimes|required|max:255',
            'emergency_phone' => 'sometimes|required|string|max:255',
            'introduction' => 'sometimes|required|max:255'
        ];
    }
}
