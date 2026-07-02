<?php
/*
 * Project:     Beacon
 * File:        RememberToken.php
 * Date:        2026-06-11
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Models;

use App\Models\Traits\HasUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $user_id
 * @property string $token_hash
 * @property Carbon $expires_at
 */

class RememberToken extends Model
{
    use HasUuid;

    protected $table = 'remember_tokens';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'user_id',
        'token_hash',
        'expires_at',
    ];
}