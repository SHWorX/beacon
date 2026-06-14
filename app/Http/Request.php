<?php
/*
 * Project:     Beacon
 * File:        Request.php
 * Date:        2026-06-01
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Http;

class Request
{
    public function method(): string
    {
         return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    /**
     * Returns the REQUEST URI
     *
     * @return string
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function uri(): string
    {
        return parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    }

    /**
     * @param string $key
     * @param string|null $default
     *
     * @return string|null
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function input(string $key, ?string $default = null): string|null
    {
        return $_REQUEST[$key] ?? $default;
    }

    /**
     * @param string $key
     * @param string|null $default
     *
     * @return string|null
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function query(string $key, ?string $default = null): string|null
    {
        return $_GET[$key] ?? $default;
    }

    /**
     * @param string $key
     * @param string|null $default
     *
     * @return string|null
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function post(string $key, ?string $default = null): string|null
    {
        return $_POST[$key] ?? $default;
    }

    /**
     * @return array
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function all(): array
    {
        return $_REQUEST;
    }

    /**
     * @return array
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function queryParams(): array
    {
        return $_GET;
    }

    /**
     * @return array
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function postParams(): array
    {
        return $_POST;
    }

    /**
     * @param string $key
     * @param mixed|null $default
     *
     * @return mixed
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function server(string $key, mixed $default = null): mixed
    {
        return $_SERVER[$key] ?? $default;
    }

    /**
     * @return bool
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function isPost(): bool
    {
        return $this->method() === 'POST';
    }

    /**
     * @return bool
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function isGet(): bool
    {
        return $this->method() === 'GET';
    }

    /**
     * @return bool
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function isPut(): bool
    {
        return $this->method() === 'PUT';
    }

    /**
     * @return bool
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function isDelete(): bool
    {
        return $this->method() === 'DELETE';
    }

    public function referer(): string
    {
        return $_SERVER['HTTP_REFERER'];
    }

    /**
     * Return all session variables
     *
     * @return array
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function session(): array
    {
        return $_SESSION;
    }

    /**
     * Get value from session
     *
     * @param string $key Session key
     * @param mixed|null $default [optional] Default value (default: null)
     *
     * @return mixed
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function sessionGet(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Set session value
     *
     * @param string $key Session key
     * @param mixed|null $value Value
     *
     * @return void
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function sessionSet(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }
}