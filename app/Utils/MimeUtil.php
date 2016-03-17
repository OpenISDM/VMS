<?php

namespace App\Utils;

use App\Exceptions\TypeErrorException;

class MimeUtil
{
    public static function getExtension($mime)
    {
        switch ($mime) {
            case 'image/png':
                return 'png';
            case 'image/jpg':
                return 'jpg';
            case 'image/jpeg':
                return 'jpeg';
            default:
                return false;
        }
    }

    public static function getExtensionByBase64($data)
    {
        $startPosition = strpos($data, ':');

        if (!$startPosition) {
            throw new TypeErrorException();
        }

        $data = substr($data, $startPosition+1);
        $endPosition = strpos($data, ';');

        if (!$startPosition || !$endPosition) {
            throw new TypeErrorException();
        }

        $mime = substr($data, 0, $endPosition);

        if ($mime) {
            $extension = self::getExtension($mime);

            if (!$extension) {
                throw new TypeErrorException();
            }

            return $extension;
        } else {
            throw new TypeErrorException();
        }
    }
}
