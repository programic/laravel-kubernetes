<?php

namespace Programic\LaravelKubernetes;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Programic\LaravelKubernetes\Middleware\TraceMiddleware;

class KubernetesServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/kubernetes.php', 'kubernetes');
    }

    public function boot(Router $router)
    {
        $this->registerPublishing();
        $this->setOctaneConfig();
        $this->registerCommands();

        $router->middlewareGroup('api', [TraceMiddleware::class]);
        $router->middlewareGroup('web', [TraceMiddleware::class]);
    }

    protected function registerCommands(): void
    {
        $this->app->singleton('tracer', fn () => new Tracer);

        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\InstallCommand::class,
                Commands\StartCommand::class,
            ]);
        }
    }

    protected function setOctaneConfig(): void
    {
        config()->set('octane.server', 'frankenphp');
        config()->set('octane.watch', config('kubernetes.watch.paths'));
        config()->set('octane.max_execution_time', config('kubernetes.max_execution_time'));
    }

    protected function setLoggingConfig(): void
    {
        config()->set('logging.channels.kubernetes', config('kubernetes.log'));

        if (!$this->app->environment('local', 'testing')) {
            config()->set('logging.default', 'kubernetes');
        }
    }

    protected function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/kubernetes.php' => config_path('kubernetes.php'),
            ], 'kubernetes-config');
        }
    }
}