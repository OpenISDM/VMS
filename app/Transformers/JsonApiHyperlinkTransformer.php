<?php

namespace App\Transformers;

use App\Hyperlink;
use League\Fractal\TransformerAbstract;

class JsonApiHyperlinkTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'project',
    ];

    public function transform(Hyperlink $hyperlink)
    {
        $item = $hyperlink->toArray();

        return $item;
    }

    public function includeProject(Hyperlink $hyperlink)
    {
        $project = $hyperlink->project();

        if ($project === null) {
            $this->null();
        }

        return $this->collection($project,
            new JsonApiProjectTransformer(),
            'project'
        );
    }
}
