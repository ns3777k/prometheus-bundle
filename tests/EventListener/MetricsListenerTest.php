<?php

declare(strict_types=1);

namespace Ns3777k\PrometheusBundle\Tests\EventListener;

use Ns3777k\PrometheusBundle\EventListener\MetricsListener;
use Ns3777k\PrometheusBundle\Metrics\CollectorRegistryInterface;
use PHPUnit\Framework\TestCase;
use Prometheus\Histogram;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;

class MetricsListenerTest extends TestCase
{
    public function testIgnoreRoutesOnKernelTerminate(): void
    {
        $registry = $this->createMock(CollectorRegistryInterface::class);
        $registry->expects($this->never())->method('getOrRegisterHistogram');
        $listener = new MetricsListener($registry, ['some_route']);

        $request = new Request([], [], ['_route' => 'some_route']);
        $event = $this->createMock(TerminateEvent::class);
        $event->method('getRequest')->willReturn($request);

        $listener->onKernelTerminate($event);
    }

    /**
     * @group time-sensitive
     */
    public function testObserveRequestDuration(): void
    {
        $histogram = $this->createMock(Histogram::class);
        $histogram
            ->method('observe')
            ->with(
                $this->anything(),
                $this->equalTo(['some_route', Response::HTTP_OK, 'GET'])
            );

        $registry = $this->createMock(CollectorRegistryInterface::class);
        $registry
            ->expects($this->once())
            ->method('getOrRegisterHistogram')
            ->with(
                $this->equalTo('request_duration_seconds'),
                $this->anything(),
                $this->equalTo(['route', 'code', 'method'])
            )
            ->willReturn($histogram);

        $listener = new MetricsListener($registry);

        $request = new Request([], [], ['_route' => 'some_route']);
        $response = (new Response())->setStatusCode(Response::HTTP_OK);

        $requestEvent = $this->createMock(RequestEvent::class);
        $requestEvent->method('getRequest')->willReturn($request);

        $terminateEvent = $this->createMock(TerminateEvent::class);
        $terminateEvent->method('getRequest')->willReturn($request);
        $terminateEvent->method('getResponse')->willReturn($response);

        $listener->onKernelRequest($requestEvent);
        $listener->onKernelTerminate($terminateEvent);
    }
}
