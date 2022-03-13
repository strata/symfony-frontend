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
class ResponseTagsEventSubscriber implements EventSubscriberInterface
{
    private SymfonyResponseHelper $responseHelper;
    private QueryManager $manager;

    public function __construct(SymfonyResponseHelper $responseHelper, QueryManager $manager)
    {
        $this->responseHelper = $responseHelper;
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

        $this->responseHelper->setResponse($event->getResponse());
        $this->responseHelper->addTagsFromQueryManager($this->manager);
        $this->responseHelper->setHeadersFromResponseTagger();
    }
}
