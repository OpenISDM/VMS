<?php

namespace App\Repositories;

use App\CustomField\TypeMapping;
use App\Project;
use App\ProjectCustomField;

class ProjectCustomFieldRepository
{
    public function newInstance(
        $name,
        $description,
        $required,
        $type,
        $order,
        $metadata = null,
        $isPublished = true
    ) {
        $data = [
            'name'         => $name,
            'description'  => $description,
            'required'     => $required,
            'type'         => $type,
            'order'        => $order,
            'is_published' => $isPublished,
        ];

        if (isset($metadata)) {
            $metadataClass = TypeMapping::strToMetadataClass($type);
            $data['metadata'] = new $metadataClass($metadata);
        }

        return new ProjectCustomField($data);
    }

    public function update($project, $customFieldId, $data)
    {
        $customField = $project->customFields()->where(
            'project_custom_field.id',
            '=',
            $customFieldId
        )->first();

        if (isset($data['metadata'])) {
            $metadataClass = TypeMapping::strToMetadataClass($data['type']);
            $metadata = new $metadataClass($data['metadata']);

            $customField->metadata = $metadata;
            $customField->save();

            unset($data['metadata']);
        }

        $customField->update($data);

        return $customField;
    }

    public function getAllByProject(Project $project)
    {
    }
}
