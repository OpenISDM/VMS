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

    public static function getNonexistent($list, $existingIndexes)
    {
        foreach ($existingIndexes as $index) {
            unset($list[$index]);
        }

        return $list;
    }

    public static function combinedArray(array $data, $keyName, $combinedAsKey, $combinedKeys)
    {
        $combinedArray = [];
        $collection = collect($data);

        $collection->each(function ($value) use (&$combinedArray, $keyName, $combinedAsKey, $combinedKeys) {
            $combined = array_only($value, $combinedKeys);

            if (isset($combinedArray[$value[$keyName]])) {
                if (empty($combinedArray[$value[$keyName]][$combinedAsKey])) {
                    $combinedArray[$value[$keyName]][$combinedAsKey][] = $combined;
                } else {
                    $combinedArray[$value[$keyName]][$combinedAsKey][] = $combined;
                }
            } else {
                $origin = array_except($value, $combinedKeys);

                $combinedArray[$value[$keyName]] = $origin;
                $combinedArray[$value[$keyName]][$combinedAsKey][] = $combined;
            }
        });

        return $combinedArray;
    }
}
