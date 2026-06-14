<?php
/*
 * Project:     Beacon
 * File:        LoggingServiceProvider.php
 * Date:        2026-06-09
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Providers;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

final readonly class LoggingServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->container->singleton(
            LoggerInterface::class,
            function () {
                $logLevel = match (config('app.logLevel')) {
                    'debug' => Level::Debug,
                    'info' => Level::Info,
                    'notice' => Level::Notice,
                    'warning' => Level::Warning,
                    'critical' => Level::Critical,
                    'alert' => Level::Alert,
                    'emergency' => Level::Emergency,
                    default => Level::Error,
                };
                $logger = new Logger('app');
                $handler = new StreamHandler(storage_path('logs/app.log'), $logLevel);
                $handler->setFormatter(new LineFormatter(
                    null, // default format
                    null, // default date format
                    true,
                    true
                ));

                $logger->pushHandler($handler);

                return $logger;
            }
        );
    }
}