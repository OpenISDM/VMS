<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\CustomField\Payload;

class MemberCustomFieldData extends Model
{
    protected $table = 'member_custom_field_data';
    protected $fillable = ['data', 'project_custom_field_id', 'member_id'];
    protected $casts = [
        'id' => 'integer',
    ];
    protected $visible = ['id', 'data', 'created_at', 'updated_at'];

    public function projectCustomField()
    {
        return $this->belongsTo('App\ProjectCustomField');
    }

    public function member()
    {
        return $this->belongsTo('App\ProjectMember');
    }

    public function setDataAttribute(Payload $value)
    {
        $this->attributes['data'] = serialize($value);
    }

    public function getDataAttribute($value)
    {
        return unserialize($value);
    }
}
