<?php

namespace spec\Akeneo\ApiSandbox\Domain;

use Akeneo\ApiSandbox\Domain\Exception\NoChildrenCategoryDefinedException;
use Akeneo\ApiSandbox\Domain\Exception\NoFamilyDefinedException;
use Akeneo\ApiSandbox\Domain\Model\Attribute;
use Akeneo\ApiSandbox\Domain\Model\AttributeProperties;
use Akeneo\ApiSandbox\Domain\Model\Category;
use Akeneo\ApiSandbox\Domain\Model\CategoryRepository;
use Akeneo\ApiSandbox\Domain\Model\ChannelRepository;
use Akeneo\ApiSandbox\Domain\Model\CurrencyRepository;
use Akeneo\ApiSandbox\Domain\Model\Family;
use Akeneo\ApiSandbox\Domain\Model\FamilyRepository;
use Akeneo\ApiSandbox\Domain\Model\LocaleRepository;
use PhpSpec\ObjectBehavior;

class ProductGeneratorSpec extends ObjectBehavior
{
    function let(
        ChannelRepository $channelRepository,
        LocaleRepository $localeRepository,
        CurrencyRepository $currencyRepository,
        FamilyRepository $familyRepository,
        CategoryRepository $categoryRepository
    ) {
        $this->beConstructedWith(
            $channelRepository, $localeRepository, $currencyRepository, $familyRepository, $categoryRepository
        );
    }

    function it_generates_a_product (
        $familyRepository,
        $categoryRepository,
        Family $family,
        Attribute $sku,
        AttributeProperties $skuProperties,
        Category $children
    ) {
        $familyRepository->count()->willReturn(1);
        $familyRepository->all()->willReturn([$family]);
        $family->getAttributes()->willReturn([$sku]);
        $sku->getType()->willReturn('pim_catalog_text');
        $sku->isScopable()->willReturn(false);
        $sku->isLocalizable()->willReturn(false);
        $sku->getProperties()->willReturn($skuProperties);

        $categoryRepository->countChildren()->willReturn(1);
        $categoryRepository->allChildren()->willReturn([$children]);
        $children->getCode()->willReturn('clothes');

        $this->generateWithImages();
    }

    function it_throws_an_exception_when_no_family_exists ($familyRepository)
    {
        $familyRepository->count()->willReturn(0);
        $this->shouldThrow(
            new NoFamilyDefinedException("At least one family should exist")
        )->during(
            'generateWithoutImages',
            []
        );
    }

    function it_throws_an_exception_when_no_children_category_exists (
        $familyRepository,
        $categoryRepository,
        Family $family,
        Attribute $sku,
        AttributeProperties $skuProperties
    )
    {
        $familyRepository->count()->willReturn(1);
        $familyRepository->all()->willReturn([$family]);
        $family->getAttributes()->willReturn([$sku]);
        $sku->getType()->willReturn('pim_catalog_text');
        $sku->isScopable()->willReturn(false);
        $sku->isLocalizable()->willReturn(false);
        $sku->getProperties()->willReturn($skuProperties);

        $categoryRepository->countChildren()->willReturn(0);

        $this->shouldThrow(
            new NoChildrenCategoryDefinedException("At least one children category should exist")
        )->during(
            'generateWithoutImages',
            []
        );
    }
}