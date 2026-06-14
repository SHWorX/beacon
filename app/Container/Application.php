<?php
/*
 * Project:     Beacon
 * File:        Application.php
 * Date:        2026-06-09
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Container;

use RuntimeException;

final class Application
{
    public static ?Container $container = null;

    /**
     * @param Container $container
     *
     * @return void
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public static function setContainer(Container $container): void
    {
        self::$container = $container;
    }

    /**
     * @return Container
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public static function container(): Container
    {
        if (self::$container === null) {
            throw new RuntimeException('Application not bootstrapped.');
        }

        return self::$container;
    }
}