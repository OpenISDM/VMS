<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use StringUtil;
use Storage;

class AvatarStorageService
{
    protected $avatarFileName = '';
    protected $avatarFullLocalPath;

    public function __construct()
    {
        // check if directory exists
        $this->avatarFullLocalPath = config('filesystems.disks.avatar.root');
        $driver = config('filesystems.disks.avatar.driver');

        if ($driver == 'local') {
            if (!is_dir($this->avatarFullLocalPath)) {
                if (is_writable($this->avatarFullLocalPath)) {
                    mkdir($this->avatarFullLocalPath);
                } else {
                    throw new \App\Exceptions\FileSystemException();
                }
            }
        }
    }

    public function save($avatarBase64File)
    {
        //$defaultWidth = 400;

        // Avatar path
        $extension = \App\Utils\MimeUtil::getExtensionByBase64($avatarBase64File);

        $this->avatarFileName = substr(StringUtil::generateHashToken(), 0, 20) . '.' . $extension;
        $image = base64_decode($this->getBase64Data($avatarBase64File));

        return Storage::disk('avatar')->put($this->avatarFullLocalPath . '/' . $this->avatarFileName, $image);
    }

    public function delete($fileName)
    {
        return Storage::disk('avatar')->delete($fileName);
    }

    protected function getBase64Data($data)
    {
        $divider = strpos($data, ',');
        return substr($data, $divider + 1);
    }

    public function getFileName()
    {
        return $this->avatarFileName;
    }
}
