<?php

declare(strict_types=1);

namespace Strata\Symfony\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('strata');

        //strata.preview_mode.data_provider
        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('preview_mode')
                    ->children()
                        ->scalCarNode('data_provider')->end()
                    ->end()
                ->end() // preview_mode
            ->end()
        ;

        return $treeBuilder;
    }

}
