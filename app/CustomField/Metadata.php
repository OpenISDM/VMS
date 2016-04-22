<?php

namespace App\CustomField;

use Illuminate\Contracts\Support\Arrayable;

abstract class Metadata implements \Serializable, Arrayable
{
    protected $data;

    public function __construct(array $data)
    {
        $this->set($data);
    }

    public function serialize()
    {
        return serialize($this->data);
    }

    public function unserialize($data)
    {
        $this->data = unserialize($data);
    }

    public function get()
    {
        return $this->data;
    }

    public function set(array $data)
    {
        $this->data = $data;
    }

    public function toArray()
    {
        return $this->data;
    }
}
