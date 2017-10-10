<?php

use Akeneo\CouplingDetector\Configuration\Configuration;
use Akeneo\CouplingDetector\Configuration\DefaultFinder;
use Akeneo\CouplingDetector\Domain\Rule;
use Akeneo\CouplingDetector\Domain\RuleInterface;

$finder = new DefaultFinder();
$finder->in('src');

$rules = [
    new Rule(
        'Nidup\Sandbox\Domain',
        ['Nidup\Sandbox\Domain', 'Faker'],
        RuleInterface::TYPE_ONLY
    ),
    new Rule(
        'Nidup\Sandbox\Application',
        ['Nidup\Sandbox\Domain', 'Nidup\Sandbox\Application'],
        RuleInterface::TYPE_ONLY
    ),
    new Rule(
        'Nidup\Sandbox\Infrastructure\Database',
        [
            'Nidup\Sandbox\Domain',
            'Nidup\Sandbox\Application',
            'Nidup\Sandbox\Infrastructure\Database',
        ],
        RuleInterface::TYPE_ONLY
    ),
    new Rule(
        'Nidup\Sandbox\Infrastructure\WebApi',
        [
            'Nidup\Sandbox\Domain',
            'Nidup\Sandbox\Application',
            'Nidup\Sandbox\Infrastructure\WebApi',
            'Akeneo\Pim\AkeneoPimClientInterface',
        ],
        RuleInterface::TYPE_ONLY
    ),
    new Rule(
        'Nidup\Sandbox\Infrastructure\Cli',
        [
            'Nidup\Sandbox\Domain',
            'Nidup\Sandbox\Application',
            'Nidup\Sandbox\Infrastructure\Cli',
            'Akeneo\Pim',
            'Symfony\Component\Console',
            'Symfony\Component\Yaml',
            'Nidup\Sandbox\Infrastructure\Database', // TODO: should be decoupled
            'Nidup\Sandbox\Infrastructure\WebApi', // TODO: should be decoupled
        ],
        RuleInterface::TYPE_ONLY
    ),
];

$config = new Configuration($rules, $finder);

return $config;