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
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasUuid;

    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'username',
        'email',
        'email_verification_token',
        'email_verification_expires_at',
        'password',
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