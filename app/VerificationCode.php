<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VerificationCode extends Model
{
    protected $table = 'verification_codes';

    protected $fillable = ['code'];

    protected $casts = [
        'id' => 'integer',
    ];

    public function volunteer()
    {
        return $this->belongsTo('App\Volunteer');
    }
}
