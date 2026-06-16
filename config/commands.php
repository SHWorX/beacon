<?php
/*
 * Project:     Beacon
 * File:        commands.php
 * Date:        2026-06-12
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

use App\Console\Commands\Beacon\GenerateAppSecretCommand;
use App\Console\Commands\Beacon\GeneratePasswordHashCommand;
use App\Console\Commands\Beacon\GenerateUuidCommand;
use App\Console\Commands\Beacon\MakeControllerCommand;
use App\Console\Commands\Beacon\MakeDtoCommand;
use App\Console\Commands\Beacon\MakeMiddlewareCommand;
use App\Console\Commands\Beacon\MakeMigrationCommand;
use App\Console\Commands\Beacon\MakeModelCommand;
use App\Console\Commands\Beacon\MakeProviderCommand;
use App\Console\Commands\Beacon\MigrateCommand;
use App\Console\Commands\Beacon\MigrateRollbackCommand;

return [
    GenerateAppSecretCommand::class,
    MakeMigrationCommand::class,
    MakeControllerCommand::class,
    MakeDtoCommand::class,
    MakeMiddlewareCommand::class,
    MakeModelCommand::class,
    MakeProviderCommand::class,
    MigrateCommand::class,
    MigrateRollbackCommand::class,
    GeneratePasswordHashCommand::class,
    GenerateUuidCommand::class,
];