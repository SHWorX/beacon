<?php
/*
 * Project:     beacon
 * File:        TwoFactorService.php
 * Date:        2026-07-02
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Services;

use App\Generators\QrCodeGenerator;
use App\Generators\RecoveryCodeGenerator;
use App\Generators\TotpSecretGenerator;
use App\Helpers\StringHelper;
use App\Models\User;
use App\Security\PendingTwoFactorSetup;
use App\Security\Totp;
use App\Support\Session;
use Carbon\CarbonImmutable;
use Endroid\QrCode\Exception\ValidationException;
use JsonException;
use Random\RandomException;

final readonly class TwoFactorService
{
    private const string SESSION_KEY = 'two_factor.pending_setup';

    public function __construct(
        private Totp $totp,
        private EncryptionService $encryption,
        private TotpSecretGenerator $secretGenerator,
        private RecoveryCodeGenerator $recoveryGenerator,
        private QrCodeGenerator $qrCodeGenerator,
    ) {}

    /**
     * Starts the two-factor setup
     *
     * @throws RandomException
     */
    public function beginSetup(Session $session): PendingTwoFactorSetup
    {
        $setup = new PendingTwoFactorSetup(
            id: StringHelper::uuid7(),
            secret: $this->secretGenerator->generate(),
            createdAt: CarbonImmutable::now(),
        );

        $session->set(self::SESSION_KEY, $setup);

        return $setup;
    }

    /**
     * Verifies the secret
     *
     * @param string $secret
     * @param string $code
     *
     * @return bool
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function verifySecret(string $secret, string $code): bool
    {
        return $this->totp->verify($secret, $code);
    }

    /**
     * Enables two-factor for user
     *
     * @param User $user
     * @param PendingTwoFactorSetup $setup
     *
     * @return array
     * @throws JsonException
     * @throws RandomException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function enable(User $user, PendingTwoFactorSetup $setup): array
    {
        $recoveryCodes = $this->recoveryGenerator->generate(config('twofactor.recovery_codes'));

        $user->totp_secret = $this->encryptSecret($setup->secret);
        $user->totp_enabled_at = CarbonImmutable::now();
        $user->totp_recovery_codes = $this->hashRecoveryCodes($recoveryCodes);
        $user->save();

        return $recoveryCodes;
    }

    /**
     * Disables two-factor for user
     *
     * @param User $user
     *
     * @return void
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function disable(User $user): void
    {
        $user->totp_secret = null;
        $user->totp_enabled_at = null;
        $user->totp_recovery_codes = null;

        $user->save();
    }

    /**
     * Verifies the TOTP code
     *
     * @param User $user
     * @param string $code
     *
     * @return bool
     * @throws JsonException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function verify(User $user, string $code): bool
    {
        if (!$user->hasTwoFactorEnabled()) {
            return false;
        }

        return $this->totp->verify($this->decryptSecret($user->totp_secret), $code);
    }


    /**
     * Returns the Otp auth URI
     *
     * @param User $user
     * @param string $secret
     *
     * @return string
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function getOtpAuthUri(User $user, string $secret): string
    {
        $issuer = config('twofactor.issuer');

        return sprintf(
            'otpauth://totp/%s:%s?secret=%s&issuer=%s',
            rawurlencode($issuer),
            rawurlencode($user->email),
            $secret,
            rawurlencode($issuer),
        );
    }

    /**
     * Encrypts the secret
     *
     * @param string $secret
     *
     * @return string
     * @throws JsonException
     * @throws RandomException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function encryptSecret(string $secret): string
    {
        return $this->encryption->encrypt($secret);
    }

    /**
     * Decrypts the secret
     *
     * @param string $secret
     *
     * @return string
     * @throws JsonException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function decryptSecret(string $secret): string
    {
        return $this->encryption->decrypt($secret);
    }

    /**
     * Hashes the recovery codes
     *
     * @param array $codes
     *
     * @return array
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    private function hashRecoveryCodes(array $codes): array
    {
        return array_map(
            static fn (string $code) => password_hash($code, PASSWORD_BCRYPT), $codes);
    }

    /**
     * Uses a recovery code
     *
     * @param User $user
     * @param string $code
     *
     * @return bool
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function useRecoveryCode(User $user, string $code): bool {
        $recoveryCodes = $user->totp_recovery_codes ?? [];

        foreach ($recoveryCodes as $index => $hash) {
            if (! password_verify($code, $hash)) {
                continue;
            }

            unset($recoveryCodes[$index]);
            $user->totp_recovery_codes = array_values($recoveryCodes);
            $user->save();

            return true;
        }

        return false;
    }

    /**
     * Returns the pending setup
     *
     * @param Session $session
     *
     * @return PendingTwoFactorSetup|null
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function pendingSetup(Session $session): ?PendingTwoFactorSetup
    {
        /** @var PendingTwoFactorSetup|null $setup */
        $setup = $session->get(self::SESSION_KEY);

        if ($setup === null) {
            return null;
        }

        if ($setup->isExpired()) {
            $session->forget(self::SESSION_KEY);
            return null;
        }

        return $setup;
    }

    /**
     * Clears the pending setup
     *
     * @param Session $session
     *
     * @return void
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function clearPendingSetup(Session $session): void
    {
        $session->forget(self::SESSION_KEY);
    }

    /**
     * Returns a QR code
     *
     * @param User $user
     * @param PendingTwoFactorSetup $setup
     *
     * @return string
     * @throws ValidationException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function getQrCodeSvg(User $user, PendingTwoFactorSetup $setup): string {
        return $this->qrCodeGenerator->generate($this->getOtpAuthUri($user, $setup->secret));
    }
}
