<?php

namespace Programic\LaravelKubernetes;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class KubernetesServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/kubernetes.php', 'kubernetes');
    }

    public function boot()
    {
        $this->registerPublishing();
        $this->setOctaneConfig();
        $this->registerCommands();
    }

    protected function registerCommands(): void
    {
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

    protected function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/kubernetes.php' => config_path('kubernetes.php'),
            ], 'kubernetes-config');
        }
    }
}