<?php
/*
 * Project:     Beacon
 * File:        database.php
 * Date:        2026-06-01
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

use App\Support\Env;

return [
    'driver'    => Env::get('DB_DRIVER', 'mysql'),
    'host'      => Env::get('DB_HOST', 'localhost'),
    'port'      => Env::get('DB_PORT', '3306'),
    'database'  => Env::get('DB_DATABASE'),
    'username'  => Env::get('DB_USERNAME'),
    'password'  => Env::get('DB_PASSWORD'),
    'charset'   => Env::get('DB_CHARSET', 'utf8mb4'),
    'collation' => Env::get('DB_COLLATION', 'utf8mb4_unicode_520_ci'),
    'prefix'    => Env::get('DB_PREFIX', ''),
];