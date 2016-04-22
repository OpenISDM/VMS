<?php

namespace App\Traits;

use App\CustomField\Types\AbstractType;
use App\ProjectCustomField;

/**
 *
 */
trait ProjectHasCustomFieldTraits
{
    public function customFields()
    {
        return $this->hasMany('App\ProjectCustomField');
    }

    public function publishedCustomFields()
    {
        return $this->customFields()->where('is_published', '=', true);
    }

    public function customFieldsData()
    {
        return $this->hasManyThrough('App\MemberCustomFieldData',
            'App\ProjectCustomField', 'project_id', 'project_custom_field_id'
        );
    }
}
