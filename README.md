# Prometheus bundle

[![Build Status](https://travis-ci.org/ns3777k/prometheus-bundle.svg?branch=master)](https://travis-ci.org/ns3777k/prometheus-bundle)
[![codecov](https://codecov.io/gh/ns3777k/prometheus-bundle/branch/master/graph/badge.svg)](https://codecov.io/gh/ns3777k/prometheus-bundle)

## Requirements

- PHP 7.3+
- Symfony 4+

## Installing

There are 2 ways to install the bundle: automatically with flex or manually.

### Using symfony flex

```shell script
$ composer require ns3777k/prometheus-bundle
```

### Manually

1. Require the package:

```shell script
$ composer require ns3777k/prometheus-bundle
```

2. Register the bundle in `config/bundles.php`:

```php
<?php

return [
    // ...
    Ns3777k\PrometheusBundle\Ns3777kPrometheusBundle::class => ['all' => true],
];
```

3. Configure (see below)

## Usage

### Configuration

`config/packages/ns3777k_prometheus.yaml`:

```yaml
ns3777k_prometheus:
  namespace: app
  adapter: in_memory # or apcu or redis

  # listener (read below)
  listener:
    enabled: true
    ignored_routes: []

  # redis adapter settings
  redis:
    host: '127.0.0.1'
    port: 6379
    timeout: 0.1
    read_timeout: 10
    persistent_connections: false
    password:
    database:
```

`config/routes.yaml`:

```yaml
metrics:
  path: /metrics
  controller: 'Ns3777k\PrometheusBundle\Controller\MetricsController::prometheus'
```

### Builtin listener

By default the listener is turned on and collects only one histogram with
request duration in seconds (`request_duration_seconds`) with 3 labels: `code`,
`method` and `route`.

Histogram creates `total` and `count` metrics automatically.

Usually you don't wanna collect the metrics for routes like `_wdt` and `metrics`
(that's the route for `/metrics`) and that's where `listener.ignored_routes`
comes in.

If you use grafana to render graphs, you can import sample dashboard from ``
directory (it uses empty namespace).

### Collect own metrics

Builtin listener covers only basic information about the request and response.
You can use it to get top 10 requests, slow responses, calculate request rate
and etc.

But most of the time you wanna collect your own metrics. It's easy to do using
`CollectorRegistryInterface` (implemented by `NamespacedCollectorRegistry`).

Histogram example:

```php
<?php

declare(strict_types=1);

namespace App\Weather;

use Ns3777k\PrometheusBundle\Metrics\CollectorRegistryInterface;

class WeatherClient
{
    private $registry;

    public function __construct(CollectorRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function getWeatherForRegion(string $region)
    {
        $histogram = $this->registry->getOrRegisterHistogram(
            'weather_request_duration_seconds',
            'Weather request duration with response information',
            ['region']
        );

        $start = microtime(true);
        // do request
        $duration = microtime(true) - $start;

        $histogram->observe($duration, [$region]);
    }

}
```

No worries about the namespace. It will be prepended automatically from the
bundle's configuration.

## Security

Remember that when you add `/metrics` route it becomes publicly available from
the internet.

**It's you job to restrict access to it (using nginx for example).**
