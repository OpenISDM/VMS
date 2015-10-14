<?php

namespace App\Utils;

class StringUtil
{
    public static function generateHashToken()
    {
        // generate
        $randomString = str_random(100) . time();
        return hash('sha256', $randomString);
    }
}
