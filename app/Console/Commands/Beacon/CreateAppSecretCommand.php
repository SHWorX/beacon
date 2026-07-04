<?php
/*
 * Project:     Beacon
 * File:        CreateAppSecretCommand.php
 * Date:        2026-06-12
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Console\Commands\Beacon;

use App\Console\Command;
use App\Generators\AppSecretGenerator;
use Random\RandomException;

final class CreateAppSecretCommand extends Command
{
    protected string $signature = 'app:secret';
    protected string $description = 'Generate a new app secret';

    /**
     * @param AppSecretGenerator $generator
     */
    public function __construct(
        private readonly AppSecretGenerator $generator,
    ) { }

    /**
     * @throws RandomException
     */
    public function handle(): int
    {
        $this->success('Creating new app key...');
        $secret = $this->generator->generate();

        $this->line(PHP_EOL . 'APP SECRET: ' . $secret . PHP_EOL);
        $this->warning('Please copy the secret and and add it to "APP_SECRET" in your .env file.');

        return 0;
    }
}