<?php

/**
 * The request defines the JSON format in HTTP request for verifying if the
 * password resetting information is availible.
 *
 * @Author: Yi-Ming, Huang <aming>
 * @Date:   2016-07-14T14:39:52+08:00
 * @Email:  ym.huang0808@gmail.com
 * @Project: VMS
 * @Last modified by:   aming
 * @Last modified time: 2016-07-29T14:26:39+08:00
 * @License: GPL-3
 */

namespace App\Http\Requests\Api\V1_0;

use App\Http\Requests\AbstractJsonRequest;

class VerifyPasswordResetRequest extends AbstractJsonRequest
{
    /**
     * Because VerifyPasswordResetRequest is for public, it always return true.
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
            'email' => 'required|email|exists:volunteers,email',
            'token' => 'required',
        ];
    }
}
