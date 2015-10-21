<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'countries';

    protected $casts = [
        'id' => 'integer'
    ];


    public $timestamps = false;

    public function cities()
    {
        return $this->hasMany('App\City');
    }
}
