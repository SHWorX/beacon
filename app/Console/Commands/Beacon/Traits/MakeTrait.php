<?php
/*
 * Project:     Beacon
 * File:        MakeTrait.php
 * Date:        2026-06-15
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Console\Commands\Beacon\Traits;

trait MakeTrait
{
    /**
     * Generates the files
     *
     * @param string $name
     * @param string $basePath
     * @param string $baseNamespace
     * @param string $stubFile
     *
     * @return int
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    protected function generate(
        string $name,
        string $basePath,
        string $baseNamespace,
        string $stubFile
    ): int {
        $class = basename(str_replace('\\', '/', $name));
        $subNamespace = dirname(str_replace('\\', '/', $name));

        $namespace = "App\\{$baseNamespace}";
        if ($subNamespace !== '.') {
            $namespace .= '\\' . str_replace('/', '\\', $subNamespace);
        }

        $directory = $basePath;
        if ($subNamespace !== '.') {
            $directory .= '/' . $subNamespace;
        }

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $file = $directory . '/' . $class . '.php';
        if (file_exists($file)) {
            $this->error("File already exists.");

            return 1;
        }

        $stub = file_get_contents($stubFile);
        $stub = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$namespace, $class],
            $stub
        );

        file_put_contents($file, $stub);

        $this->success("Created: {$file}");

        return 0;
    }
}