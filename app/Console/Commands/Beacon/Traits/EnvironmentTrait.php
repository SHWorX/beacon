<?php
/*
 * Project:     beacon
 * File:        EnvironmentTrait.php
 * Date:        2026-06-20
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Console\Commands\Beacon\Traits;

use RuntimeException;

trait EnvironmentTrait
{
    /**
     * Set (or add new) environment value
     *
     * @param string $key     Key
     * @param string $value   Value
     * @param string $envFile [optional] Environment file (default: .env)
     *
     * @return void
     * @throws RuntimeException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    protected function setEnvValue(string $key, string $value, string $envFile = '.env'): void
    {
        $file = base_path('/' . $envFile);

        if (!file_exists($file)) {
            throw new RuntimeException('File "' . $file . '" does not exist.');
        }

        $content = file_exists($file) ? file_get_contents($file) : '';
        $pattern = "/^" . preg_quote($key, '/') . "=.*$/m";

        if (preg_match('/\s/', $value)) {
            if (str_contains($value, '"')) {
                $value = "'" . $value . "'";
            } else if (str_contains($value, "'")) {
                $value = '"' . $value . '"';
            }
        }

        if (preg_match($pattern, $content)) {
            // Update existing key
            echo 'Found existing key "' . $key . '" and set new value.' . PHP_EOL;
            $content = preg_replace(
                $pattern,
                $key . '=' . $value,
                $content
            );
        } else {
            // Add new key
            echo 'Key "' . $key . '" not found, create new one.' . PHP_EOL;
            $content .= PHP_EOL . $key . '=' . $value;
        }

        echo 'Save updated ' . $file . PHP_EOL;
        if (!file_put_contents($file, $content)) {
            throw new RuntimeException('Cannot write ' . $file . '.');
        }
    }
}