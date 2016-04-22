<?php

namespace App\CustomField;

use Illuminate\Contracts\Support\Arrayable;

class Payload implements PayloadContract, \Serializable, Arrayable
{
    protected $data;

    public function __construct(array $data)
    {
        $this->set($data);
    }

    public function get()
    {
        return $this->data;
    }

    public function set(array $data)
    {
        $this->data = $data;

        return $this;
    }

    public function serialize()
    {
        return serialize($this->data);
    }

    public function unserialize($data)
    {
        $this->data = unserialize($data);
    }

    public function toArray()
    {
        return $this->data;
    }
}
