<?php

namespace App\Transformers;

use Illuminate\Contracts\Support\Arrayable;
use League\Fractal\TransformerAbstract;
use App\ProjectCustomField;

class ProjectCustomFieldTransformer extends TransformerAbstract
{
    public function transform(ProjectCustomField $value)
    {
        $item = array_except($value->toArray(), ['metadata']);

        if (isset($value->metadata) && $value->metadata instanceof Arrayable) {
            $item['metadata'] = $value->metadata->toArray();
        }

        return $item;
    }
}
