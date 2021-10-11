<?php

namespace Strata\SymfonyBundle\DataCollector;

use Strata\Data\Query\QueryManager;
use Strata\Frontend\Content\Field\DateTime;
use Strata\Frontend\Site;
use Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StrataDataCollector extends AbstractDataCollector
{
    private ?Site $site = null;
    private ?QueryManager $manager = null;

    /**
     * Populate other services used to collect data
     *
     * @param QueryManager $manager
     * @param Site $site
     */
    public function __construct(QueryManager $manager, Site $site)
    {
        $this->manager = $manager;
        $this->site = $site;
    }

    public function getName(): string
    {
        return 'strata';
    }

    public function reset(): void
    {
        $this->site = null;
        $this->manager = null;
        parent::reset();
    }

    public static function getTemplate(): ?string
    {
        return '@Strata/data-collector/data-collector.html.twig';
    }

    /**
     * Collect data
     *
     * Any data we want to access in data collector templates must be populated to the $this->data property, since
     * everything is serialized by the time it is passed to the profiler
     *
     * @param Request $request
     * @param Response $response
     * @param \Throwable|null $exception
     * @throws \Strata\Frontend\Exception\InvalidLocaleException
     */
    public function collect(Request $request, Response $response, \Throwable $exception = null)
    {
        $this->data = [
            'cacheEnabled' => $this->manager->isCacheEnabled(),
            'locale' => $this->site->getLocale(),
            'textDirection' => $this->site->getTextDirection(),
            'localeData' => $this->site->getLocaleData(),
            'previewMode' => ($response->headers->get('X-Frontend-Content') === 'PREVIEW') ? true : false,
            'queryManager' => $this->manager->getDataCollector(),
        ];
    }

    public function getCacheEnabled()
    {
        return $this->data['cacheEnabled'];
    }

    public function getLocale(): string
    {
        return $this->data['locale'];
    }

    public function getTextDirection(): string
    {
        return $this->data['textDirection'];
    }

    public function getLocaleData(): array
    {
        return $this->data['localeData'];
    }

    public function getPreviewMode(): bool
    {
        return $this->data['previewMode'];
    }

    public function getDataProviders(): array
    {
        return $this->data['queryManager']['dataProviders'];
    }

    public function getQueries(): array
    {
        return $this->data['queryManager']['queries'];
    }

    public function getTotalQueries(): int
    {
        return $this->data['queryManager']['total'];
    }

    public function getTotalQueriesCached(): int
    {
        return $this->data['queryManager']['cached'];
    }

}
