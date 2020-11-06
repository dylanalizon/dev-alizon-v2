<?php

namespace App\Tests\EventSubscriber;

use App\EventSubscriber\ApiExceptionSubscriber;
use App\Exception\ValidationFailedApiException;
use App\Response\ValidationFailedJsonResponse;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiExceptionSubscriberTest extends TestCase
{
    public function testOnKernelException(): void
    {
        $errors = ['test' => ['an error message']];
        $exception = new ValidationFailedApiException($errors);
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $requestMock = $this->createMock(Request::class);
        $event = new ExceptionEvent($kernelMock, $requestMock, HttpKernelInterface::MASTER_REQUEST, $exception);
        $subscriber = new ApiExceptionSubscriber();
        $subscriber->onKernelException($event);
        $this->assertInstanceOf(ValidationFailedJsonResponse::class, $event->getResponse());
    }

    public function testOnKernelExceptionWithoutValidationFailedApiException(): void
    {
        $exception = new Exception();
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $requestMock = $this->createMock(Request::class);
        $event = new ExceptionEvent($kernelMock, $requestMock, HttpKernelInterface::MASTER_REQUEST, $exception);
        $subscriber = new ApiExceptionSubscriber();
        $subscriber->onKernelException($event);
        $this->assertNull($event->getResponse());
    }

    public function testGetSubscribedEvents(): void
    {
        $events = ApiExceptionSubscriber::getSubscribedEvents();
        $this->assertSame([
            KernelEvents::EXCEPTION => 'onKernelException',
        ], $events);
    }
}
