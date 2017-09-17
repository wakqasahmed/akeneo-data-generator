<?php

namespace Nidup\Sandbox\Infrastructure\Cli;

use Akeneo\Pim\AkeneoPimClientBuilder;
use Akeneo\Pim\AkeneoPimClientInterface;
use Nidup\Sandbox\Application\ConfigProvider;
use Nidup\Sandbox\Application\ProductGenerator;
use Nidup\Sandbox\Domain\AttributeRepository;
use Nidup\Sandbox\Domain\ChannelRepository;
use Nidup\Sandbox\Domain\FamilyRepository;
use Nidup\Sandbox\Domain\LocaleRepository;
use Nidup\Sandbox\Domain\Product;
use Nidup\Sandbox\Infrastructure\Database\InMemoryAttributeRepository;
use Nidup\Sandbox\Infrastructure\Database\InMemoryChannelRepository;
use Nidup\Sandbox\Infrastructure\Database\InMemoryFamilyRepository;
use Nidup\Sandbox\Infrastructure\Database\InMemoryLocaleRepository;
use Nidup\Sandbox\Infrastructure\Pim\AttributeRepositoryInitializer;
use Nidup\Sandbox\Infrastructure\Pim\ChannelRepositoryInitializer;
use Nidup\Sandbox\Infrastructure\Pim\FamilyRepositoryInitializer;
use Nidup\Sandbox\Infrastructure\Pim\LocaleRepositoryInitializer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateProductsCommand extends Command
{
    protected function configure()
    {
        $this->setName('nidup:sandbox:generate-products')
            ->setDescription('Import generated products through the Akeneo PIM Web API')
            ->addArgument('number', InputArgument::REQUIRED, 'Number of products to generate')
            ->addOption('debug', null, InputOption::VALUE_NONE, 'Enable debug mode');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $generator = $this->getGenerator();
        $number = $input->getArgument('number');
        $debug = $input->getOption('debug');

        $batchInfo = 100;
        for ($index = 0; $index < $number; $index++) {
            $product = $generator->generate();
            $this->importProduct($product, $debug);
            if ($index !== 0 && $index % $batchInfo === 0) {
                $output->writeln(sprintf('<info>%s products have been generated and imported</info>', $index));
            }
        }
        $output->writeln(sprintf('<info>%s products have been generated and imported</info>', $number));
    }

    private function importProduct(Product $product, bool $debug)
    {
        $client = $this->getClient();
        $productData = $product->toArray();
        try {
            if ($debug) {
                var_dump($productData);
                var_dump($productData['values']);
            }
            $client->getProductApi()->upsert($product->getIdentifier(), $productData);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    private function getGenerator(): ProductGenerator
    {
        $localeRepository = $this->buildLocaleRepository();
        $channelRepository = $this->buildChannelRepository($localeRepository);
        $attributeRepository = $this->buildAttributeRepository();
        $familyRepository = $this->buildFamilyRepository($attributeRepository);

        return new ProductGenerator($channelRepository, $localeRepository, $familyRepository);
    }

    private function buildFamilyRepository(AttributeRepository $attributeRepository): FamilyRepository
    {
        $client = $this->getClient();
        $repository = new InMemoryFamilyRepository();
        $initializer = new FamilyRepositoryInitializer($client, $attributeRepository);
        $initializer->initialize($repository);

        return $repository;
    }

    private function buildAttributeRepository(): AttributeRepository
    {
        $client = $this->getClient();
        $initializer = new AttributeRepositoryInitializer($client);
        $repository = new InMemoryAttributeRepository();
        $initializer->initialize($repository);

        return $repository;
    }

    private function buildLocaleRepository(): LocaleRepository
    {
        $client = $this->getClient();
        $initializer = new LocaleRepositoryInitializer($client);
        $repository = new InMemoryLocaleRepository();
        $initializer->initialize($repository);

        return $repository;
    }

    private function buildChannelRepository(LocaleRepository $localeRepository): ChannelRepository
    {
        $client = $this->getClient();
        $initializer = new ChannelRepositoryInitializer($client, $localeRepository);
        $repository = new InMemoryChannelRepository();
        $initializer->initialize($repository);

        return $repository;
    }

    private function getClient(): AkeneoPimClientInterface
    {
        $config = new ConfigProvider( __DIR__.'/../../../../../app/parameters.yml');
        $baseUri = sprintf('%s:%s', $config->getParameter('host'), $config->getParameter('port'));
        $clientId = $config->getParameter('client_id');
        $secret = $config->getParameter('secret');
        $username = $config->getParameter('username');
        $password = $config->getParameter('password');

        $clientBuilder = new AkeneoPimClientBuilder($baseUri);
        return $clientBuilder->buildAuthenticatedByPassword(
            $clientId,
            $secret,
            $username,
            $password
        );
    }
}