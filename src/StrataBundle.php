<?php

declare(strict_types=1);

namespace Strata\SymfonyBundle;

use Strata\SymfonyBundle\EventSubscriber\ResponseTagsEventSubscriber;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

/**
 * Enable in config/bundles.php via:
 *     Strata\SymfonyBundle\StrataBundle::class => ['all' => true],
 */
class StrataBundle extends AbstractBundle
{
    /**
     * Setup config available for this service
     */
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->arrayNode('preview_mode')
                    ->children()
                        ->scalarNode('data_provider')->defaultNull()->info('Data provider name to set preview mode on')->end()
                    ->end()
                ->end()
                ->arrayNode('tags')
                    ->children()
                        ->booleanNode('enabled')->defaultFalse()->info('Whether cache tags are enabled')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.yaml');
        $container->import('../config/data_collector.yaml');

        if ($config['tags']['enabled'] ?? false) {
            $container->import('../config/response_tagger.yaml');
        }

        // Pass params to service classes
        $builder->getDefinition('strata.event_subscriber.preview_mode')
            ->replaceArgument(0, $config['preview_mode']['data_provider']);
    }
}
