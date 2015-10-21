<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Image;
use App\Utils\StringUtil;

class AvatarStorageService
{
    protected $avatarLocalRootPath = '';
    protected $avatarFileName = '';
    protected $avatarFullLocalPath = '';

    public function __construct()
    {
        $this->avatarLocalRootPath = public_path() . '/' . config('vms.avatarRootPath');

        // check if directory exists
        if (!is_dir($this->avatarLocalRootPath)) {
            if(is_writable($this->avatarLocalRootPath)) {
                mkdir($this->avatarFullLocalPath);
            } else {
                throw \App\Exceptions\FileSystemException();
            }
        }
    }

    public function save($avatarBase64File)
    {
        $defaultWidth = 400;
        $image = Image::make($avatarBase64File);

        // Check avatar width
        if ($image->width() > $defaultWidth) {
            $image->resize($defaultWidth, null);
        }

        // Avatar path
        // TODO: Need to refactor into helper functions
        $extension = \App\Utils\MimeUtil::getExtension($image->mime());
        $this->avatarFileName = StringUtil::generateHashToken() . '.' . $extension;
        $this->avatarFullLocalPath = $this->avatarLocalRootPath . '/' . $this->avatarFileName;

        $image->save($this->avatarFullLocalPath, 100);
    }

    public function getLocalRootPath()
    {
        return $this->avatarLocalPath;
    }

    public function getFileName()
    {
        return $this->avatarFileName;
    }

    public function getFullLocalPath()
    {
        return $this->avatarFullLocalPath;
    }
}
