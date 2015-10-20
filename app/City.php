<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'cities';

    protected $casts = [
        'id' => 'integer'
    ];

    public $timestamps = false;

    public function volunteers()
    {
        return $this->hasMany('App\Volunteer');
    }

    public function country()
    {
        return $this->belongsTo('App\Country');
    }
}
