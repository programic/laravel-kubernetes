<?php

namespace Programic\LaravelKubernetes\Tracing\Facades;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;
use Programic\LaravelKubernetes\Tracing\TraceManager;
use Spatie\OpenTelemetry\Support\Span;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method static TraceManager mount(Request $request)
 * @method static TraceManager unmount(Request $request, Response $response)
 * @method static Span makeSpan(string $name, array $properties = [])
 * @method static TraceManager stopSpan(string $name)
 * @method static Span currentSpan()
 */
class Tracing extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return TraceManager::class;
    }
}