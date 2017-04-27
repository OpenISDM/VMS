<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectMember extends Model
{
    protected $table = 'project_volunteers';
    protected $visible = [
        'id', 'project_id', 'volunteer_id', 'status', 'is_full_profile_permit',
        'permission', 'created_at', 'updated_at',
    ];
    protected $casts = [
        'id'                     => 'integer',
        'project_id'             => 'integer',
        'volunteer_id'           => 'integer',
        'status'                 => 'integer',
        'is_full_profile_permit' => 'boolean',
        'permission'             => 'integer',
        'created_at'             => 'datetime',
        'updated_at'             => 'datetime',
    ];

    public function memberCustomFieldData()
    {
        return $this->hasMany('App\MemberCustomFieldData');
    }

    public function volunteers()
    {
        return $this->belongsToMany('App\Volunteer');
    }
}
