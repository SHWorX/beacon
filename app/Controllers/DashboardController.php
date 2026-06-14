<?php
/*
 * Project:     Beacon
 * File:        DashboardController.php
 * Date:        2026-06-09
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Controllers;

use App\Http\Response;

class DashboardController extends Controller
{

    public function index(): Response
    {
        return $this->view('dashboard/index.twig');
    }
}