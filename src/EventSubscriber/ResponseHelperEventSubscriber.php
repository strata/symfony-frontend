<?php

declare(strict_types=1);

namespace Strata\SymfonyBundle\EventSubscriber;

use Strata\Data\Query\QueryManager;
use Strata\SymfonyBundle\ResponseHelper\SymfonyResponseHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

/**
 * Add response headers
 */
class ResponseHelperEventSubscriber implements EventSubscriberInterface
{
    private SymfonyResponseHelper $helper;
    private ResponseTagger $responseTagger;
    private QueryManager $manager;

    public function __construct(SymfonyResponseHelper $helper, ResponseTagger $responseTagger, QueryManager $manager)
    {
        $this->helper = $helper;
        $this->responseTagger = $responseTagger;
        $this->manager = $manager;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ResponseEvent::class => 'onKernelResponse',
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

        // Apply response tags from query manager
        $this->helper->applyResponseTagsFromQuery($this->responseTagger, $this->manager);

        // Apply any headers set in response helper
        $this->helper->apply($event->getResponse());
    }
}
