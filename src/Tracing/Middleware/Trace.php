<?php

namespace Programic\LaravelKubernetes\Tracing\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Programic\LaravelKubernetes\Tracing\Facades\Tracing;

class Trace
{
    public function handle(Request $request, Closure $next): Response
    {
        Tracing::mount($request);

        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        Tracing::unmount($request, $response);
    }
}