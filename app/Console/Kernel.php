<?php
/*
 * Project:     Beacon
 * File:        Kernel.php
 * Date:        2026-06-12
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Console;

use App\Container\Container;
use ReflectionException;

final readonly class Kernel
{
    public function __construct(
        private Container $container,
    ) { }

    /**
     * @throws ReflectionException
     */
    public function handle(array $argv): int
    {
        $commands = require config_path('commands.php');
        $name = $argv[1] ?? 'list';

        if ($name === 'list') {
            $this->showCommands($commands);
            return 0;
        }

        foreach ($commands as $commandClass) {
            $command = $this->container->make($commandClass);

            if ($command->name() === $name) {
                return $command->run(
                    array_slice($argv, 2)
                );
            }
        }

        echo "Unknown command: {$name}" . PHP_EOL . PHP_EOL;
        $this->showCommands($commands);

        return 1;
    }

    /**
     * Shows all existing and registered commands
     *
     * @param array $commands
     *
     * @return void
     * @throws ReflectionException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    private function showCommands(array $commands): void
    {
        echo PHP_EOL . 'Available commands:' . PHP_EOL . PHP_EOL;

        printf(
            "  %-70s %s\n",
            'list',
            'List all commands'
        );

        usort(
            $commands,
            fn (string $a, string $b) =>
                strcmp(
                    $this->container->make($a)->signature(),
                    $this->container->make($b)->signature()
                )
        );

        foreach ($commands as $commandClass) {
            $command = $this->container->make($commandClass);

            printf(
                "  %-70s %s\n",
                $command->signature(),
                $command->description()
            );
        }

        echo PHP_EOL;
    }
}