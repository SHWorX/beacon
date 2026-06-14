<?php
/*
 * Project:     Beacon
 * File:        View.php
 * Date:        2026-06-01
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\View;

use App\Support\Flash;
use JetBrains\PhpStorm\NoReturn;
use Psr\Log\LoggerInterface;
use Throwable;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use TypeError;

readonly class View
{
    public function __construct(
        private Environment $twig,
        private Flash $flash,
        private LoggerInterface $logger,
    ) { }

    public function render(string $template, array $data = []): string
    {
        $data['old'] = $this->flash->old();
        $data['errors'] = $this->flash->errors();
        $data['_appName'] = config('app.name');
        $data['_appSlogan'] = config('app.slogan');
        $data['_appVersion'] = config('app.version');
        $data['_appCopyright'] = config('app.copyright');

        try {
            return $this->twig->render($template, $data);

        } catch(LoaderError|SyntaxError|RuntimeError $e) {
            $this->error($e);
        }
    }

    #[NoReturn] private function error(Throwable $e): void
    {
        $this->logger->error($e->getMessage() . "\nStacktrace:\n" .$e->getTraceAsString());

        $error = '';
        if (
            config('app.env') === 'local'
            && config('app.debug', false) === true
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
        <h1>HTTP 500 - Internal Server Error</h1>
        $error
    </body>
</html>
HTML;

        echo $html;
        exit;
    }
}