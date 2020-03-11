<?php

declare(strict_types=1);

namespace Ns3777k\PrometheusBundle\Metrics;

use Prometheus\Counter;
use Prometheus\Gauge;
use Prometheus\Histogram;

interface CollectorRegistryInterface
{
    public function registerGauge(string $name, string $help, array $labels = []): Gauge;

    public function getGauge(string $name): Gauge;

    public function getOrRegisterGauge(string $name, string $help, array $labels = []): Gauge;

    public function registerCounter(string $name, string $help, array $labels = []): Counter;

    public function getCounter(string $name): Counter;

    public function getOrRegisterCounter(string $name, string $help, array $labels = []): Counter;

    public function registerHistogram(string $name, string $help, array $labels = [], array $buckets = null): Histogram;

    public function getHistogram(string $name): Histogram;

    public function getOrRegisterHistogram(string $name, string $help, array $labels = [], array $buckets = null): Histogram;

    public function getMetricFamilySamples(): array;
}
