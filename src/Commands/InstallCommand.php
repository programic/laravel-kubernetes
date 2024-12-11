<?php

namespace Programic\LaravelKubernetes\Commands;

use Illuminate\Console\Command;
use function Laravel\Prompts\spin;

class InstallCommand extends Command
{
    protected $signature = 'kubernetes:install
                            {--force : Overwrite any existing configuration files}';

    protected $description = 'Install components and resources';

    public function handle(): int
    {
        $this->callSilent('vendor:publish', [
            '--tag' => 'kubernetes-config',
            '--force' => $this->option('force'),
        ]);

        spin(
            function (): void {
                $this->callSilent(InstallOctaneCommand::class, ['--server' => 'frankenphp']);
            },
            'Installing Octane components and resources'
        );

        $this->components->info('Laravel kubernetes installed successfully.');

        return 0;
    }
}