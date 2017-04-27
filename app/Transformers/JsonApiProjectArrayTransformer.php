<?php

namespace App\Transformers;

use App\Project;
use League\Fractal\TransformerAbstract;

class JsonApiProjectArrayTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'managers',
        'hyperlinks',
    ];

    public function transform($project)
    {
        $item = Project::find($project->id)->toArray();

        return $item;
    }

    public function includeManagers($project)
    {
        $managers = Project::find($project->id)->managers()->get();

        if ($managers === null) {
            return $this->null();
        }

        return $this->collection($managers,
            new JsonApiManagerTransformer(), 'managers');
    }

    public function includeHyperlinks($project)
    {
        $hyperlinks = Project::find($project->id)->hyperlinks()->get();

        if ($hyperlinks === null) {
            $this->null();
        }

        return $this->collection($hyperlinks,
            new JsonApiHyperlinkTransformer(),
            'hyperlinks'
        );
    }
}
