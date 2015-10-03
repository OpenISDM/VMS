<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VerificationCode extends Model
{
    protected $table = 'verification_codes';

    public function volunteer()
    {
        return $this->belongsTo('App\Volunteer');
    }
}
