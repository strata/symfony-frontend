<?php

declare(strict_types=1);

namespace Strata\SymfonyBundle;

use Strata\Symfony\DependencyInjection\StrataExtension;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

/**
 * Enable in config/bundles.php via:
 *     Strata\Symfony\StrataBundle::class => ['all' => true],
 */
class StrataBundle extends AbstractBundle
{
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        // load a PHP or YAML file
        $container->import('../config/services.php');
    }
}
