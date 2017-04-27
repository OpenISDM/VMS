<?php

namespace App\Transformers\Project;

use App\Hyperlink;
use League\Fractal\TransformerAbstract;

class ProjectHyperlinkTransformer extends TransformerAbstract
{
    public function transform(Hyperlink $hyperlink)
    {
        $item = $hyperlink->toArray();

        return $item;
    }
}
