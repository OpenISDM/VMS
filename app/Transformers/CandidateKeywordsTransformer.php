<?php

namespace App\Transformers;

use App\Utils\StringUtil;
use League\Fractal\TransformerAbstract;

class CandidateKeywordsTransformer extends TransformerAbstract
{
    public function transform($model)
    {
        $candidate = [
            'name'      => $model->name,
            'id'        => $model->id,
            'head_line' => StringUtil::highlightKeyword($model->keyword, $model->name),
        ];

        return $candidate;
    }
}
