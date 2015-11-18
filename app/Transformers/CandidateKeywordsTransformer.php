<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use Illuminate\Database\Eloquent\Model;
use App\Utils\StringUtil;

class CandidateKeywordsTransformer extends TransformerAbstract
{
    public function transform($model)
    {
        $candidate = [
            'name' => $model->name,
            'id' => $model->id,
            'head_line' => StringUtil::highlightKeyword($model->keyword, $model->name)
        ];

        return $candidate;
    }
}
