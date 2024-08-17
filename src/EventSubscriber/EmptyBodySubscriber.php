<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class EmptyBodySubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => 'handleEmptyBody'];
    }

    public function handleEmptyBody(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $method = $request->getMethod();

        if (!in_array($method, [Request::METHOD_POST, Request::METHOD_PUT])) {
            return;
        }

        if (strlen($request->getContent()) === 0) {
            throw new BadRequestHttpException(
                'The body of the POST/PUT method cannot be empty.'
            );
        }
    }
}
