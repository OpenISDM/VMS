<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Transformers\CandidateKeywordsTrait;

class Equipment extends Model
{
    use CandidateKeywordsTrait;
    
    protected $table = 'equipment';

    protected $fillable = ['name'];

    protected $visible = ['id', 'name'];

    protected $casts = [
        'id' => 'integer'
    ];

    public function volunteers()
    {
        return $this->belongsToMany('App\Volunteer');
    }
}
