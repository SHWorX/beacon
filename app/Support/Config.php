<?php
/*
 * Project:     Beacon
 * File:        Config.php
 * Date:        2026-06-09
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Support;

final readonly class Config
{
    public function __construct(
        private array $config = [],
    ) { }

    /**
     * @param string $key
     * @param mixed|null $default
     *
     * @return mixed
     * @author Steffen Haase <shworx.development@gmail.com
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $segments = explode('.', $key);
        $config = $this->config;

        foreach ($segments as $segment) {
            if (
                !is_array($config)
                || !array_key_exists($segment, $config)
            ) {
                return $default;
            }

            $config = $config[$segment];
        }

        return $config;
    }
}