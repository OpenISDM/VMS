<?php

namespace App\Transformers\Project;

use League\Fractal\TransformerAbstract;
use App\Hyperlink;

class ProjectHyperlinkTransformer extends TransformerAbstract
{
    public function transform(Hyperlink $hyperlink)
    {
        $item = $hyperlink->toArray();

        return $item;
    }
}
