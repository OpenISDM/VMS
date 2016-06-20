<?php

namespace App\Transformers;

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
