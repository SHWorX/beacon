<?php
/*
 * Project:     Beacon
 * File:        setup.php
 * Date:        2026-06-15
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

$directories = [
    'storage/logs',
    'storage/sessions',
    'storage/cache',
];

foreach ($directories as $directory) {
    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }

    chmod($directory, 0755);
}

echo "Storage directories initialized.\n";