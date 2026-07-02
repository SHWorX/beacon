<?php
/*
 * Project:     beacon
 * File:        UserController.php
 * Date:        2026-07-01
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Controllers;

use App\Http\Response;

class UserController extends Controller
{
    public function me(): Response
    {
        $userId = $this->auth->userId();
        if (!$userId) {
            return Response::json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = $this->auth->user();

        return Response::json([
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
        ]);
    }
}