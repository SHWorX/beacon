<?php
/*
 * Project:     Beacon
 * File:        tests.php
 * Date:        2026-06-10
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

$publicRoutes = [
    'auth/reset/*',
];

$routes = [
    'auth/reset',
    'auth/reset/xxx-xxx-xxx-xxx',
    'auth/reset/',
];

function normalizeRoute(string $path): string {
    if ($path !== '/') {
        return rtrim($path, '/');
    }
    return $path;
}

foreach ($routes as $route) {
    $route = normalizeRoute($route);
    if (array_any($publicRoutes, fn($pattern) => fnmatch($pattern, $route))) {
        echo $route . PHP_EOL;
    }
}