<?php

namespace App\Utils;

class ArrayUtil
{
    public static function isIndexExceed($list, $max)
    {
        if (count($list) <= $max) {
            return true;
        }

        return false;
    }

    public static function getUnexisting($list, $existingIndexes)
    {
        foreach ($existingIndexes as $index) {
            unset($list[$index]);
        }

        return $list;
    }
}