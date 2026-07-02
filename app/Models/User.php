<?php
/*
 * Project:     Beacon
 * File:        User.php
 * Date:        2026-06-01
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Models;

use App\Models\Traits\HasUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $username
 * @property string $email
 * @property Carbon $email_verified_at
 * @property string $email_verification_token
 * @property Carbon $email_verification_expires_at
 * @property string $totp_secret
 * @property Carbon $totp_enabled_at
 * @property Json $totp_recovery_codes
 */

class User extends Model
{
    use HasUuid;

    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = [
        'username',
        'password',
        'email',
        'email_verified_at',
        'email_verification_token',
        'email_verification_expires_at',
        'totp_secret',
        'totp_enabled_at',
        'totp_recovery_codes',
    ];

    /**
     * Check if email is verified
     *
     * @return bool
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function hasVerifiedEmail(): bool
    {
        return $this->email_verified_at !== null;
    }

    /**
     * Mark email as verified
     *
     * @return void
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function markEmailAsVerified(): void
    {
        $this->email_verified_at = Carbon::now();
        $this->email_verification_token = null;
        $this->email_verification_expires_at = null;
        $this->save();
    }
}