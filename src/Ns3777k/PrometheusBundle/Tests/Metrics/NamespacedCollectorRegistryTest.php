<?php

declare(strict_types=1);

namespace Ns3777k\PrometheusBundle\Tests\Metrics;

use Ns3777k\PrometheusBundle\Metrics\NamespacedCollectorRegistry;
use PHPUnit\Framework\TestCase;
use Prometheus\CollectorRegistry;

class NamespacedCollectorRegistryTest extends TestCase
{
    private const NAMESPACE = 'app';

    /**
     * @var NamespacedCollectorRegistry
     */
    private $registry;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|CollectorRegistry
     */
    private $nestedRegistryStub;

    protected function setUp(): void
    {
        $this->nestedRegistryStub = $this->createMock(CollectorRegistry::class);
        $this->registry = new NamespacedCollectorRegistry($this->nestedRegistryStub, static::NAMESPACE);
    }

    private function expectRegistryMethodToBeCalledWith(string $method, string $name, array $with = []): void
    {
        $this->nestedRegistryStub
            ->expects($this->once())
            ->method($method)
            ->with(
                $this->equalTo(static::NAMESPACE),
                $this->equalTo($name),
                ...$with
            );
    }

    public function testGetGauge(): void
    {
        $this->expectRegistryMethodToBeCalledWith('getGauge', 'gauge');
        $this->registry->getGauge('gauge');
    }

    public function testGetHistogram(): void
    {
        $this->expectRegistryMethodToBeCalledWith('getCounter', 'counter');
        $this->registry->getCounter('counter');
    }

    public function testGetCounter(): void
    {
        $this->expectRegistryMethodToBeCalledWith('getHistogram', 'histogram');
        $this->registry->getHistogram('histogram');
    }

    public function testRegisterGauge(): void
    {
        $this->expectRegistryMethodToBeCalledWith('registerGauge', 'gauge', ['help', ['test' => 'yes']]);
        $this->registry->registerGauge('gauge', 'help', ['test' => 'yes']);
    }

    public function testRegisterCounter(): void
    {
        $this->expectRegistryMethodToBeCalledWith('registerCounter', 'counter', ['help', ['test' => 'yes']]);
        $this->registry->registerCounter('counter', 'help', ['test' => 'yes']);
    }

    public function testRegisterHistogram(): void
    {
        $this->expectRegistryMethodToBeCalledWith('registerHistogram', 'histogram', ['help', ['test' => 'yes'], [0.55]]);
        $this->registry->registerHistogram('histogram', 'help', ['test' => 'yes'], [0.55]);
    }

    public function testGetOrRegisterGauge(): void
    {
        $this->expectRegistryMethodToBeCalledWith('getOrRegisterGauge', 'gauge', ['help', ['test' => 'yes']]);
        $this->registry->getOrRegisterGauge('gauge', 'help', ['test' => 'yes']);
    }

    public function testGetOrRegisterCounter(): void
    {
        $this->expectRegistryMethodToBeCalledWith('getOrRegisterCounter', 'counter', ['help', ['test' => 'yes']]);
        $this->registry->getOrRegisterCounter('counter', 'help', ['test' => 'yes']);
    }

    public function testGetOrRegisterHistogram(): void
    {
        $this->expectRegistryMethodToBeCalledWith('getOrRegisterHistogram', 'histogram', ['help', ['test' => 'yes'], [0.55]]);
        $this->registry->getOrRegisterHistogram('histogram', 'help', ['test' => 'yes'], [0.55]);
    }

    public function testGetMetricFamilySamples(): void
    {
        $this->nestedRegistryStub
            ->expects($this->once())
            ->method('getMetricFamilySamples')
            ->willReturn([]);

        $this->registry->getMetricFamilySamples();
    }
}
