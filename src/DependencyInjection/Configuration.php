<?php

declare(strict_types=1);

namespace Strata\SymfonyBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('strata');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('preview_mode')
                    ->children()
                        ->scalarNode('data_provider')->defaultNull()->info('Data provider name to set preview mode on')->end()
                    ->end()
                ->end() // preview_mode
                ->arrayNode('tags')
                    ->children()
                        ->booleanNode('enabled')->defaultFalse()->info('Whether cache tags are enabled')->end()
                    ->end()
                ->end() // tags

            ->end()
        ;

        return $treeBuilder;
    }
}
