<?php
/*
 * Project:     Beacon
 * File:        MiddlewareInterface.php
 * Date:        2026-06-02
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Interfaces;

use App\Http\Request;
use App\Http\Response;

interface MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response;
}