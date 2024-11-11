<?php

declare(strict_types=1);

namespace App\Tests\EventSubscriber;

use App\Component\Validator\Exception\AcceptanceFailedException;
use App\EventSubscriber\ExceptionSubscriber;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ExceptionSubscriberTest extends KernelTestCase
{
    private EventDispatcher $dispatcher;

    protected function setUp(): void
    {
        $subscriber = new ExceptionSubscriber();
        $this->dispatcher = new EventDispatcher();
        $this->dispatcher->addSubscriber($subscriber);
    }

    public function testHandleValidationFailedException()
    {
        $request = Request::create('/api/fruits', 'POST', ['name' => '']);
        $httpKernel = $this->createMock(HttpKernelInterface::class);
        $violation = new ConstraintViolation(
            'Name cannot be empty.',
            null,
            [],
            null,
            'name',
            null
        );
        $violations = new ConstraintViolationList([$violation]);
        $exception = new ValidationFailedException([], $violations);

        $event = new ExceptionEvent($httpKernel, $request, 0, $exception);

        $this->dispatcher->dispatch($event, KernelEvents::EXCEPTION);

        $response = $event->getResponse();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $this->assertSame('[{"property":"name","message":"Name cannot be empty."}]', $response->getContent());
    }

    public function testHandleAcceptanceFailedException()
    {
        $request = Request::create('/api/vegetables', 'GET', ['page' => 'str2']);
        $httpKernel = $this->createMock(HttpKernelInterface::class);
        $violation = new ConstraintViolation(
            'Page must be numeric value.',
            null,
            [],
            null,
            'page',
            null
        );
        $violations = new ConstraintViolationList([$violation]);
        $exception = new AcceptanceFailedException([], $violations);

        $event = new ExceptionEvent($httpKernel, $request, 0, $exception);

        $this->dispatcher->dispatch($event, KernelEvents::EXCEPTION);

        $response = $event->getResponse();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertSame('[{"property":"page","message":"Page must be numeric value."}]', $response->getContent());
    }

    public function testHandleBadRequestHttpException()
    {
        $request = Request::create('/api/fruits', 'POST');
        $httpKernel = $this->createMock(HttpKernelInterface::class);
        $exception = new BadRequestHttpException('POST method body cannot be empty.');

        $event = new ExceptionEvent($httpKernel, $request, 0, $exception);

        $this->dispatcher->dispatch($event, KernelEvents::EXCEPTION);

        $response = $event->getResponse();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertSame('{"error":"POST method body cannot be empty."}', $response->getContent());
    }

    public function testUnhandledException()
    {
        $request = Request::create('/api/fake', 'POST');
        $httpKernel = $this->createMock(HttpKernelInterface::class);
        $exception = new \Exception('Internal Server Error!');

        $event = new ExceptionEvent($httpKernel, $request, 0, $exception);

        $this->dispatcher->dispatch($event, KernelEvents::EXCEPTION);

        $response = $event->getResponse();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertSame('{"error":"Internal Server Error!"}', $response->getContent());
    }
}
