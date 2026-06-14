<?php
/*
 * Project:     Beacon
 * File:        session.php
 * Date:        2026-06-02
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

session_name('APPSESSID');

session_save_path(storage_path('sessions'));

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['_flash'])) {
    $_SESSION['_flash'] = [];
}

if (!isset($_SESSION['_flash_new'])) {
    $_SESSION['_flash_new'] = [];
}

if (!isset($_SESSION['_flash_old'])) {
    $_SESSION['_flash_old'] = [];
}

foreach ($_SESSION['_flash_old'] as $key) {
    unset($_SESSION['_flash'][$key]);
}

$_SESSION['_flash_old'] = $_SESSION['_flash_new'];
$_SESSION['_flash_new'] = [];
