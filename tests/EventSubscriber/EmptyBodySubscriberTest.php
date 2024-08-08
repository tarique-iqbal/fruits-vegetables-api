<?php

declare(strict_types=1);

namespace App\Tests\EventSubscriber;

use App\EventSubscriber\EmptyBodySubscriber;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class EmptyBodySubscriberTest extends KernelTestCase
{
    public function testHandleEmptyBody()
    {
        $this->expectException(BadRequestHttpException::class);

        $request = Request::create('/api/fruits', 'POST');
        $request->headers->set('Content-Type', 'application/json');

        $event = new RequestEvent(static::bootKernel(), $request, null);
        (new EmptyBodySubscriber())->handleEmptyBody($event);
    }
}
