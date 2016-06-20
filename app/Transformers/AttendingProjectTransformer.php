<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Project;

class AttendingProjectTransformer extends TransformerAbstract
{

    public function transform(Project $project)
    {
        $item = [];
        $item['project'] = $project->toArray();
        $item['attending_at'] = $project->pivot->created_at;

        return $item;
    }
}
