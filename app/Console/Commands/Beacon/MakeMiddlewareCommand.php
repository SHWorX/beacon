<?php
/*
 * Project:     Beacon
 * File:        MakeMiddlewareCommand.php
 * Date:        2026-06-15
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Console\Commands\Beacon;

use App\Console\Command;
use App\Console\Commands\Beacon\Traits\MakeTrait;

class MakeMiddlewareCommand extends Command
{
    use MakeTrait;

    protected string $signature = 'make:middleware {name}';
    protected string $description = 'Create a new middleware class';

    public function handle(): int
    {
        $name = $this->argument('name');

        if ($name === null) {
            $this->error('Middleware name is required.');

            return 1;
        }

        $name = str_replace('/', '\\', $name);

        if (!str_ends_with($name, 'Middleware')) {
            $name .= 'Middleware';
        }

        return $this->generate(
            $name,
            app_path('Middleware'),
            'Middleware',
            resource_path('stubs/middleware.stub')
        );
    }
}