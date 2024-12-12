<?php

namespace Programic\LaravelKubernetes;

use Exception;
use OpenTelemetry\API\Trace\TracerInterface;
use OpenTelemetry\Contrib\Zipkin\Exporter as ZipkinExporter;
use OpenTelemetry\Contrib\Otlp\SpanExporter as OtlpExporter;
use OpenTelemetry\SDK\Common\Export\Http\PsrTransport;
use OpenTelemetry\SDK\Common\Export\Http\PsrTransportFactory;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;

class Tracer
{
    protected TracerInterface $tracer;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $exporter = match (config('kubernetes.tracing.driver')) {
            'otlp' => new OtlpExporter((new PsrTransportFactory)->create(config('kubernetes.tracing.drivers.otlp.endpoint'), 'application/json')),
            'zipkin' => new ZipkinExporter((new PsrTransportFactory)->create(config('kubernetes.tracing.drivers.zipkin.endpoint'), 'application/json')),
            default => throw new Exception('Invalid tracing driver'),
        };

        $this->tracer = (new TracerProvider(new SimpleSpanProcessor($exporter)))
            ->getTracer(
                config('app.name', 'Laravel'),
                config('app.version', null),
            );
    }

    public function getTracer(): TracerInterface
    {
        return $this->tracer;
    }
}