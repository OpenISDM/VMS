<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use App\Utils\ValidatorUtil;

/**
 * Reference from: 
 * https://laracasts.com/discuss/channels/laravel/how-to-validate-json-input-using-requests
 */
abstract class JsonRequest extends Request
{
    /**
     * Get the validator instanct
     * 
     * @return \Illuminate\Foundation\Validation\Validator
     */
    protected function getValidatorInstance()
    {
        $factory = $this->container->make('Illuminate\Validation\Factory');

        if (method_exists($this, 'validator')) {
            return $this->container->call([$this, 'validator'], compact('factory'));
        }
        
        return $factory->make($this->json()->all(), 
                              $this->container->call([$this, 'rules']), 
                              $this->messages(), $this->attributes()
                              );        
    }

    /**
     * Determine if the current request is asking for JSON in return.
     *
     * @return bool
     */
    public function wantsJson()
    {
        $acceptable = $this->getAcceptableContentTypes();

        return isset($acceptable[0]) && 
            preg_match("/^(application?\/)?(([\w]*\.){2}(\w)*(json)?)?/", $acceptable[0] );
    }

    public function messages()
    {
        return [
            'required' => "missing_field"
        ];
    }

    
    protected function formatErrors(Validator $validator)
    {
        $errorArray = $validator->errors()->toArray();
        $formattedResult = ValidatorUtil::formatter($errorArray);

        return [
            "message" => "Validation failed",
            "errors" => $formattedResult
        ];
    }

}