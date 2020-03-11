<?php

declare(strict_types=1);

namespace Ns3777k\PrometheusBundle\Controller;

use Ns3777k\PrometheusBundle\Metrics\CollectorRegistryInterface;
use Ns3777k\PrometheusBundle\Metrics\RendererInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MetricsController.
 */
class MetricsController
{
    /**
     * @var CollectorRegistryInterface
     */
    private $registry;

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * MetricsController constructor.
     */
    public function __construct(CollectorRegistryInterface $registry, RendererInterface $renderer)
    {
        $this->registry = $registry;
        $this->renderer = $renderer;
    }

    public function prometheus(): Response
    {
        $metrics = $this->renderer->render($this->registry->getMetricFamilySamples());

        return new Response($metrics, Response::HTTP_OK, ['Content-Type' => RendererInterface::MIME_TYPE_PLAIN]);
    }
}
