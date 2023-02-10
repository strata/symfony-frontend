<?php

declare(strict_types=1);

namespace Strata\SymfonyBundle\EventSubscriber;

use FOS\HttpCache\ResponseTagger;
use Strata\Data\Query\QueryManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

/**
 * Add response headers
 */
class ResponseTagsEventSubscriber implements EventSubscriberInterface
{
    const TAGS_HEADER_NAME = 'X-Strata-Cache-Tags';

    private ResponseTagger $responseTagger;
    private QueryManager $manager;

    public function __construct(ResponseTagger $responseTagger, QueryManager $manager)
    {
        $this->responseTagger = $responseTagger;
        $this->manager = $manager;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        /**
         * onKernelResponse event needs to be higher than
         * FOS\HttpCacheBundle\EventListener\TagListener::onKernelResponse()
         */
        return [
            ResponseEvent::class => ['onKernelResponse', 10],
        ];
    }

    /**
     * Add cache tags from query manager to response headers
     * @param ResponseEvent $event
     */
    public function onKernelResponse(ResponseEvent $event)
    {
        if (!$event->isMainRequest()) {
            // don't do anything if it's not the main request
            return;
        }

        // Set query cache tags to FOSHttpCache response tagger
        foreach ($this->manager->getQueries() as $query) {
            if ($query->hasResponseRun() && $query->hasCacheTags()) {
                $this->responseTagger->addTags($query->getCacheTags());
            }
        }

        // Add custom response header (since some cache proxies strip cache tag header)
        $event->getResponse()->headers->set(self::TAGS_HEADER_NAME, $this->responseTagger->getTagsHeaderValue());
    }

}
