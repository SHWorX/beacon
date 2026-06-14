<?php
/*
 * Project:     Beacon
 * File:        Redirect.php
 * Date:        2026-06-02
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Support;

use App\Http\Response;

class Redirect
{
    public function to(string $url): Response
    {
        return Response::redirect($url);
    }

    public function back(): Response
    {
        return Response::redirect($_SERVER['HTTP_REFERER'] ?? '/');
    }
}