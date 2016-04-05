<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Hyperlink;
use App\Transformers\JsonApiProjectTransformer;

class JsonApiHyperlinkTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'project'
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
