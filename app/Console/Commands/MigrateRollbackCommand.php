<?php
/*
 * Project:     Beacon
 * File:        MigrateRollbackCommand.php
 * Date:        2026-06-12
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Console\Commands;

use App\Console\Command;
use App\Database\MigrationRepository;

final class MigrateRollbackCommand extends Command
{
    protected string $signature = 'migrate:rollback';

    protected string $description = 'Rollback last migration batch';

    public function __construct(
        private readonly MigrationRepository $repository,
    ) { }

    public function handle(): int
    {
        foreach ($this->repository->lastBatch() as $migrationRecord) {
            $file = database_path('migrations/' . $migrationRecord->migration
            );

            if (!file_exists($file)) {
                continue;
            }

            $migration = require $file;
            $migration->down();
            $this->repository->remove(
                $migrationRecord->migration
            );
            $this->success("Rolled back $migrationRecord->migration");
        }

        return 0;
    }
}