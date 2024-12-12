<?php

namespace Programic\LaravelKubernetes\Facades;

use Illuminate\Support\Facades\Facade;
use OpenTelemetry\API\Trace\TracerInterface;

/**
 * @method static TracerInterface getTracer()
 */
class Tracer extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'tracer';
    }
}