<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CandidateKeywordsTrait;

class Equipment extends Model
{
    /*
     * For store candidiated keyword
     */
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
