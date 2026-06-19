<?php
/*
 * Project:     Beacon
 * File:        MakeMigrationCommand.php
 * Date:        2026-06-12
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Console\Commands\Beacon;

use App\Console\Command;
use App\Exceptions\NotFoundException;

final class MakeMigrationCommand extends Command
{
    protected string $signature = 'make:migration {name} {--table=}';
    protected string $description = 'Create a new migration file';

    public function handle(): int
    {
        $name = $this->argument('name');
        $table = $this->option('table');
        if ($table === true || $table === null) {
            $this->error('Table option must be passed with a name (example: --table=my_table)');
            return 1;
        }

        if (!$name) {
            $this->error('Migration name required.');
            return 1;
        }

        $timestamp = date('Y_m_d_His');
        $file = database_path("migrations/{$timestamp}_{$name}.php");

        if (preg_match('/^create_(.+)_table$/', $name, $matches)) {
            $type = 'create';
            $table ??= $matches[1];

        } elseif (preg_match('/^add_.+_to_(.+)_table$/', $name, $matches)) {
            $type = 'update';
            $table ??= $matches[1];

        } elseif (preg_match('/^drop_(.+)_table$/', $name, $matches)) {
            $type = 'drop';
            $table ??= $matches[1];

        } else {
            $type = 'blank';
        }

        try {
            $stub = $this->getStub($type);
        } catch (NotFoundException $e) {
            $this->error($e->getMessage());

            return 1;
        }

        $stub = str_replace('{{table}}', $table, $stub);

        if (file_put_contents($file, $stub)) {
            $this->success("Created migration: $file");

            return 0;
        }

        $this->error('Failed to create migration.');

        return 1;
    }

    /**
     * Returns the stub
     *
     * @param string $type
     *
     * @return string
     * @throws NotFoundException
     * @author Steffen Haase <shworx.development@gmail.com>
     */
    private function getStub(string $type): string
    {
        $stubFile = resource_path('stubs/migration.' . $type . '.stub');
        if (!file_exists($stubFile)) {
            throw new NotFoundException("Stub file \"$stubFile\" not found.");
        }

        return file_get_contents($stubFile);
    }
}