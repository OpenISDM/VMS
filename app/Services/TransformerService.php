<?php
namespace App\Services;

class TransformerService
{
    public static function getManager()
    {
        // Set serialzer for a transformer
        $manager = new \League\Fractal\Manager();
        $manager->setSerializer(new \League\Fractal\Serializer\ArraySerializer());

        return $manager;
    }

    public static function getResourceItem($object, $transformer, $key) 
    {   
        // transform Experience model into array
        $resource = new \League\Fractal\Resource\Item($object, new $transformer, $key);

        return $resource;
    }
}
