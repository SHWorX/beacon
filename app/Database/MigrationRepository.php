<?php
/*
 * Project:     Beacon
 * File:        MigrationRepository.php
 * Date:        2026-06-12
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Database;

use Carbon\Carbon;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;

final readonly class MigrationRepository
{
    public function __construct(
        private Connection $connection,
    ) { }

    /**
     * Creates the migration table
     * 
     * @return void
     * @author Steffen Haase <shworx.development@gmail.com
     */
    public function createRepository(): void
    {
        if ($this->connection->getSchemaBuilder()->hasTable('migrations')) {
            return;
        }

        $this->connection->getSchemaBuilder()->create(
            'migrations',
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('migration')->unique();
                $table->integer('batch');
                $table->timestamp('executed_at');
            }
        );
    }

    /**
     * Checks if a migration has already been executed
     *
     * @param string $migration
     *
     * @return bool
     * @author Steffen Haase <shworx.development@gmail.com
     */
    public function hasRun(string $migration): bool
    {
        return $this->connection
            ->table('migrations')
            ->where('migration', $migration)
            ->exists();
    }

    /**
     * Log a migration
     *
     * @param string $migration
     * @param int $batch
     *
     * @return void
     * @author Steffen Haase <shworx.development@gmail.com
     */
    public function log(string $migration, int $batch): void
    {
        $this->connection->table('migrations')->insert([
            'migration' => $migration,
            'batch' => $batch,
            'executed_at' => Carbon::now(),
        ]);
    }

    /**
     * Removes a migration
     *
     * @param string $migration
     *
     * @return void
     * @author Steffen Haase <shworx.development@gmail.com
     */
    public function remove(string $migration): void
    {
        $this->connection
            ->table('migrations')
            ->where('migration', $migration)
            ->delete();
    }

    /**
     * Returns the next batch number
     *
     * @return int
     * @author Steffen Haase <shworx.development@gmail.com
     */
    public function nextBatchNumber(): int
    {
        return ((int) $this->connection
            ->table('migrations')
            ->max('batch')
        ) + 1;
    }

    /**
     * Returns the last batch
     *
     * @return array
     * @author Steffen Haase <shworx.development@gmail.com
     */
    public function lastBatch(): array
    {
        $batch = $this->connection
            ->table('migrations')
            ->max('batch');

        return $this->connection
            ->table('migrations')
            ->where('batch', $batch)
            ->orderByDesc('id')
            ->get()
            ->all();
    }
}