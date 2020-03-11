<?php

declare(strict_types=1);

namespace Ns3777k\PrometheusBundle\Metrics;

use Prometheus\CollectorRegistry;
use Prometheus\Counter;
use Prometheus\Gauge;
use Prometheus\Histogram;
use Prometheus\MetricFamilySamples;

/**
 * Class NamespacedCollectorRegistry.
 */
class NamespacedCollectorRegistry implements CollectorRegistryInterface
{
    /**
     * @var CollectorRegistry
     */
    private $collectorRegistry;

    /**
     * @var string
     */
    private $namespace;

    /**
     * NamespacedCollectorRegistry constructor.
     */
    public function __construct(CollectorRegistry $collectorRegistry, string $namespace = '')
    {
        $this->collectorRegistry = $collectorRegistry;
        $this->namespace = $namespace;
    }

    /**
     * @throws \Prometheus\Exception\MetricsRegistrationException
     */
    public function registerGauge(string $name, string $help, array $labels = []): Gauge
    {
        return $this->collectorRegistry->registerGauge($this->namespace, $name, $help, $labels);
    }

    /**
     * @throws \Prometheus\Exception\MetricNotFoundException
     */
    public function getGauge(string $name): Gauge
    {
        return $this->collectorRegistry->getGauge($this->namespace, $name);
    }

    /**
     * @throws \Prometheus\Exception\MetricsRegistrationException
     */
    public function getOrRegisterGauge(string $name, string $help, array $labels = []): Gauge
    {
        return $this->collectorRegistry->getOrRegisterGauge($this->namespace, $name, $help, $labels);
    }

    /**
     * @throws \Prometheus\Exception\MetricsRegistrationException
     */
    public function registerCounter(string $name, string $help, array $labels = []): Counter
    {
        return $this->collectorRegistry->registerCounter($this->namespace, $name, $help, $labels);
    }

    /**
     * @throws \Prometheus\Exception\MetricNotFoundException
     */
    public function getCounter(string $name): Counter
    {
        return $this->collectorRegistry->getCounter($this->namespace, $name);
    }

    /**
     * @throws \Prometheus\Exception\MetricsRegistrationException
     */
    public function getOrRegisterCounter(string $name, string $help, array $labels = []): Counter
    {
        return $this->collectorRegistry->getOrRegisterCounter($this->namespace, $name, $help, $labels);
    }

    /**
     * @throws \Prometheus\Exception\MetricsRegistrationException
     */
    public function registerHistogram(string $name, string $help, array $labels = [], array $buckets = null): Histogram
    {
        return $this->collectorRegistry->registerHistogram($this->namespace, $name, $help, $labels, $buckets);
    }

    /**
     * @throws \Prometheus\Exception\MetricNotFoundException
     */
    public function getHistogram(string $name): Histogram
    {
        return $this->collectorRegistry->getHistogram($this->namespace, $name);
    }

    /**
     * @throws \Prometheus\Exception\MetricsRegistrationException
     */
    public function getOrRegisterHistogram(string $name, string $help, array $labels = [], array $buckets = null): Histogram
    {
        return $this->collectorRegistry->getOrRegisterHistogram($this->namespace, $name, $help, $labels, $buckets);
    }

    /**
     * @return MetricFamilySamples[]
     */
    public function getMetricFamilySamples(): array
    {
        return $this->collectorRegistry->getMetricFamilySamples();
    }
}
