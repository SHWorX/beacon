<?php
/*
 * Project:     Beacon
 * File:        MakeControllerCommand.php
 * Date:        2026-06-15
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Console\Commands\Beacon;

use App\Console\Command;
use App\Console\Commands\Beacon\Traits\MakeTrait;

class MakeControllerCommand extends Command
{
    use MakeTrait;

    protected string $signature = 'make:controller {name}';
    protected string $description = 'Create a new controller class';

    public function handle(): int
    {
        $name = $this->argument('name');

        if ($name === null) {
            $this->error('Controller name is required.');

            return 1;
        }

        $name = str_replace('/', '\\', $name);

        if (!str_ends_with($name, 'Controller')) {
            $name .= 'Controller';
        }

        return $this->generate(
            $name,
            app_path('Controllers'),
            'Controllers',
            resource_path('stubs/controller.stub')
        );
    }
}