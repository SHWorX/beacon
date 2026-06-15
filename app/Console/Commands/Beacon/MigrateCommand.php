<?php
/*
 * Project:     Beacon
 * File:        MigrateCommand.php
 * Date:        2026-06-12
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Console\Commands\Beacon;

use App\Console\Command;
use App\Database\MigrationRepository;

final class MigrateCommand extends Command
{
    protected string $signature = 'migrate';

    protected string $description = 'Run pending migrations';

    public function __construct(
        private readonly MigrationRepository $repository,
    ) { }

    public function handle(): int
    {
        $this->repository->createRepository();
        $batch = $this->repository->nextBatchNumber();

        $files = glob(database_path('migrations/*.php'));
        sort($files);

        foreach ($files as $file) {
            $name = basename($file);

            if ($this->repository->hasRun($name)) {
                continue;
            }

            $migration = require $file;
            $this->line("Migrating $name", false);
            $migration->up();
            $this->repository->log(
                $name,
                $batch
            );
            $this->success('Done');
        }

        return 0;
    }
}
