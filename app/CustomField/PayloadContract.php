<?php

namespace App\CustomField;

interface PayloadContract
{
    public function get();
    public function set(array $payload);
}
