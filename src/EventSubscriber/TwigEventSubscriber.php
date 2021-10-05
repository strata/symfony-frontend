<?php

namespace Strata\SymfonyBundle\EventSubscriber;

use Strata\Frontend\Site;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;

class TwigEventSubscriber implements EventSubscriberInterface
{
    private Environment $twig;
    private Site $site;

    public function __construct(Environment $twig, Site $site)
    {
        $this->twig = $twig;
        $this->site = $site;
    }

    public function onKernelController(ControllerEvent $event)
    {
        $this->twig->addGlobal('site', $this->site);
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.controller' => 'onKernelController',
        ];
    }

}
