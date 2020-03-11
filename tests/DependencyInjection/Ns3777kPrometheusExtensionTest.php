<?php

declare(strict_types=1);

namespace Ns3777k\PrometheusBundle\Tests\DependencyInjection;

use Ns3777k\PrometheusBundle\DependencyInjection\Ns3777kPrometheusExtension;
use Ns3777k\PrometheusBundle\EventListener\MetricsListener;
use Ns3777k\PrometheusBundle\Metrics\NamespacedCollectorRegistry;
use PHPUnit\Framework\TestCase;
use Prometheus\Storage\Adapter;
use Prometheus\Storage\APC;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class Ns3777kPrometheusExtensionTest extends TestCase
{
    public function testReplaceNamespace()
    {
        $extension = new Ns3777kPrometheusExtension();
        $config = ['ns3777k_prometheus' => ['namespace' => 'my_app']];
        $extension->load($config, $container = $this->getContainer());

        $namespace = $container->getDefinition(NamespacedCollectorRegistry::class)
            ->getArgument('$namespace');

        $this->assertEquals('my_app', $namespace);
    }

    public function testRemoveListenerWhenDisabled()
    {
        $extension = new Ns3777kPrometheusExtension();
        $config = [
            'ns3777k_prometheus' => [
                'listener' => [
                    'enabled' => false,
                ],
            ],
        ];
        $extension->load($config, $container = $this->getContainer());

        $this->assertFalse($container->hasDefinition(MetricsListener::class));
    }

    public function testSaveIgnoreRoutes()
    {
        $extension = new Ns3777kPrometheusExtension();
        $config = [
            'ns3777k_prometheus' => [
                'listener' => [
                    'ignored_routes' => ['some_route', 'new_something'],
                ],
            ],
        ];
        $extension->load($config, $container = $this->getContainer());

        $listenerDefinition = $container->getDefinition(MetricsListener::class);
        $arguments = $listenerDefinition->getArgument('$ignoredRoutes');

        $this->assertEquals(['some_route', 'new_something'], $arguments);
    }

    public function testResolveStorageAdapter()
    {
        $extension = new Ns3777kPrometheusExtension();
        $config = [
            'ns3777k_prometheus' => [
                'adapter' => 'apcu',
            ],
        ];
        $extension->load($config, $container = $this->getContainer());

        $adapterDefinition = $container->getDefinition(Adapter::class);
        $this->assertFalse($adapterDefinition->isAbstract());
        $this->assertEquals(APC::class, $adapterDefinition->getClass());
    }

    public function testUseRedisStorageAdapterOptions()
    {
        $extension = new Ns3777kPrometheusExtension();
        $config = [
            'ns3777k_prometheus' => [
                'adapter' => 'redis',
            ],
        ];
        $extension->load($config, $container = $this->getContainer());

        $expected = [
            'host' => '127.0.0.1',
            'port' => 6379,
            'timeout' => 0.1,
            'read_timeout' => 10,
            'persistent_connections' => false,
        ];
        $options = $container->getDefinition(Adapter::class)->getArgument(0);
        $this->assertEquals($expected, $options);
    }

    private function getContainer()
    {
        return new ContainerBuilder(new ParameterBag([
            'kernel.debug' => false,
            'kernel.bundles' => [],
            'kernel.cache_dir' => sys_get_temp_dir(),
            'kernel.environment' => 'test',
            'kernel.root_dir' => __DIR__.'/../../', // src dir
        ]));
    }
}
