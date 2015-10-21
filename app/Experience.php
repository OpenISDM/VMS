<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    protected $table = 'experiences';
    
    protected $fillable = ['company', 'job_title', 'start_year', 'end_year'];

    protected $casts = [
        'id' => 'integer',
        'volunteer_id' => 'integer'
    ];

    public function volunteer()
    {
        return $this->belongsTo('App\Volunteer');
    }
}
