<?php
declare(strict_types=1);

namespace Akeneo\DataGenerator\Infrastructure\Cli\Catalog;

final class Attributes
{
    /** @var int */
    private $count;

    /** @var int */
    private $percentageOfLocalizable;

    /** @var int */
    private $percentageOfScopable;

    /** @var int */
    private $percentageOfLocalizableAndScopable;

    /** @var int */
    private $percentageOfUseableInGrid;

    /**
     * @param int $count
     * @param int $percentageOfLocalizable
     * @param int $percentageOfScopable
     * @param int $percentageOfLocalizableAndScopable
     * @param int $percentageOfUseableInGrid
     */
    public function __construct(
        int $count,
        int $percentageOfLocalizable,
        int $percentageOfScopable,
        int $percentageOfLocalizableAndScopable,
        int $percentageOfUseableInGrid
    ) {
        $this->count = $count;
        $this->percentageOfLocalizable = $percentageOfLocalizable;
        $this->percentageOfScopable = $percentageOfScopable;
        $this->percentageOfLocalizableAndScopable = $percentageOfLocalizableAndScopable;
        $this->percentageOfUseableInGrid = $percentageOfUseableInGrid;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->count;
    }

    /**
     * Returns percentage of localizable wanted.
     *
     * @return int
     */
    public function percentageOfLocalizable(): int
    {
        return $this->percentageOfLocalizable;
    }

    /**
     * Returns percentage of scopable wanted.
     *
     * @return int
     */
    public function percentageOfScopable(): int
    {
        return $this->percentageOfScopable;
    }

    /**
     * Returns percentage of localizable and scopable wanted.
     *
     * @return int
     */
    public function percentageOfLocalizableAndScopable(): int
    {
        return $this->percentageOfLocalizableAndScopable;
    }

    /**
     * Returns percentage of useable in grid wanted.
     *
     * @return int
     */
    public function percentageOfUseableInGrid(): int
    {
        return $this->percentageOfUseableInGrid;
    }
}
