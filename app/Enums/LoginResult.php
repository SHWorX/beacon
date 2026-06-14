<?php
/*
 * Project:     Beacon
 * File:        LoginResult.php
 * Date:        2026-06-13
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Enums;

enum LoginResult
{
    case SUCCESS;
    case INVALID_CREDENTIALS;
    case EMAIL_NOT_VERIFIED;
}