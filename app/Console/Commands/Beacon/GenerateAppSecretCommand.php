<?php
/*
 * Project:     Beacon
 * File:        GenerateAppSecretCommand.php
 * Date:        2026-06-12
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Console\Commands\Beacon;

use App\Console\Command;
use Random\RandomException;

class GenerateAppSecretCommand extends Command
{
    protected string $signature = 'app:secret';

    protected string $description = 'Generate a new app secret';

    /**
     * @throws RandomException
     */
    public function handle(): int
    {
        $this->success('Creating new app key...');
        $this->line(PHP_EOL . 'APP SECRET: ' . bin2hex(random_bytes(32)) . PHP_EOL);
        $this->warning('Please copy the secret and and add it to "APP_SECRET" in your .env file.');

        return 0;
    }
}