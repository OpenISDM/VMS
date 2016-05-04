<?php

namespace App\CustomField;

class TypeMapping
{
    private static $typeStrToIntMapping;
    private static $typeIntToStrMapping;

    public static function strToInt($key)
    {
        self::mappingType();

        if (isset(self::$typeStrToIntMapping[$key]['number'])) {
            return self::$typeStrToIntMapping[$key]['number'];
        }

        return null;
    }

    public static function strToMetadataClass($key)
    {
        self::mappingType();

        if (isset(self::$typeStrToIntMapping[$key]['metadata'])) {
            return self::$typeStrToIntMapping[$key]['metadata'];
        }

        return null;
    }

    public static function intToStr($key)
    {
        self::mappingType();

        if (isset(self::$typeIntToStrMapping[$key]['type'])) {
            return self::$typeIntToStrMapping[$key]['type'];
        }

        return null;
    }

    public static function intToClass($key)
    {
        self::mappingType();

        if (isset(self::$typeIntToStrMapping[$key]['class'])) {
            return self::$typeIntToStrMapping[$key]['class'];
        }

        return null;
    }

    protected static function mappingType()
    {
        if (isset(static::$typeStrToIntMapping) && isset(static::$typeIntToStrMapping)) {
            return;
        }

        /**
         * TODO: Check the configuration string exists
         */
        $customFieldTypeConfig = config('constants.custom_field_type');
        static::$typeStrToIntMapping = $customFieldTypeConfig;
        static::$typeIntToStrMapping = self::reverseMapping($customFieldTypeConfig, 'type', 'number');
    }

    private static function reverseMapping($value, $originKeyName, $needleReversedKeyName)
    {
        $collection = collect($value);
        $reversed = [];
        $collection->each(function ($item, $key) use (&$reversed, $originKeyName, $needleReversedKeyName) {
            $mappingValue = [];
            $mappingKeyName = $item[$needleReversedKeyName];

            unset($item[$needleReversedKeyName]);

            $mappingValue[$originKeyName] = $key;

            foreach ($item as $itemKey => $itemValue) {
                $mappingValue[$itemKey] = $itemValue;
            }

            $reversed[$mappingKeyName] = $mappingValue;
        });

        return $reversed;
    }
}
