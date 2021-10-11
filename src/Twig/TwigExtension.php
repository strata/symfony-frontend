<?php

declare(strict_types=1);

namespace Strata\SymfonyBundle\Twig;

use Strata\Frontend\View\TableOfContents;
use Strata\Frontend\View\ViewFilters;
use Strata\Frontend\View\ViewFunctions;
use Strata\Frontend\View\ViewTests;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

/**
 * Twig custom functions and filters
 *
 * To use add this to your services.yaml:

# Register Frontend Twig helpers
Strata\Symfony\TwigExtension:
  tags: ['twig.extension']

 * @package Strata\Frontend\Twig
 */
class TwigExtension extends AbstractExtension
{
    public function getFunctions()
    {
        $helpers = new ViewFunctions();
        return [
            new TwigFunction('not_empty', [$helpers, 'notEmpty'], ['is_variadic' => true]),
            new TwigFunction('all_not_empty', [$helpers, 'allNotEmpty'], ['is_variadic' => true]),
            new TwigFunction('staging_banner', [$helpers, 'stagingBanner'], ['is_safe' => ['html']]),
            new TwigFunction('table_of_contents', [$this, 'tableOfContents'], ['is_safe' => ['html'], 'needs_environment' => true]),
        ];
    }

    public function getFilters()
    {
        $helpers = new ViewFilters();
        return [
            new TwigFilter('excerpt', [$helpers, 'excerpt']),
            new TwigFilter('build_version', [$helpers, 'buildVersion']),
            new TwigFilter('slugify', [$helpers, 'slugify']),
            new TwigFilter('fix_url', [$helpers, 'fixUrl']),

        ];
    }

    public function getTests()
    {
        $helpers = new ViewTests();
        return [
            new TwigTest('is_prod', [$helpers, 'isProd']),
        ];
    }

    /**
     * Entry point for table_of_contents Twig function
     * @param Environment $env
     * @param $content
     * @param array|null $levels
     * @return TableOfContents
     * @throws \Strata\Frontend\Exception\ViewHelperException
     */
    public function tableOfContents(Environment $env, $content, ?array $levels = null)
    {
        $content = (string) $content;
        if (is_array($levels)) {
            $toc = new TableOfContents($content, $levels);
        } else {
            $toc = new TableOfContents($content);
        }
        if ($env->isDebug()) {
            $toc->enableDebug();
        }
        return $toc;
    }

}
