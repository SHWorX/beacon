<?php
/*
 * Project:     Beacon
 * File:        app.php
 * Date:        2026-06-01
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

use App\Container\Application;
use App\Container\Container;
use App\Support\Config;
use Dotenv\Dotenv;

require_once dirname(__DIR__) . '/vendor/autoload.php';

// Initialize Vlucas Dotenv
$dotenv = Dotenv::createImmutable(base_path());
$dotenv->safeLoad();

// Create configuration array
$config = [
    'app' => require config_path('app.php'),
    'database' => require config_path('database.php'),
    'mailer' => require config_path('mailer.php'),
    'twig' => require config_path('twig.php'),
];

// Set timezone
date_default_timezone_set($config['app']['timezone'] ?? 'UTC');

##########################################################################
# S E R V I C E    R E G I S T R A T I O N S
##########################################################################
$container = new Container();
Application::setContainer($container);

$container->singleton(
    Config::class,
    fn () => new Config($config)
);

$providers = require config_path('providers.php');

/** @var array<\App\Providers\ServiceProvider> $instances */
$instances = [];

foreach ($providers as $providerClass) {
    $provider = new $providerClass($container);
    $provider->register();
    $instances[] = $provider;
}

foreach ($instances as $provider) {
    $provider->boot();
}

return $container;
