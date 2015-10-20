<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    protected $table = 'educations';
    
    protected $fillable = ['school', 'degree', 'field_of_study', 'start_year', 'end_year'];

    protected $casts = [
        'id' => 'integer',
        'volunteer_id' => 'integer'
    ];


    public function volunteer()
    {
        return $this->belongsTo('App\Volunteer');
    }
}
