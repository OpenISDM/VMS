<?php

namespace App\Http\Responses;

/**
 * Avatar object for response
 */
class Avatar
{
    private $avatarName;

    private $avatarHost;

    private $avatarRootPath;

    public function __construct()
    {
        $this->avatarHost = config('vms.avatarHost');
        $this->avatarRootPath = config('vms.avatarRootPath');
    }

    public function __set($name, $value)
    {
        if ($name == "avatar_name") {
            $this->avatarName = $value;
        }
    }

    public function __get($name)
    {
        if ($name == "avatar_name") {
            return $this->avatarName;
        } elseif ($name == "avatar_url") {
            $avatarUrl = $this->avatarHost . '/' . $this->avatarRootPath . '/' . $this->avatarName;

            return $avatarUrl;
        }
    }
}
