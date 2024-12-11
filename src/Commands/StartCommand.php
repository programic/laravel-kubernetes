<?php

namespace Programic\LaravelKubernetes\Commands;

use Illuminate\Support\Str;
use Laravel\Octane\Commands\StartFrankenPhpCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

#[AsCommand(name: 'kubernetes:start')]
class StartCommand extends StartFrankenPhpCommand
{
    protected $hidden = false;

    /**
     * The command's signature.
     *
     * @var string
     */
    public $signature = 'kubernetes:start
                    {--host=127.0.0.1 : The IP address the server should bind to}
                    {--port=80 : The port the server should be available on}
                    {--admin-host=localhost : The host the admin server should be available on}
                    {--admin-port=2019 : The port the admin server should be available on}
                    {--workers=auto : The number of workers that should be available to handle requests}
                    {--max-requests=500 : The number of requests to process before reloading the server}
                    {--caddyfile= : The path to the FrankenPHP Caddyfile file}
                    {--https : Enable HTTPS, HTTP/2, and HTTP/3, and automatically generate and renew certificates}
                    {--http-redirect : Enable HTTP to HTTPS redirection (only enabled if --https is passed)}
                    {--watch : Automatically reload the server when the application is modified (enable by default if APP_ENV is local)}
                    {--poll : Use file system polling while watching in order to watch files over a network}
                    {--log-level=INFO : Log messages at or above the specified log level}';

    public $description = 'Start the server';

    protected function writeServerOutput($server): void
    {
        [$output, $errorOutput] = $this->getServerOutput($server);

        Str::of($output)
            ->explode("\n")
            ->filter()
            ->each(function ($output): void {
                $this->output->writeln($output);
            });

        Str::of($errorOutput)
            ->explode("\n")
            ->filter()
            ->each(function ($output): void {
                $debug = json_decode($output, true);

                if (! is_array($debug)) {
                    $this->components->info($output);

                    return;
                }

                if (isset($debug['logger']) && Str::startsWith($debug['logger'], 'http.log.access.log')) {
                    $this->output->writeln($output);
                }
            });
    }

    protected function startServerWatcher()
    {
        $watch = config('kubernetes.watch.enabled') || $this->option('watch');

        if (! $watch) {
            return new class
            {
                public function __call($method, $parameters)
                {
                    return null;
                }
            };
        }

        if (empty($paths = config('octane.watch'))) {
            throw new InvalidArgumentException(
                'List of directories/files to watch not found. Please update your "config/octane.php" configuration file.',
            );
        }

        return tap(new Process([
            (new ExecutableFinder)->find('node'),
            'file-watcher.cjs',
            json_encode(collect(config('octane.watch'))->map(fn ($path) => base_path($path))),
            $this->option('poll'),
        ], realpath(__DIR__.'/../../bin'), null, null, null))->start();
    }
}