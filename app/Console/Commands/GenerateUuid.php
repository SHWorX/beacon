<?php
/*
 * Project:     Beacon
 * File:        GenerateUuid.php
 * Date:        2026-06-13
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Console\Commands;

use App\Console\Command;
use Ramsey\Uuid\Uuid;

class GenerateUuid extends Command
{
    protected string $signature = 'generate:uuid {--v4}';
    protected string $description = 'Generates a UUID (default: v7)';

    public function handle(): int
    {
        $version = 'v7';

        if ($this->option('v4') !== null) {
            $version = 'v4';
        }

        $this->success('Generating UUID (' . $version . ') ...');

        if ($version === 'v4') {
            $uuid = Uuid::uuid4()->toString();
        } else {
            $uuid = Uuid::uuid7()->toString();
        }

        $this->line(PHP_EOL . 'GENERATED UUID: ' . $uuid . PHP_EOL);

        return 0;
    }
}