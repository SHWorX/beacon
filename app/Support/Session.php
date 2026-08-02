<?php
/*
 * Project:     Beacon
 * File:        Session.php
 * Date:        2026-06-02
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Support;

use App\Services\EncryptionService;
use JsonException;
use Psr\Log\LoggerInterface;
use Random\RandomException;

class Session
{
    public function __construct(
        private readonly EncryptionService $encryptionService,
        private readonly LoggerInterface $logger,
    ) {}

    /**
     * Get a session value
     *
     * @param string $key         Session key
     * @param mixed|null $default [optional] Default value (default: null)
     * @param bool $decrypt       [optional] Decrypt value (default: false)
     *
     * @return mixed
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function get(string $key, mixed $default = null, bool $decrypt = false): mixed
    {
        if (!$this->has($key)) {
            return $default;
        }

        $value = $_SESSION[$key];

        if ($decrypt && $value !== null) {
            try {
                $value = $this->encryptionService->decrypt($value);
            } catch (JsonException $e) {
                $this->logger->error($e->getMessage() . "\nStacktrace:\n" .$e->getTraceAsString());
            }
        }

        return $value ?? $default;
    }

    /**
     * Add a new session key
     *
     * @param string $key Session key
     * @param mixed $value The value of the key
     * @param bool $encrypt [optional] Encrypt value (default: false)
     *
     * @return void
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function set(string $key, mixed $value, bool $encrypt = false): void
    {
        if ($encrypt) {
            try {
                $value = $this->encryptionService->encrypt($value);
            } catch (JsonException|RandomException $e) {
                $this->logger->error($e->getMessage() . "\nStacktrace:\n" .$e->getTraceAsString());
            }
        }

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