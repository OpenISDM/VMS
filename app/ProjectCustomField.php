<?php

namespace App;

use App\CustomField\Metadata;
use App\CustomField\TypeMapping;
use Illuminate\Database\Eloquent\Model;

class ProjectCustomField extends Model
{
    protected $table = 'project_custom_field';
    protected $fillable = ['name', 'description', 'required', 'type', 'is_published', 'metadata', 'order'];
    protected $casts = [
        'id'           => 'integer',
        'required'     => 'boolean',
        'type'         => 'integer',
        'is_published' => 'boolean',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];
    protected $visible = ['id', 'name', 'description', 'required', 'type',
        'is_published', 'metadata', 'order', 'created_at', 'updated_at',
    ];

    public function memberCustomFieldData()
    {
        return $this->hasMany('App\MemberCustomFieldData');
    }

    public function project()
    {
        return $this->belongsTo('App\Project');
    }

    public function getTypeAttribute($data)
    {
        return TypeMapping::intToStr($data);
    }

    public function setTypeAttribute($value)
    {
        $this->attributes['type'] = TypeMapping::strToInt($value);
    }

    public function setMetadataAttribute(Metadata $value)
    {
        $this->attributes['metadata'] = $this->serializeMetadata($value);
    }

    public function getMetadataAttribute($value)
    {
        return $this->unserializeMetadata($value);
    }

    protected function serializeMetadata(Metadata $data)
    {
        return serialize($data);
    }

    protected function unserializeMetadata($data)
    {
        return unserialize($data);
    }
}
