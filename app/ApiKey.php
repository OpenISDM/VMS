<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    protected $table = 'api_keys';

    protected $fillable = ['api_key'];

    protected $casts = [
        'id' => 'integer',
    ];
}
