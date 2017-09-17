<?php

namespace Nidup\Sandbox\Application;

use Faker\Factory;
use Faker\Generator;
use Nidup\Sandbox\Domain\Attribute;
use Nidup\Sandbox\Domain\AttributeTypes;
use Nidup\Sandbox\Domain\ChannelRepository;
use Nidup\Sandbox\Domain\Family;
use Nidup\Sandbox\Domain\FamilyRepository;
use Nidup\Sandbox\Domain\LocaleRepository;
use Nidup\Sandbox\Domain\Product;
use Nidup\Sandbox\Domain\ProductValue;
use Nidup\Sandbox\Domain\ProductValues;

class ProductGenerator
{
    /** @var ChannelRepository */
    private $channelRepository;
    /** @var LocaleRepository */
    private $localeRepository;
    /** @var FamilyRepository */
    private $familyRepository;
    /** @var  Generator */
    private $identifierGenerator;
    /** @var ProductValueGeneratorRegistry */
    private $valueGeneratorRegistry;

    public function __construct(
        ChannelRepository $channelRepository,
        LocaleRepository $localeRepository,
        FamilyRepository $familyRepository
    ) {
        $this->channelRepository = $channelRepository;
        $this->localeRepository = $localeRepository;
        $this->familyRepository = $familyRepository;
        $this->identifierGenerator = Factory::create();
        $this->valueGeneratorRegistry = new ProductValueGeneratorRegistry();
    }

    public function generate(): Product
    {
        $identifier = $this->identifierGenerator->ean13();
        $family = $this->getRandomFamily();
        $values = $this->getRandomValues($family);

        return new Product($identifier, $family, $values, []);
    }

    private function getRandomFamily(): Family
    {
        $families = $this->familyRepository->all();

        return $families[rand(0, count($families) -1 )];
    }

    private function getRandomValues(Family $family): ProductValues
    {
        $attributes = $family->getAttributes();
        $values = new ProductValues();
        /** @var Attribute $attribute */
        foreach ($attributes as $attribute) {
            $this->generateValues($values, $attribute);
        }

        return $values;
    }

    private function generateValues(ProductValues $values, Attribute $attribute)
    {
        if (!$this->valueGeneratorRegistry->support($attribute)) {
            return;
        }
        $generator = $this->valueGeneratorRegistry->get($attribute);

        if ($attribute->isScopable() && $attribute->isLocalizable()) {
            foreach ($this->channelRepository->all() as $channel) {
                foreach ($channel->getLocales() as $locale) {
                    $values->addValue($generator->generate($attribute, $channel->getCode(), $locale->getCode()));
                }
            }
        } else if ($attribute->isScopable()) {
            foreach ($this->channelRepository->all() as $channel) {
                $values->addValue($generator->generate($attribute, $channel->getCode(), null));
            }
        } else if ($attribute->isLocalizable()) {
            foreach ($this->localeRepository->all() as $locale) {
                $values->addValue($generator->generate($attribute, null, $locale->getCode()));
            }
        } else {
            $values->addValue($generator->generate($attribute, null, null));
        }
    }
}