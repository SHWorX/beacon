<?php
/*
 * Project:     Beacon
 * File:        AuthToken.php
 * Date:        2026-06-14
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Enums;

enum AuthToken
{
    case EMAIL;
    case PASSWORD;
}
