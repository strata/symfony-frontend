<?php

declare(strict_types=1);

namespace Strata\SymfonyBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class StrataExtension extends ConfigurableExtension
{
    public function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.xml');
        $loader->load('data_collector.xml');

        // Pass params to service classes
        $definition = $container->getDefinition('strata.event_subscriber.preview_mode');
        $definition->replaceArgument(0, $mergedConfig['preview_mode']['data_provider']);

        // Load response tagger is loaded via FOSHttpCacheBundle
        if ($container->has('fos_http_cache.response_tagger')) {
            $loader->load('response_tagger.xml');
        }
    }
}
