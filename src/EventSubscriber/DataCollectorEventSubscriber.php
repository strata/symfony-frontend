<?php

declare(strict_types=1);

namespace Strata\SymfonyBundle\EventSubscriber;

use Strata\Data\Event\FailureEvent;
use Strata\Data\Event\StartEvent;
use Strata\Data\Event\SuccessEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DataCollectorEventSubscriber  implements EventSubscriberInterface
{
    private array $data = [];

    public static function getSubscribedEvents(): array
    {
        return [
            StartEvent::NAME    => 'start',
            //    SuccessEvent::NAME  => 'stop',
            //    FailureEvent::NAME  => 'failure',
        ];
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function start(StartEvent $event)
    {
        dump($event);
        $this->data[] = $event->getUri();
    }

    public function success(SuccessEvent $event)
    {
        dump($event);
    }

    public function failure(FailureEvent $event)
    {
        dump($event);
    }
}
