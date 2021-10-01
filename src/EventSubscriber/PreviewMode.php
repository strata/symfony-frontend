<?php

namespace Strata\Symfony\EventSubscriber;

use Strata\Data\Exception\MissingDataProviderException;
use Strata\Data\Query\QueryManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

/**
 * CraftCMS preview mode
 */
class PreviewMode implements EventSubscriberInterface
{
    private QueryManager $manager;
    private bool $previewMode = false;
    private string $dataProvider;

    public function __construct(QueryManager $manager, string $dataProvider)
    {
        $this->manager = $manager;
        $this->dataProvider = $dataProvider;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onKernelRequest',
            ResponseEvent::class => 'onKernelResponse',
        ];
    }

    /**
     * Detect Craft preview token and pass back in HTTP requests
     *
     * URL format: x-craft-live-preview=abc123
     *
     * @see https://nystudio107.com/blog/headless-preview-in-craft-cms
     * @param RequestEvent $event
     * @throws MissingDataProviderException
     */
    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMainRequest()) {
            // don't do anything if it's not the main request
            return;
        }

        $request = $event->getRequest();
        $token = $request->get('x-craft-live-preview');
        if (!empty($token)) {
            $this->previewMode = true;

            // Set token in all HTTP requests for CraftCMS to authenticate preview content requests
            $this->manager->getDataProvider($this->dataProvider)->setDefaultOptions(['headers' => [
                'X-Craft-Live-Preview' => $token,
            ]]);
        }
    }

    /**
     * Add response headers to indicate whether in preview mode or not
     * @param ResponseEvent $event
     */
    public function onKernelResponse(ResponseEvent $event)
    {
        if (!$event->isMainRequest()) {
            // don't do anything if it's not the main request
            return;
        }

        $response = $event->getResponse();
        if ($this->previewMode) {
            // Preview mode = true
            $response->headers->set('X-Frontend-Content', 'PREVIEW');
        } else {
            $response->headers->set('X-Frontend-Content', 'PUBLISHED');
        }
    }

}