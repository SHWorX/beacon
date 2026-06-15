<?php
/*
 * Project:     Beacon
 * File:        MakeProviderCommand.php
 * Date:        2026-06-15
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Console\Commands\Beacon;

use App\Console\Command;
use App\Console\Commands\Beacon\Traits\MakeTrait;

final class MakeProviderCommand extends Command
{
    use MakeTrait;

    protected string $signature = 'make:provider {name}';
    protected string $description = 'Create a new service provider';

    public function handle(): int
    {
        $name = $this->argument('name');

        if ($name === null) {
            $this->error('Provider name is required.');

            return 1;
        }

        $name = str_replace('/', '\\', $name);

        if (!str_ends_with($name, 'Provider')) {
            $name .= 'Provider';
        }

        return $this->generate(
            $name,
            app_path('Providers'),
            'Providers',
            resource_path('stubs/provider.stub')
        );
    }
}
