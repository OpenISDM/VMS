<?php

namespace App\Serializer;

use League\Fractal\Serializer\ArraySerializer;

class MyArraySerializer extends ArraySerializer
{
    public function collection($resourceKey, array $data)
    {
        echo 'resource key = ' . $resourceKey;

        return array($resourceKey ?: 'data' => $data);
    }
}