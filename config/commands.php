<?php
/*
 * Project:     Beacon
 * File:        commands.php
 * Date:        2026-06-12
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

use App\Console\Commands\GenerateAppSecret;
use App\Console\Commands\GeneratePasswordHash;
use App\Console\Commands\GenerateUuid;
use App\Console\Commands\MakeMigrationCommand;
use App\Console\Commands\MigrateCommand;
use App\Console\Commands\MigrateRollbackCommand;

return [
    GenerateAppSecret::class,
    MakeMigrationCommand::class,
    MigrateCommand::class,
    MigrateRollbackCommand::class,
    GeneratePasswordHash::class,
    GenerateUuid::class,
];