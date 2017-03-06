<?php

namespace App\Transformers\Project;

use League\Fractal\TransformerAbstract;
use App\Project;

class ProjectQueryTransformer extends TransformerAbstract
{
    public function transform($project)
    {
        $managerVisibleField = [
            'volunteers.id',
            'username',
            'first_name',
            'last_name',
            'avatar_path'
        ];

        $model = Project::find($project->id);
        $item = $model->toArray();

        $managerCollection = $model->managers()->get($managerVisibleField)->toArray();
        $hyperlinkCollection = $model->hyperlinks()->get()->toArray();

        $item['managers'] = $managerCollection;
        $item['hyperlinks'] = $hyperlinkCollection;

        return $item;
    }
}
