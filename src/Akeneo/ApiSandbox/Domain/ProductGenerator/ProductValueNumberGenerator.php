<?php

namespace Akeneo\ApiSandbox\Domain\ProductGenerator;

use Faker\Factory;
use Faker\Generator;
use Akeneo\ApiSandbox\Domain\Model\Attribute;
use Akeneo\ApiSandbox\Domain\Model\ProductValue;

class ProductValueNumberGenerator implements ProductValueGenerator
{
    /** @var Generator */
    private $generator;

    public function __construct()
    {
        $this->generator = Factory::create();
    }

    public function generate(Attribute $attribute, $channelCode, $localeCode): ProductValue
    {
        $data = $this->generator->numberBetween(1, 100);

        return new ProductValue($attribute, $data, $localeCode, $channelCode);
    }
}