<?php

namespace App\Services;

/**
 * TransformerSerivce class is responsible for Tansformer object
 * creation in controller.
 */
class TransformerService
{
    /**
     * Get Fractal Manager.
     *
     * @return \League\Fractal\Manager
     */
    public static function getManager()
    {
        /**
         * @TODO: The method should be changed into `newManager()` or `makeManager()`
         */
        // Set serialzer for a transformer
        $manager = new \League\Fractal\Manager();
        $manager->setSerializer(new \League\Fractal\Serializer\ArraySerializer());

        return $manager;
    }

    public static function getDataArrayManager()
    {
        // Set serialzer for a transformer
        $manager = new \League\Fractal\Manager();
        $manager->setSerializer(new \League\Fractal\Serializer\DataArraySerializer());

        return $manager;
    }

    public static function getJsonApiManager()
    {
        // Set serialzer for a transformer
        $manager = new \League\Fractal\Manager();
        $manager->setSerializer(new \League\Fractal\Serializer\JsonApiSerializer());

        return $manager;
    }

    /**
     * Get resource item object.
     *
     * @param \Illuminate\Database\Eloquent\Model $object
     * @param string                              $transformer
     * @param string                              $key
     *
     * @return \League\Fractal\Resource\Item
     */
    public static function getResourceItem($object, $transformer, $key)
    {
        // transform Experience model into array
        $resource = new \League\Fractal\Resource\Item($object, new $transformer(), $key);

        return $resource;
    }

    /**
     * Get resource collection.
     *
     * @param \Illuminate\Database\Eloquent\Model $object
     * @param string                              $transformer
     * @param string                              $key
     *
     * @return \League\Fractal\Resource\Collection
     */
    public static function getResourceCollection($object, $transformer, $key)
    {
        $resource = new \League\Fractal\Resource\Collection($object, new $transformer(), $key);

        return $resource;
    }
}
