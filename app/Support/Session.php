<?php
/*
 * Project:     Beacon
 * File:        Session.php
 * Date:        2026-06-02
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Support;

class Session
{
    /**
     * Get a session value
     *
     * @param string $key Session key
     * @param mixed|null $default [optional] Default value (default: null)
     *
     * @return mixed
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Add a new session key
     *
     * @param string $key Session key
     * @param mixed $value Value
     *
     * @return void
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Forget (remove) a session key
     *
     * @param string $key Session key
     *
     * @return void
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Check if a session key exists
     *
     * @param string $key
     *
     * @return bool
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Returns all session keys incl. their values
     *
     * @return array
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function all(): array
    {
        return $_SESSION;
    }
}