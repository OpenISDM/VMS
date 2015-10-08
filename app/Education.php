<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    protected $table = 'educations';
    protected $fillable = ['school', 'degree', 'start_year', 'end_year'];

    public function volunteer()
    {
        return $this->belongsTo('App\Volunteer');
    }
}
