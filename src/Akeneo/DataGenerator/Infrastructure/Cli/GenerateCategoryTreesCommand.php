<?php

namespace Akeneo\DataGenerator\Infrastructure\Cli;

use Akeneo\DataGenerator\Application\GenerateCategoryTree;
use Akeneo\DataGenerator\Application\GenerateCategoryTreeHandler;
use Akeneo\DataGenerator\Domain\CategoryTreeGenerator;
use Akeneo\DataGenerator\Infrastructure\Cli\ApiClient\ApiClientFactory;
use Akeneo\DataGenerator\Infrastructure\WebApi\WriteRepositories;
use Akeneo\Pim\ApiClient\AkeneoPimClientInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCategoryTreesCommand extends Command
{
    protected function configure()
    {
        $this->setName('akeneo:api:generate-category-trees')
            ->setDescription('Import generated category tree through the Akeneo PIM Web API')
            ->addArgument('number', InputArgument::REQUIRED, 'Number of trees to generate')
            ->addArgument('children', InputArgument::REQUIRED, 'Number of categories to generate per tree')
            ->addArgument('levels', InputArgument::REQUIRED, 'Number of levels per tree');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $number = $input->getArgument('number');
        $children = $input->getArgument('children');
        $levels = $input->getArgument('levels');
        $handler = $this->categoryTreeHandler();
        for ($index = 0; $index < $number; $index++) {
            $command = new GenerateCategoryTree($children, $levels);
            $handler->handle($command);

            $output->writeln(sprintf('<info>%s trees have been generated and imported</info>', $index+1));
        }
    }

    private function categoryTreeHandler(): GenerateCategoryTreeHandler
    {
        $generator = new CategoryTreeGenerator();
        $writeRepositories = new WriteRepositories($this->getClient());
        $categoryRepository = $writeRepositories->categoryRepository();

        return new GenerateCategoryTreeHandler($generator, $categoryRepository);
    }

    private function getClient(): AkeneoPimClientInterface
    {
        $factory = new ApiClientFactory();
        return $factory->create();
    }
}
