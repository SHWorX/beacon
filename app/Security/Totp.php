<?php
/*
 * Project:     beacon
 * File:        Totp.php
 * Date:        2026-07-02
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Security;

/**
 * This class fully implements RFC 4226 + RFC 6238
 */
final readonly class Totp
{
    public function __construct(
        private int $digits,
        private int $period,
        private int $window,
    ) { }

    /**
     * Generates the TOTP code
     *
     * @param string $secret
     * @param int|null $timestamp
     *
     * @return string
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function generate(string $secret, ?int $timestamp = null): string
    {
        $timestamp ??= time();
        $counter = intdiv($timestamp, $this->period);

        return $this->hotp(
            Base32::decode($secret),
            $counter,
        );
    }

    /**
     * Verifies a TOTP code
     *
     * @param string $secret
     * @param string $code
     *
     * @return bool
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function verify(string $secret, string $code): bool
    {
        $counter = intdiv(time(), $this->period);

        for ($i = -$this->window; $i <= $this->window; $i++) {
            if (hash_equals($this->hotp(Base32::decode($secret), $counter + $i), $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns a HOTP (RFC 4226)
     *
     * @param string $secret
     * @param int $counter
     *
     * @return string
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    private function hotp(string $secret, int $counter): string
    {
        $binaryCounter = pack('N2', $counter >> 32, $counter & 0xffffffff);
        $hash = hash_hmac('sha1', $binaryCounter, $secret, true);
        $offset = ord($hash[19]) & 0x0f;

        $binary = (
            ((ord($hash[$offset]) & 0x7f) << 24)
            | ((ord($hash[$offset + 1]) & 0xff) << 16)
            | ((ord($hash[$offset + 2]) & 0xff) << 8)
            | (ord($hash[$offset + 3]) & 0xff)
        );

        $otp = $binary % (10 ** $this->digits);

        return str_pad((string) $otp, $this->digits, '0', STR_PAD_LEFT);
    }
}
