<?php
/*
 * Project:     beacon
 * File:        InstallCommand.php
 * Date:        2026-06-20
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Console\Commands\Beacon;

use App\Console\Command;
use App\Console\Commands\Beacon\Traits\EnvironmentTrait;
use App\Console\Kernel;
use App\Generators\AppSecretGenerator;
use Random\RandomException;
use RuntimeException;

class InstallCommand extends Command
{
    use EnvironmentTrait;

    protected string $signature = 'app:install';
    protected string $description = 'Installs the application';

    public function __construct(
        private readonly Kernel $kernel,
        private readonly AppSecretGenerator $appSecretGenerator,
    ) { }

    /**
     * @throws RandomException
     */
    public function handle(): int
    {
        // Create APP SECRET
//        try {
//            $this->setAppSecret();
//        } catch (RuntimeException $e) {
//            $this->error($e->getMessage());
//            return 1;
//        }

        $age = $this->ask('What is your age?');
        $this->line('You are ' . $age . ' years old.');

        $confirm = $this->confirm('Are you '.$age.' years old?') ? 'Yes' : 'No';
        $this->line('You said: ' .$confirm);

        return 0;
    }

    /**
     * Generates an APP secret and saves it as environment key "APP_SECRET"
     *
     * @return void
     * @throws RandomException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    private function setAppSecret(): void
    {
        $secret = $this->appSecretGenerator->generate();
        $this->line('Generated new secret: ' . $secret);
        $this->setEnvValue('APP_SECRET', $secret);
    }
}