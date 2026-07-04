<?php
/*
 * Project:     beacon
 * File:        RateLimit.php
 * Date:        2026-07-01
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Models;

use App\Models\Traits\HasUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $key
 * @property int $hits
 * @property Carbon $window_start
 * @property Carbon $expires_at
 */
class RateLimit extends Model
{
    protected $table = 'rate_limits';
    protected $primaryKey = 'key';

    protected $fillable = [
        'key',
        'hits',
        'window_start',
        'expires_at',
    ];

    protected $casts = [
        'window_start' => 'datetime',
        'expires_at' => 'datetime',
    ];
}
