<?php

namespace App\Transformers\Project;

use League\Fractal\TransformerAbstract;
use App\Project;

class ProjectTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'managers',
        'hyperlinks'
    ];

    public function transform(Project $project)
    {
        $item = $project->toArray();

        return $item;
    }

    public function includeManagers(Project $project)
    {
        $managerCollection = $project->managers()->get();

        return $this->collection($managerCollection, new ProjectManagerTransformer);
    }

    public function includeHyperlinks(Project $project)
    {
        $hyperlinkCollection = $project->hyperlinks()->get();

        return $this->collection($hyperlinkCollection, new ProjectHyperlinkTransformer);
    }
}
