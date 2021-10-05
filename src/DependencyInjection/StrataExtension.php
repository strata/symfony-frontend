<?php

declare(strict_types=1);

namespace Strata\SymfonyBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class StrataExtension extends ConfigurableExtension
{

    public function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.yaml');

        // Pass params to service classes
        $definition = $container->getDefinition('strata.event_subscriber.preview_mode');
        $definition->replaceArgument(0, $mergedConfig['preview_mode']['data_provider']);
    }

}
