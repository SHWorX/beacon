<?php
/*
 * Project:     beacon
 * File:        MakeDtoCommand.php
 * Date:        2026-06-16
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Console\Commands\Beacon;

use App\Console\Command;
use App\Console\Commands\Beacon\Traits\MakeTrait;

class MakeDtoCommand extends Command
{
    use MakeTrait;

    protected string $signature = 'make:dto {name}';
    protected string $description = 'Create a new data transfer object';

    public function handle(): int
    {
        $name = $this->argument('name');

        if ($name === null) {
            $this->error('DTO name is required.');

            return 1;
        }

        $name = str_replace('/', '\\', $name);

        if (!str_ends_with($name, 'Dto')) {
            $name .= 'Dto';
        }

        return $this->generate(
            $name,
            app_path('DTO'),
            'DTO',
            resource_path('stubs/dto.stub')
        );
    }
}