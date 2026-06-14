<?php
/*
 * Project:     Beacon
 * File:        Controller.php
 * Date:        2026-06-02
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Controllers;

use App\Http\Response;
use App\Support\Session;
use App\View\View;
use Psr\Log\LoggerInterface;

class Controller
{
    public function __construct(
        protected View $view,
        protected Session $session,
        protected LoggerInterface $logger,
    ) { }

    protected function view(
        string $template,
        array $data = [],
        int $status = 200
    ): Response {
        return Response::html($this->view->render($template, $data), $status);
    }

    protected function redirect(string $url): Response
    {
        return Response::redirect($url);
    }

    protected function route(string $name, array $parameters = []): string
    {
        return route($name, $parameters);
    }
}