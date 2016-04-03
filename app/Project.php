<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'projects';
    protected $fillable = ['name', 'description', 'is_published', 'permission',
        'organization'];
    protected $casts = [
      'id' => 'integer',
      'is_published' => 'boolean',
      'permission' => 'integer',
      'created_at' => 'datetime',
      'updated_at' => 'datetime'
    ];
    protected $visible = ['id', 'name', 'description', 'organization', 'is_published',
        'permission', 'created_at', 'updated_at', 'managers', 'hyperlinks'
    ];

    public function hyperlinks()
    {
        return $this->hasMany('App\Hyperlink');
    }

    public function managers()
    {
        return $this->belongsToMany('App\Volunteer',
        'project_manager_project',
        'project_id',
        'project_manager_id');
    }

    public function members()
    {
        return $this->belongsToMany('App\Volunteer',
            'project_volunteers',
            'project_id',
            'volunteer_id'
        );
    }
}
