<?php
/*
 * Project:     Beacon
 * File:        GeneratePasswordHashCommand.php
 * Date:        2026-06-13
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Console\Commands\Beacon;

use App\Console\Command;

class GeneratePasswordHashCommand extends Command
{

    protected string $signature = 'generate:password-hash {password}';
    protected string $description = 'Generates a password hash for the given password';

    public function handle(): int
    {
        $this->success('Generating password hash ...');
        $password = $this->argument('password');
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $this->line(PHP_EOL . 'PASSWORD HASH: '. $hash . PHP_EOL);

        return 0;
    }
}