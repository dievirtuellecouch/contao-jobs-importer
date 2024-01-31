<?php

namespace DVC\JobsImporter\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('jobs_importer');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('sources')
                    ->arrayPrototype()
                        ->children()
                            ->enumNode('type')
                                ->values(['talentstorm'])
                            ->end()
                            ->scalarNode('api_key')
                                ->defaultNull()
                            ->end()
                            ->integerNode('timeout')
                                ->defaultValue(3)
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
