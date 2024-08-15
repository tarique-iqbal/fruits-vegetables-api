<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onException',
        ];
    }

    public function onException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof HttpExceptionInterface) {
            $message = $exception->getMessage();
            $statusCode = $exception->getStatusCode();
        } else {
            $message = $exception->getMessage();
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        $data = [
            ['message' => $message],
            $statusCode
        ];
        $response = new JsonResponse($data, $statusCode);

        $event->setResponse($response);
    }
}
