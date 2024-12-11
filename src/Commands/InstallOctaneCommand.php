<?php

namespace Programic\LaravelKubernetes\Commands;

use Laravel\Octane\Commands\InstallCommand;
use function Laravel\Prompts\select;

class InstallOctaneCommand extends InstallCommand
{
    public function handle()
    {
        return (int) ! tap(
            $this->installFrankenPhpServer()
        );
    }
}