<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Project;
use App\Transformers\ManagerTransformer;
use App\Transformers\ProjectHyperlinkTransformer;

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

        return $this->collection($managerCollection, new ManagerTransformer);
    }

    public function includeHyperlinks(Project $project)
    {
        $hyperlinkCollection = $project->hyperlinks()->get();

        return $this->collection($hyperlinkCollection, new ProjectHyperlinkTransformer);
    }
}
