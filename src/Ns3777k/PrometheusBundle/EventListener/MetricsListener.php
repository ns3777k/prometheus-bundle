<?php

declare(strict_types=1);

namespace Ns3777k\PrometheusBundle\EventListener;

use Ns3777k\PrometheusBundle\Metrics\CollectorRegistryInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;

/**
 * Class MetricsListener.
 */
class MetricsListener
{
    /**
     * @var array
     */
    private $ignoredRoutes = [];

    /**
     * @var CollectorRegistryInterface
     */
    private $registry;

    /**
     * @var float
     */
    private $startTime;

    /**
     * MetricsListener constructor.
     */
    public function __construct(CollectorRegistryInterface $registry, array $ignoredRoutes = [])
    {
        $this->registry = $registry;
        $this->ignoredRoutes = $ignoredRoutes;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $route = $request->attributes->get('_route');
        if ($this->ignore($route)) {
            return;
        }

        $this->startTime = microtime(true);
    }

    public function onKernelTerminate(TerminateEvent $event): void
    {
        $request = $event->getRequest();
        $route = $request->attributes->get('_route');
        if ($this->ignore($route)) {
            return;
        }

        $method = $request->getMethod();
        $responseCode = $event->getResponse()->getStatusCode();
        $duration = microtime(true) - $this->startTime;

        $histogram = $this->registry->getOrRegisterHistogram(
            'request_duration_seconds',
            'Request duration with response information',
            ['route', 'code', 'method']
        );

        $histogram->observe($duration, [$route, $responseCode, $method]);
    }

    private function ignore(?string $route): bool
    {
        return in_array($route, $this->ignoredRoutes, true);
    }
}
