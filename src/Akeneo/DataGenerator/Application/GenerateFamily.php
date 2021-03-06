<?php

namespace Akeneo\DataGenerator\Application;

class GenerateFamily
{
    private $attributes;

    public function __construct(int $attributes)
    {
        $this->attributes = $attributes;
    }

    public function getAttributes(): int
    {
        return $this->attributes;
    }
}
