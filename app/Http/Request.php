<?php
/*
 * Project:     Beacon
 * File:        Request.php
 * Date:        2026-06-01
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Http;

use App\Support\Session;

class Request
{
    public function __construct(
        private readonly Session $session,
    ) {}

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

    public function referer(): ?string
    {
        return $_SERVER['HTTP_REFERER'] ?? null;
    }

    /**
     * Return all session variables
     *
     * @return Session
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function session(): Session
    {
        return $this->session;
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

    /**
     * Checks if the request expects a JSON response
     *
     * @return bool
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function expectsJson(): bool
    {
        $accept = $this->header('Accept', '');

        return str_contains($accept, 'application/json') || str_starts_with($this->uri(), '/api');
    }

    /**
     * Returns a header value
     *
     * @param string $key Header key
     * @param mixed|null $default [optional] Default value (default: null)
     *
     * @return mixed
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function header(string $key, mixed $default = null): mixed
    {
        $key = str_replace('-', '_', strtoupper($key));
        $key = 'HTTP_' . $key;

        if (isset($_SERVER[$key])) {
            return $_SERVER[$key];
        }

        if (function_exists('getallheaders')) {
            $headers = getallheaders();

            foreach ($headers as $name => $value) {
                if (strcasecmp($name, str_replace('_', '-', strtolower(substr($key, 5)))) === 0) {
                    return $value;
                }
            }
        }

        return $default;
    }

    /**
     * Get the client IP address.
     *
     * Attempts to resolve the real IP behind proxies/load balancers.
     *
     * @return string|null
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function ip(): ?string
    {
        $keys = [
            'HTTP_CF_CONNECTING_IP',   // Cloudflare
            'HTTP_X_REAL_IP',
            'HTTP_X_FORWARDED_FOR',
            'REMOTE_ADDR',
        ];

        foreach ($keys as $key) {
            if (!empty($_SERVER[$key])) {

                // X_FORWARDED_FOR may contain a list: client, proxy1, proxy2
                if ($key === 'HTTP_X_FORWARDED_FOR') {
                    $ips = explode(',', $_SERVER[$key]);
                    $ip = trim($ips[0]);
                } else {
                    $ip = $_SERVER[$key];
                }

                if ($this->isValidIp($ip)) {
                    return $ip;
                }
            }
        }

        return null;
    }

    /**
     * Validate IP address (IPv4 + IPv6)
     *
     * @param string $ip
     *
     * @return bool
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    private function isValidIp(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }
}
