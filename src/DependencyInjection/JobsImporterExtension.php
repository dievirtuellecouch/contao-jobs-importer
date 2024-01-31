<?php

namespace DVC\JobsImporter\DependencyInjection;

use DVC\JobsImporter\DependencyInjection\Configuration;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Filesystem\Path;

class JobsImporterExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(Path::canonicalize(__DIR__ . '../../../config/')));
        $loader->load('services.yaml');

        $configuration = new Configuration();

        $processedConfiguration = $this->processConfiguration($configuration, $configs);

        if (!\array_key_exists('sources', $processedConfiguration) || !is_array($processedConfiguration['sources'])) {
            $sources = [];
        }
        else {
            $sources = $this->initExternalSources($container, $processedConfiguration['sources']);
        }

        $sourceRegistryDefinition = $container->getDefinition(\DVC\JobsImporter\ExternalSource\ExternalSourceRegistry::class);
        $sourceRegistryDefinition->setArgument('$configuredSources', $sources);
    }

    private function initExternalSources(ContainerBuilder $container, array $sources): array
    {
        if (empty($sources)) {
            return [];
        }

        $result = [];

        foreach ($sources as $sourceConfig) {
            $sourceName = 'jobs_importer.external_source.' . $sourceConfig['type'];

            if (!$container->has($sourceName)) {
                continue;
            }

            $clientDefinition = $container->getDefinition($sourceName . '.client');
            $clientDefinition->setArgument('$apiSecret', $sourceConfig['api_key']);
            $clientDefinition->setArgument('$timeout', $sourceConfig['timeout']);

            $result[] = new Reference($sourceName);
        }

        return $result;
    }
}
