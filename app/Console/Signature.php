<?php
/*
 * Project:     Beacon
 * File:        Signature.php
 * Date:        2026-06-12
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Console;

final class Signature
{
    public string $name;

    /** @var array<string,int> */
    public array $arguments = [];

    /** @var array<string,bool> */
    public array $options = [];

    public function __construct(string $signature)
    {
        preg_match_all('/\{([^}]+)\}/', $signature, $matches);
        $this->name = trim(preg_replace('/\{[^}]+\}/', '', $signature));

        foreach ($matches[1] as $part) {
            if (str_starts_with($part, '--')) {
                $part = substr($part, 2);

                if (str_ends_with($part, '=')) {
                    $part = rtrim($part, '=');
                }

                $this->options[$part] = true;
                continue;
            }

            $this->arguments[$part] = count($this->arguments);
        }
    }
}
