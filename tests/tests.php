<?php
/*
 * Project:     Beacon
 * File:        tests.php
 * Date:        2026-06-10
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

use Ramsey\Uuid\Uuid;

require_once __DIR__ . '/../vendor/autoload.php';

echo 'UUID v7: ' . Uuid::uuid7()->toString() . PHP_EOL;