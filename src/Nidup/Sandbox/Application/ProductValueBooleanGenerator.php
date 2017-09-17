<?php

namespace Nidup\Sandbox\Application;

use Nidup\Sandbox\Domain\Attribute;
use Nidup\Sandbox\Domain\ProductValue;

class ProductValueBooleanGenerator implements ProductValueGenerator
{
    public function generate(Attribute $attribute, $channelCode, $localeCode): ProductValue
    {
        $data = rand(0, 1) == 1;

        return new ProductValue($attribute, $data, $localeCode, $channelCode);
    }
}