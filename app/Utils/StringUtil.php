<?php

namespace App\Utils;

use Hash;

class StringUtil
{
    public static function generateHashToken()
    {
        // generate 
        $randomString = str_random(100) . time();
        return Hash::make($randomString);
    }
}