<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    //
    public function processes()
    {
        return $this->hasMany('App\Process');
    }
}

