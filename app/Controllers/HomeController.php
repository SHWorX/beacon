<?php
/*
 * Project:     Beacon
 * File:        HomeController.php
 * Date:        2026-06-01
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Controllers;

use App\Http\Response;
use App\Services\AuthService;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class HomeController extends Controller
{
    public function index(
        AuthService $auth,
    ): Response {
        if ($auth->check()) {
            return $this->redirect('/dashboard');
        }

        return $this->view('home/index.twig');
    }

    public function about(): Response
    {
        return $this->view('home/about.twig');
    }
}