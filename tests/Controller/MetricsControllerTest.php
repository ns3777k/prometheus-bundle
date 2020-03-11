<?php

declare(strict_types=1);

namespace Ns3777k\PrometheusBundle\Tests\Controller;

use Ns3777k\PrometheusBundle\Controller\MetricsController;
use Ns3777k\PrometheusBundle\Metrics\CollectorRegistryInterface;
use Ns3777k\PrometheusBundle\Metrics\RendererInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class MetricsControllerTest extends TestCase
{
    public function testPrometheus()
    {
        $registry = $this->createMock(CollectorRegistryInterface::class);
        $registry
            ->expects($this->once())
            ->method('getMetricFamilySamples')
            ->willReturn(['some_metric' => 0.11]);

        $renderer = $this->createMock(RendererInterface::class);
        $renderer
            ->expects($this->once())
            ->method('render')
            ->willReturnCallback(function ($arg) {
                return "some_metric: {$arg['some_metric']}";
            });

        $controller = new MetricsController($registry, $renderer);
        $response = $controller->prometheus();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('some_metric: 0.11', $response->getContent());
        $this->assertEquals(RendererInterface::MIME_TYPE_PLAIN, $response->headers->get('Content-Type'));
    }
}
