<?php
/*
 * Project:     Beacon
 * File:        Cookie.php
 * Date:        2026-06-11
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Support;

use App\Services\EncryptionService;
use Carbon\Carbon;
use Random\RandomException;

class Cookie
{
    /**
     * Set a cookie
     *
     * Examples for \$expiresOrOptions:
     *
     * Set options:
     * <pre>
     * [
     *     'expires' => strtotime('+60 days'),
     *     'path' => '/'
     *     'domain' => 'www.example.org',
     *     'secure' => true,
     *     'httponly' => true,
     *     'samesite' => 'Lax',
     * ]
     * </pre>
     *
     * Set expiration time:
     * <pre>
     *     $options = strtotime('+60 days');
     * </pre>
     *
     * @param string $name      Cookie name
     * @param string $value     Cookie value
     * @param int|null $expires [optional] Cookie expiry
     * @param bool $encrypt [optional] Encrypt cookie value (default: false)
     *
     * @return bool
     * @author Steffen Haase <shworx.development@gmail.com>
     */
    public static function set(
        string $name,
        string $value,
        ?int $expires = null,
        bool $encrypt = false
    ): bool {
        $options = [
            'expires' => $expires ?? Carbon::now()->addDays(30)->getTimestamp(),
            'path' => '/',
            'domain' => config('app.cookieDomain'),
            'secure' => config('app.env') !== 'local' ? config('app.cookieSecure') : false,
            'httponly' => true,
            'samesite' => config('app.cookieSameSite'),
        ];

        if ($encrypt) {
            $encryptionService = new EncryptionService();

            try {
                $value = $encryptionService->encrypt($value);
            } catch (JsonException|RandomException $e) {
                $this->logger->error($e->getMessage() . "\nStacktrace:\n" .$e->getTraceAsString());
            }
        }

        return setcookie($name, $value, $options);
    }
    /**
     * Returns the content of a cookie
     *
     * @param string $name Cookie name
     * @param bool $decrypt [optional] Decrypt cookie content (default: false)
     *
     * @return string|null
     * @author Steffen Haase <shworx.development@gmail.com>
     */
    public static function get(string $name, bool $decrypt = false): ?string
    {
        return $_COOKIE[$name] ?? null;
    }

    /**
     * Deletes a cookie
     *
     * @param string $name Cookie name
     *
     * @return bool
     * @author Steffen Haase <shworx.development@gmail.com>
     */
    public static function delete(string $name): bool
    {
        return self::set($name, '', time() - 3600);
    }
}