<?php

declare(strict_types=1);

namespace Ns3777k\PrometheusBundle\DependencyInjection;

use Ns3777k\PrometheusBundle\EventListener\MetricsListener;
use Ns3777k\PrometheusBundle\Metrics\NamespacedCollectorRegistry;
use Prometheus\Storage\Adapter;
use Prometheus\Storage\APC;
use Prometheus\Storage\InMemory;
use Prometheus\Storage\Redis;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class Ns3777kPrometheusExtension.
 */
class Ns3777kPrometheusExtension extends Extension
{
    private $adapters = [
        'in_memory' => InMemory::class,
        'redis' => Redis::class,
        'apcu' => APC::class,
    ];

    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('prometheus.xml');

        $configuration = $this->processConfiguration(new Configuration(), $configs);

        $container->getDefinition(NamespacedCollectorRegistry::class)
            ->replaceArgument('$namespace', $configuration['namespace']);

        if (!$configuration['listener']) {
            $container->removeDefinition(MetricsListener::class);
        } else {
            $container->getDefinition(MetricsListener::class)
                ->replaceArgument('$ignoredRoutes', $configuration['ignored_routes']);
        }

        $adapterDefinition = $container->getDefinition(Adapter::class)
            ->setAbstract(false)
            ->setClass($this->adapters[$configuration['adapter']]);

        if ('redis' === $configuration['adapter']) {
            $adapterDefinition->setArgument(0, $configuration['redis']);
        }
    }
}
