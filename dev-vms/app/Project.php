<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{

    protected $guarded = [];

    //
    public function processes()
    {
        return $this->hasMany('App\Process');
    }
}

