<?php

namespace App\Utils;

class MimeUtil
{
    public static function getExtension($mime)
    {
        switch ($mime) {
            case 'image/png':
                return 'png';
            case 'image/jpg';
                return 'jpg';
            case 'image/jpeg':
                return 'jpeg';
            default:
                return false;
        }
    }
}
