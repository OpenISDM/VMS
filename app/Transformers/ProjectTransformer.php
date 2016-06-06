<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Project;

class ProjectTransformer extends TransformerAbstract
{
    public function transform(Project $project)
    {
        $managerVisibleField = [
            'volunteers.id',
            'username',
            'first_name',
            'last_name',
            'avatar_path'
        ];

        $item = $project->toArray();
        $managerCollection = $project->managers()->get($managerVisibleField)->toArray();
        $hyperlinkCollection = $project->hyperlinks()->get()->toArray();

        $item['managers'] = $managerCollection;
        $item['hyperlinks'] = $hyperlinkCollection;

        return $item;
    }
}
