<?php
/*
 * Project:     Beacon
 * File:        LogoutController.php
 * Date:        2026-06-13
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Controllers\Auth;

use App\Controllers\Controller;
use App\Http\Response;
use App\Services\AuthService;

class LogoutController extends Controller
{
    /**
     * Process logout
     *
     * @param AuthService $auth
     *
     * @return Response
     * @author Steffen Haase <shworx.development@gmail.com
     */
    public function logout(
        AuthService $auth,
    ): Response {
        $auth->logout();

        return Response::redirect('/');
    }

}