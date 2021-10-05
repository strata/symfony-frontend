<?php

declare(strict_types=1);

namespace Strata\Symfony;

use Strata\Symfony\DependencyInjection\StrataExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Enable in config/bundles.php via:
 *     Strata\Symfony\Bundle\StrataBundle::class => ['all' => true],
 */
class StrataBundle extends Bundle
{
    public function getPath(): string
    {
        return dirname(__DIR__);
    }

}
