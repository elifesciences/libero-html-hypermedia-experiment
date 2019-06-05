<?php

namespace App\EventListener;

use GuzzleHttp\Promise\PromiseInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class PromiseViewSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents() : array
    {
        return [KernelEvents::VIEW => 'onView'];
    }

    public function onView(ViewEvent $event) : void
    {
        $result = $event->getControllerResult();

        if ($result instanceof PromiseInterface) {
            $event->setResponse($result->wait());
        }
    }
}
