<?php
/*
 * Project:     Beacon
 * File:        MakeModelCommand.php
 * Date:        2026-06-15
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Console\Commands\Beacon;

use App\Console\Command;

final class MakeModelCommand extends Command
{
    protected string $signature = 'make:model {name} {--standard} {--uuid}';
    protected string $description = 'Create a new Eloquent model';

    public function handle(): int
    {
        $name = $this->argument('name');
        $isStandard = $this->hasOption('standard');
        $isUuid = !$this->hasOption('standard');

        if ($name === null) {
            $this->error('Model name is required.');

            return 1;
        }

        $name = str_replace('/', '\\', $name);
        $class = basename(str_replace('\\', '/', $name));
        $subNamespace = dirname(str_replace('\\', '/', $name));

        $namespace = 'App\\Models';
        if ($subNamespace !== '.') {
            $namespace .= '\\' .
                str_replace('/', '\\', $subNamespace);
        }

        $directory = app_path('Models');
        if ($subNamespace !== '.') {
            $directory .= '/' . $subNamespace;
        }

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $file = $directory . '/' . $class . '.php';
        if (file_exists($file)) {
            $this->error(
                "Model already exists: {$file}"
            );

            return 1;
        }

        $table = $this->guessTableName($class);
        $stub = file_get_contents(resource_path(
            $isUuid ? 'stubs/model.uuid.stub' : 'stubs/model.standard.stub'
        ));

        $stub = str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ table }}'],
            [$namespace, $class, $table],
            $stub
        );

        file_put_contents($file, $stub);

        $this->success(sprintf(
            'Model created: %s (Type: %s)',
            $file,
            $isUuid ? 'UUID' : 'Standard'
        ));

        return 0;
    }

    /**
     * @param string $class
     *
     * @return string
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    private function guessTableName(string $class): string {
        $snake = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $class));

        return str_ends_with($snake, 's') ? $snake : $snake . 's';
    }
}
