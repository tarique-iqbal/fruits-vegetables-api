<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof ValidationFailedException) {
            $violations = $exception->getViolations();
            $message = $this->getErrorMessage($violations);
            $statusCode = Response::HTTP_BAD_REQUEST;
        } elseif ($exception instanceof HttpExceptionInterface) {
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

    private function getErrorMessage(ConstraintViolationList $violations): array
    {
        $messages = [];
        foreach ($violations as $violation) {
            $messages[] = [
                'property' => $violation->getPropertyPath(),
                'message' => $violation->getMessage(),
            ];
        }

        return $messages;
    }
}
