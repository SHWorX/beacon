<?php
/*
 * Project:     beacon
 * File:        EncryptionService.php
 * Date:        2026-07-02
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Services;

use JsonException;
use Random\RandomException;
use RuntimeException;

final readonly class EncryptionService
{
    private string $appSecret;
    private const string CIPHER = 'aes-256-gcm';

    public function __construct() {
        $this->appSecret = config('APP_SECRET');
    }

    /**
     * Encrypt the payload
     *
     * @param string $plaintext
     *
     * @return string
     * @throws JsonException
     * @throws RandomException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function encrypt(string $plaintext): string
    {
        $iv = random_bytes(openssl_cipher_iv_length(self::CIPHER));
        $tag = '';

        $ciphertext = openssl_encrypt(
            $plaintext,
            self::CIPHER,
            $this->key(),
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
        );

        if ($ciphertext === false) {
            throw new RuntimeException('Unable to encrypt value.');
        }

        return base64_encode(
            json_encode([
                'iv' => base64_encode($iv),
                'tag' => base64_encode($tag),
                'value' => base64_encode($ciphertext),
            ], JSON_THROW_ON_ERROR)
        );
    }

    /**
     * Decrypt the payload
     *
     * @param string $payload
     *
     * @return string
     * @throws JsonException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function decrypt(string $payload): string
    {
        $payload = json_decode(
            base64_decode($payload),
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        $plaintext = openssl_decrypt(
            base64_decode($payload['value']),
            self::CIPHER,
            $this->key(),
            OPENSSL_RAW_DATA,
            base64_decode($payload['iv']),
            base64_decode($payload['tag']),
        );

        if ($plaintext === false) {
            throw new RuntimeException('Unable to decrypt value.');
        }

        return $plaintext;
    }

    /**
     * Returns a hashed version of the APP_SECRET
     *
     * @return string
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    private function key(): string
    {
        return hash('sha256', $this->appSecret, true);
    }
}