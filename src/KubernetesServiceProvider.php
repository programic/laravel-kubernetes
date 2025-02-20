<?php

namespace Programic\LaravelKubernetes;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Programic\LaravelKubernetes\Metrics\Collectors\Horizon\CurrentMasterSupervisorCollector;
use Programic\LaravelKubernetes\Metrics\Collectors\Horizon\CurrentProcessesPerQueueCollector;
use Programic\LaravelKubernetes\Metrics\Collectors\Horizon\CurrentWorkloadCollector;
use Programic\LaravelKubernetes\Metrics\Collectors\Horizon\FailedJobsPerHourCollector;
use Programic\LaravelKubernetes\Metrics\Collectors\Horizon\HorizonStatusCollector;
use Programic\LaravelKubernetes\Metrics\Collectors\Horizon\JobsPerMinuteCollector;
use Programic\LaravelKubernetes\Metrics\Collectors\Horizon\RecentJobsCollector;
use Spatie\Prometheus\Facades\Prometheus;

class KubernetesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/kubernetes.php', 'kubernetes');

        $this->registerMetricsCollectors();
    }

    public function boot(): void
    {
        $this->registerPublishing();
        $this->registerCommands();

        $this->setOctaneConfig();
        $this->setPrometheusConfig();
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

    protected function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/kubernetes.php' => config_path('kubernetes.php'),
            ], 'kubernetes-config');
        }
    }

    protected function setOctaneConfig(): void
    {
        config()->set('octane.server', 'frankenphp');
        config()->set('octane.watch', config('kubernetes.watch.paths'));
        config()->set('octane.max_execution_time', config('kubernetes.max_execution_time'));
    }

    protected function setPrometheusConfig(): void
    {
        config()->set('prometheus.enabled', config('kubernetes.metrics.enabled'));
        config()->set('prometheus.urls.default', config('kubernetes.metrics.path'));
        config()->set('prometheus.middleware', config('kubernetes.metrics.middleware'));
    }

    protected function registerMetricsCollectors(): void
    {
        Prometheus::registerCollectorClasses([
            CurrentMasterSupervisorCollector::class,
            CurrentProcessesPerQueueCollector::class,
            CurrentWorkloadCollector::class,
            FailedJobsPerHourCollector::class,
            HorizonStatusCollector::class,
            JobsPerMinuteCollector::class,
            RecentJobsCollector::class,
        ]);
    }
}