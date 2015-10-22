<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class StringUtil extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'stringUtil';
    }
}
