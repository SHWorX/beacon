<?php
/*
 * Project:     Beacon
 * File:        Flash.php
 * Date:        2026-06-02
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Support;

class Flash
{
    /**
     * Set flash data
     *
     * @param string $key Identifier key
     * @param mixed $data Data
     *
     * @return void
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function set(string $key, mixed $data): void
    {
        $_SESSION['_flash'][$key] = $data;
        $_SESSION['_flash_new'][] = $key;
    }

    /**
     * Get flash data
     *
     * Note: The flash data will still remain in the session.
     *
     * @param string $key Identifier key
     * @param mixed $default [optional] Default data
     *
     * @return mixed
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION['_flash'][$key] ?? $default;
    }

    /**
     * Get flash data
     *
     * Note: The flash data will be deleted from session when retrieving the message.
     *
     * @param string $key Identifier key
     * @param string $default [optional] Default data
     *
     * @return mixed
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function pull(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);

        return $value;
    }

//    public function old(string $key, mixed $default = null): mixed
    public function old(): mixed
    {
//        return ($_SESSION['_flash']['old'][$key] ?? $default);
        return $this->get('old', []);
    }

    public function errors(): array
    {
        return $this->get('errors', []);
    }

    public function error(string|array $data): void
    {
        $this->set('errors', $data);
    }

    public function success(string|array $data): void
    {
        $this->set('success', $data);
    }
}
