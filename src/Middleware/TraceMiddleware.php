<?php

namespace Programic\LaravelKubernetes\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Programic\LaravelKubernetes\Facades\Tracer;

class TraceMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $span = Tracer::getTracer()->spanBuilder('request')->startSpan();

        $response = $next($request);

        $span->end();

        return $response;
    }
}