<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use StringUtil;
use Storage;

class AvatarStorageService
{
    protected $avatarFileName = '';

    public function __construct()
    {
        // check if directory exists
        $avatarFullLocalPath = config('filesystems.disks.avatar.root');

        if (!is_dir($avatarFullLocalPath)) {
            if (is_writable($avatarFullLocalPath)) {
                mkdir($avatarFullLocalPath);
            } else {
                throw new \App\Exceptions\FileSystemException();
            }
        }
    }

    public function save($avatarBase64File)
    {
        //$defaultWidth = 400;

        // Avatar path
        $extension = \App\Utils\MimeUtil::getExtensionByBase64($avatarBase64File);
        $this->avatarFileName = substr(StringUtil::generateHashToken(), 0, 20) . '.' . $extension;
        $image = base64_decode($avatarBase64File);

        return Storage::disk('avatar')->put($this->avatarFileName, $image);
    }

    public function getFileName()
    {
        return $this->avatarFileName;
    }
}
