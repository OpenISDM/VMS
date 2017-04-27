<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hyperlink extends Model
{
    protected $table = 'hyperlinks';
    protected $fillable = ['name', 'link'];
    protected $casts = [
        'id' => 'integer',
    ];
    protected $visible = ['id', 'name', 'link'];

    public function project()
    {
        return $this->belongsTo('App\Project');
    }
}
