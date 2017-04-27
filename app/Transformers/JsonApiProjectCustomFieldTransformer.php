<?php

namespace App\Transformers;

use App\ProjectCustomField;
use Illuminate\Contracts\Support\Arrayable;
use League\Fractal\TransformerAbstract;

class JsonApiProjectCustomFieldTransformer extends TransformerAbstract
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
