<?php

namespace App\Transformers;

use App\Project;
use League\Fractal\TransformerAbstract;

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
