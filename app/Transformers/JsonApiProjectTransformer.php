<?php

namespace App\Transformers;

use App\Project;

use League\Fractal\TransformerAbstract;

class JsonApiProjectTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'managers',
        'hyperlinks',
    ];

    public function transform(Project $project)
    {
        $item = $project->toArray();

        return $item;
    }

    public function includeManagers(Project $project)
    {
        $managers = $project->managers()->get();

        if ($managers === null) {
            return $this->null();
        }

        return $this->collection($managers,
            new JsonApiManagerTransformer(), 'managers');
    }

    public function includeHyperlinks(Project $project)
    {
        $hyperlinks = $project->hyperlinks()->get();

        if ($hyperlinks === null) {
            $this->null();
        }

        return $this->collection($hyperlinks,
            new JsonApiHyperlinkTransformer(),
            'hyperlinks'
        );
    }
}
