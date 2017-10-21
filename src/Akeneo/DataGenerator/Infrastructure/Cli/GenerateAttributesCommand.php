<?php

namespace Akeneo\DataGenerator\Infrastructure\Cli;

use Akeneo\DataGenerator\Application\GenerateAttribute;
use Akeneo\DataGenerator\Application\GenerateAttributeHandler;
use Akeneo\DataGenerator\Domain\AttributeGenerator;
use Akeneo\DataGenerator\Infrastructure\Cli\ApiClient\ApiClientFactory;
use Akeneo\DataGenerator\Infrastructure\WebApi\ReadRepositories;
use Akeneo\DataGenerator\Infrastructure\WebApi\WriteRepositories;
use Akeneo\Pim\AkeneoPimClientInterface;
use Akeneo\Pim\Exception\HttpException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateAttributesCommand extends Command
{
    protected function configure()
    {
        $this->setName('akeneo:api:generate-attributes')
            ->setDescription('Import generated attributes through the Akeneo PIM Web API')
            ->addArgument('number', InputArgument::REQUIRED, 'Number of attributes to generate')
            ->addOption('useable-in-grid', null, InputOption::VALUE_NONE, 'Useable in product grid');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $number = $input->getArgument('number');
        $inGrid = $input->getOption('useable-in-grid');
        $handler = $this->attributeHandler();
        $batchInfo = 100;
        for ($index = 0; $index < $number; $index++) {
            $command = new GenerateAttribute($inGrid);
            try {
                $handler->handle($command);
            } catch (HttpException $e) {
                echo $e->getMessage();
            }

            if ($index !== 0 && $index % $batchInfo === 0) {
                $output->writeln(sprintf('<info>%s attributes have been generated and imported</info>', $index));
            }
        }
        $output->writeln(sprintf('<info>%s attributes have been generated and imported</info>', $number));
    }

    private function attributeHandler(): GenerateAttributeHandler
    {
        $client = $this->getClient();
        $readRepositories = new ReadRepositories($client);
        $generator = new AttributeGenerator($readRepositories->attributeGroupRepository());

        $writeRepositories = new WriteRepositories($client);
        $attributeRepository = $writeRepositories->attributeRepository();

        return new GenerateAttributeHandler($generator, $attributeRepository);
    }

    private function getClient(): AkeneoPimClientInterface
    {
        $factory = new ApiClientFactory();
        return $factory->create();
    }
}