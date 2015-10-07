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

    public static function getLastId($data)
    {
        $tempList = explode('_', $data);
        $maxIndex = count($tempList) - 1;
        
        return $tempList[$maxIndex];
    }
}
