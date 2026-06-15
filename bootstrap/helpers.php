<?php
/*
 * Project:     Beacon
 * File:        helpers.php
 * Date:        2026-06-01
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

use App\Container\Application;
use App\Routing\Router;
use App\Services\AuthService;
use App\Support\Config;
use App\Support\Env;

if (!function_exists('strip_first_slash')) {
    /**
     * Strips the first slash from a path if exists
     *
     * @param string $path
     *
     * @return string
     * @author Steffen Haase <shworx.development@gmail.com
     */
    function strip_first_slash(string $path): string
    {
        if (str_starts_with($path, '/')) {
            return substr($path, 1);
        }

        return $path;
    }
}

if (!function_exists('base_path')) {
    /**
     * Returns the base path
     *
     * @param string $path Sub path under base path
     *
     * @return string
     * @author Steffen Haase <shworx.development@gmail.com
     */
    function base_path(string $path = ''): string
    {
        $base = dirname(__DIR__);

        return $path === '' ? $base : $base . '/' . strip_first_slash($path);
    }
}

if (!function_exists('config_path')) {
    /**
     * Returns the config path
     *
     * @param string $path Sub path under config path
     *
     * @return string
     * @author Steffen Haase <shworx.development@gmail.com
     */
    function config_path(string $path = ''): string
    {
        $path = strip_first_slash($path);
        return base_path('config') . ($path ? '/' . $path : '');
    }
}

if (!function_exists('app_path')) {
    /**
     * Returns the app path
     *
     * @param string $path Sub path under app path
     *
     * @return string
     * @author Steffen Haase <shworx.development@gmail.com
     */
    function app_path(string $path = ''): string
    {
        $path = strip_first_slash($path);
        return base_path('app') . ($path ? '/' . $path : '');
    }
}

if (!function_exists('storage_path')) {
    /**
     * Returns the storage path
     *
     * @param string $path
     *
     * @return string
     * @author Steffen Haase <shworx.development@gmail.com
     */
    function storage_path(string $path = ''): string
    {
        $path = strip_first_slash($path);
        return base_path('storage') . ($path ? '/' . $path : '');
    }
}

if (!function_exists('resource_path')) {
    /**
     * Returns the resources path
     *
     * @param string $path
     *
     * @return string
     * @author Steffen Haase <shworx.development@gmail.com
     */
    function resource_path(string $path = ''): string
    {
        $path = strip_first_slash($path);
        return base_path('resources') . ($path ? '/' . $path : '');
    }
}

if (!function_exists('database_path')) {
    /**
     * Returns the database path
     *
     * @param string $path
     *
     * @return string
     * @author Steffen Haase <shworx.development@gmail.com
     */
    function database_path(string $path = ''): string
    {
        $path = strip_first_slash($path);
        return base_path('database') . ($path ? '/' . $path : '');
    }
}

if (!function_exists('env')) {
    /**
     * Returns the value of an ENVIRONMENT variable
     *
     * If \$default is specified, the type must match $type.<br><br>
     * Examples:
     * <pre>
     *     // Valid
     *     config('MY_VAR');
     *     config('MY_VAR', 'my default');
     *     config('MY_VAR', 'my default', 'string');
     *     config('MY_VAR', 'my default', 'int');
     *     config('MY_VAR', true, 'bool');
     *     config(key: 'MY_VAR', type: bool)
     *
     *     // Invalid
     *     config('MY_VAR');
     *     config('MY_VAR', 'my default', 'int');
     *     config('MY_VAR', 0, 'string');
     *     config('MY_VAR', 1, 'bool');
     * </pre>
     *
     * If \$type is "string" (default) or "int", the "default" value will be null if \$default is omitted.<br>
     * If \$type is "bool", the default value will be (bool) false if \$default is omitted.
     *
     * @param string $key ENVIRONMENT variable name
     * @param mixed|null $default [optional] Default value
     * @param string|null $type [optional] Return type (default: string)
     *
     * @return string|bool|int|null
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    function env(
        string $key,
        mixed $default = null,
        ?string $type = 'string',
    ): string|bool|int|null {
        if ($type === 'bool' && $default === null) {
            $default = false;
        }

        switch ($type) {
            case 'bool':
                if ($default === null) {
                    $default = false;
                }

                if (!is_bool($default)) {
                    throw new InvalidArgumentException('Default value must be boolean.');
                }

                return Env::bool($key, $default);
            case 'int':
                if ($default !== 'null' && !is_int($default)) {
                    throw new InvalidArgumentException('Default value must be integer.');
                }

                return Env::int($key, $default);
            case 'string':
            default:
                return Env::get($key, $default);
        }
    }
}

if (!function_exists('config')) {
    try {
        /**
         * @param string $key
         * @param mixed|null $default
         *
         * @return mixed
         */
        function config(string $key, mixed $default = null): mixed
        {
            return Application::container()
                ->make(Config::class)
                ->get($key, $default);
        }
    } catch (Throwable $exception) {
        error_log($exception->getMessage());
    }
}

if (!function_exists('create_dl_data')) {
    /**
     * Creates required download link data
     *
     * Returns an array with 3 elements:<br>
     * name = Base64 encoded name<br>
     * exp = Current timestamp + 10 minutes<br>
     * sig = Cryptographic signature<br><br>
     * Example:
     * <pre>
     *     [
     *         'name' => 'bXktZG93bmxvYWQ=',
     *         'exp'  => 1780917412,
     *         'sig'  => '47b562696a27a321fdf74b8975814f792e8b8be525fb6c656160140404f5f4e7'
     *     ]
     * </pre>
     *
     * @param string $name Name of the download
     *
     * @return array
     */
    function create_dl_data(string $name): array
    {
        $exp = time() + 600; // 10 minutes
        $secret = config('app.secret');
        $sig = hash_hmac('sha256', $name . '|' . $exp, $secret);
        return [
            'name' => base64_encode($name),
            'exp' => $exp,
            'sig' => $sig,
        ];
    }
}

if (!function_exists('validate_dl_data')) {
    /**
     * Validates download link data
     *
     * Throws a Runtime exception if the validation fails, otherwise (bool) true.
     *
     * @param string $name The name (Base64 decoded)
     * @param int $exp Expiry
     * @param string $sig Signature
     *
     * @return bool
     * @throws RuntimeException
     */
    function validate_dl_data(string $name, int $exp, string $sig): bool
    {
        if ($exp < time()) {
            throw new RuntimeException('expired');
        }

        $secret = config('app.secret');
        $expectedSig = hash_hmac('sha256', $name . '|' . $exp, $secret);

        if (!hash_equals($expectedSig, $sig)) {
            throw new RuntimeException('invalid signature');
        }

        return true;
    }
}

if (!function_exists('auth')) {
    try {
        /**
         * @return AuthService
         * @author Steffen Haase <shworx.development@gmail.com
         */
        function auth(): AuthService
        {
                return Application::container()->make(AuthService::class);
        }
    } catch (Throwable $exception) {
        error_log($exception->getMessage());
    }
}

if (!function_exists('route')) {
    try {
        /**
         * Returns a route by name
         *
         * @param string $name
         * @param array $parameters [optional]
         *
         * @return string
         * @author Steffen Haase <shworx.development@gmail.com
         */
        function route(string $name, array $parameters = []): string
        {
            return Application::container()
                ->make(Router::class)
                ->route($name, $parameters);
        }
    } catch (Throwable $exception) {
        error_log($exception->getMessage());
    }
}

if (!function_exists('current_route')) {
    /**
     * Returns the current route
     *
     * @return string|null
     * @author Steffen Haase <shworx.development@gmail.com
     */
    function current_route(): ?string
    {
        return $_SERVER['_route'] ?? null;
    }
}

if (!function_exists('app_url')) {
    /**
     * Returns an application URL
     *
     * @param string|null $path [optional] URL path
     *
     * @return string
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    function app_url(?string $path = null): string
    {
        $app_url = config('app.url');

        if ($path !== null) {
            if (str_starts_with($path, '/')) {
                $path = substr($path, 1);
            }

            $app_url = rtrim($app_url, '/') . '/' . $path;
        }

        return $app_url;
    }
}
if (!function_exists('asset')) {
    /**
     * Returns an "asset" URL
     *
     * @param $path
     *
     * @return string
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    function asset($path): string
    {
        if (str_starts_with($path, '/')) {
            $path = substr($path, 1);
        }

        return app_url('assets/' . $path);
    }
}
