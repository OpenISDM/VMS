<?php

namespace App\Transformers;

trait CandidateKeywordsTrait
{
    public $keyword;

    public function scopeKeywordName($query, $keyword)
    {
        $collection = $query->where('name', 'LIKE', '%' . $keyword . '%')->get();

        $collection->map(function ($item, $key) use ($keyword) {
            $item->keyword = $keyword;
        });

        return $collection;
    }
}
