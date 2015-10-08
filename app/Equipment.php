<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $table = 'equipment';

    protected $fillable = ['name'];

    public function volunteers()
    {
        return $this->belongsToMany('App\Volunteer');
    }
}
