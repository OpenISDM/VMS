<?php

namespace App\Http\Requests\Api\V1_0;

use App\Http\Requests\AbstractJsonRequest;

class UpdateProfileRequest extends AbstractJsonRequest
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
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'birth_year' => 'required|date_format:Y',
            'gender' => 'required|in:male,female,other',
            'introduction' => 'required|max:255',
            'city' => 'required',
            'city.id' => 'required|exists:cities,id',
            'location' => 'required|string|max:255',
            'phone_number' => 'required|max:255',
            'emergency_contact' => 'required|max:255',
            'emergency_phone' => 'required|string|max:255',
        ];
    }
}
