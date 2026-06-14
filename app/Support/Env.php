<?php
/*
 * Project:     Beacon
 * File:        Env.php
 * Date:        2026-06-01
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Support;

class Env
{
    /**
     * Returns the value of an ENVIRONMENT variable
     *
     * @param string $key          ENVIRONMENT variable
     * @param string|null $default [optional] Default value (default: null)
     *
     * @return string|null
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public static function get(string $key, ?string $default = null): string|null
    {
        return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }

    /**
     * Returns the value of an ENVIRONMENT variable as BOOLEAN
     *
     * @param string $key         ENVIRONMENT variable
     * @param bool|false $default [optional] Default value (default: false)
     *
     * @return bool
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public static function bool(string $key, ?bool $default = false): bool
    {
        return filter_var(self::get($key, $default), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Returns the value of an ENVIRONMENT variable as INTEGER
     *
     * @param string $key ENVIRONMENT variable
     * @param int|null $default [optional] Default value (default: null)
     *
     * @return int|null
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public static function int(string $key, ?int $default = null): int|null
    {
        return (int) self::get($key, $default);
    }
}