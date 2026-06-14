<?php
/*
 * Project:     Beacon
 * File:        app.php
 * Date:        2026-06-02
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

use App\Http\Kernel;
use App\Middleware\CsrfMiddleware;
use App\Middleware\RememberMeMiddleware;
use Psr\Log\LoggerInterface;

require_once dirname(__DIR__) . '/bootstrap/helpers.php';
require dirname(__DIR__) . '/bootstrap/session.php';

$container = require dirname(__DIR__) . '/bootstrap/app.php';
try {
    require dirname(__DIR__) . '/bootstrap/routes.php';

    $kernel = $container->make(Kernel::class);
    $kernel->addMiddleware(RememberMeMiddleware::class);
    $kernel->addMiddleware(CsrfMiddleware::class);
    $logger = $container->make(LoggerInterface::class);

    $response = $kernel->handle();
    $response->send();
} catch (Throwable $e) {
    $error = '';

    if (
        config('app.env') === 'local'
        && config('app.debug', false, 'bool') === true
    ) {
        $error = sprintf(
            '<div class="error"><h2>%s</h2><div class="trace"><b>Stacktrace:</b><pre>%s</pre></div>',
            $e->getMessage(),
            ltrim($e->getTraceAsString())
        );
    }

    $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>HTTP 500 - Internal Server Error</title>
        
        <style>
            body {
                background-color: #ff6767;
                font-family: sans-serif;
            }
            
            h1 {
                font-size: 3em;
                text-align: center;
            }
            
            .error h2 {
                text-align: center;
            }

            .trace {
                font-size: 1.2em;
            }
            .trace pre {
                margin-top: 0;
            }
        </style>
        
    </head>
    <body>
        <h1>HTTP 500 - Internal Server Error(blubb)</h1>
        $error
    </body>
</html>
HTML;

    echo $html;
}
