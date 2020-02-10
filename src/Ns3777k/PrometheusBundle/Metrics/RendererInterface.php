<?php

declare(strict_types=1);

namespace Ns3777k\PrometheusBundle\Metrics;

use Prometheus\RenderTextFormat;

interface RendererInterface
{
    const MIME_TYPE_PLAIN = RenderTextFormat::MIME_TYPE;

    public function render(array $metrics): string;
}
