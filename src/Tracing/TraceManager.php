<?php

namespace Programic\LaravelKubernetes\Tracing;


use Illuminate\Http\Request;
use Spatie\OpenTelemetry\Facades\Measure;
use Spatie\OpenTelemetry\Support\ParsedTraceParentHeaderValue;
use Spatie\OpenTelemetry\Support\Span;
use Symfony\Component\HttpFoundation\Response;

class TraceManager
{
    protected ?string $traceParent = null;
    protected ?Span $appSpan = null;

    public function __construct()
    {
    }

    public function mount(Request $request): self
    {
        if ($this->appSpan) {
            return $this;
        }

        $this->traceParent = (string) $request->header(config('kubernetes.tracing.traceparent_header'));

        if (! $parsedHeader = ParsedTraceParentHeaderValue::make($this->traceParent)) {
            return $this;
        }

        Measure::setTraceId($parsedHeader->traceId);

        $this->appSpan = Measure::start('http.server');
        $this->appSpan->tags([
            'span.category' => 'http',
            'span.description' => $request->path(),
            'http.request.method' => $request->method(),
            'http.request.url' => $request->fullUrl(),
        ]);

        return $this;
    }

    public function unmount(Request $request, Response $response): self
    {
        if (!$this->appSpan) {
            return $this;
        }

        Measure::stop('http.server');
        $this->appSpan->stop();
        $this->appSpan = null;

        return $this;
    }

    public function makeSpan(string $name, array $properties = []): Span
    {
        return Measure::start($name, $properties);
    }

    public function stopSpan(Span $span): self
    {
        Measure::stop($span->name);
        $span->stop();

        return $this;
    }

    public function currentSpan(): ?Span
    {
        return Measure::currentSpan();
    }
}