<?php
/*
 * Project:     Beacon
 * File:        PasswordResetToken.php
 * Date:        2026-06-14
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Models;

use App\Models\Traits\HasUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $user_id
 * @property string $token_hash
 * @property Carbon $expires_at
 */

class PasswordResetToken extends Model
{
    use HasUuid;

    protected $table = 'password_reset_tokens';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'user_id',
        'token_hash',
        'expires_at',
    ];

    /**
     * Returns the user model
     *
     * @return BelongsTo
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
