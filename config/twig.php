<?php
/*
 * Project:     Beacon
 * File:        twig.php
 * Date:        2026-06-03
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

use App\Support\Env;

return [
    'cache' => Env::bool('TWIG_CACHE'),
    'debug' => Env::bool('TWIG_DEBUG'),
];