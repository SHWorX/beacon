<?php
/*
 * Project:     Beacon
 * File:        Command.php
 * Date:        2026-06-11
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Console;

abstract class Command
{
    /** @var string Command name */
    protected string $signature;
    /** @var string Command description */
    protected string $description;

    protected array $arguments = [];
    protected array $options = [];

    private Signature $definition;

    abstract public function handle(): int;

    final public function run(array $input = []): int
    {
        $this->definition = new Signature($this->signature);
        $this->parseInput($input);

        return $this->handle();
    }

    /**
     * Parses the command input (arguments + options)
     *
     * @param array $input
     *
     * @return void
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    protected function parseInput(array $input): void
    {
        $this->arguments = [];
        $this->options = [];

        foreach ($input as $item) {
            if (!str_starts_with($item, '--')) {
                $this->arguments[] = $item;
                continue;
            }

            $item = substr($item, 2);
            if (str_contains($item, '=')) {
                [$name, $value] = explode('=', $item, 2);
                $this->options[$name] = empty($value) ? null : $value;
                continue;
            }

            $this->options[$item] = true;
        }
    }

    /**
     * Returns all arguments
     *
     * @return array
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    protected function arguments(): array
    {
        return $this->arguments;
    }

    /**
     * Returns an argument
     *
     * @param string $name Argument name
     * @param mixed|null $default [optional] Default value
     *
     * @return string
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    protected function argument(string $name, mixed $default = null): mixed
    {
        $index = $this->definition->arguments[$name] ?? null;
        if ($index === null) {
            return $default;
        }

        return $this->arguments[$index] ?? $default;
    }

    /**
     * Returns the value of an option
     *
     * Returns `null` if option does not exist.<br>
     * Returns `true` if option is passed without a value (e.g. `--my-option`).
     * In all other cases it will return the value of the option (e.g. `--my-option=value`),<br>
     * or the default value.
     *
     * @param string $name Option name (without --)
     * @param string|null $default [optional] Default value
     *
     * @return true|string|null
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    protected function option(string $name, ?string $default = null): true|string|null
    {
        return $this->options[$name] ?? $default;
    }

    /**
     * Checks if an option was passed to the command
     *
     * @param string $name Option name (without --)
     *
     * @return bool
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    protected function hasOption(string $name): bool
    {
        return array_key_exists($name, $this->options);
    }

    /**
     * Returns the command name
     *
     * @return string
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function name(): string
    {
        return new Signature($this->signature)->name;
    }

    /**
     * Returns the command name
     *
     * @return string
     * @author Steffen Haase <shworx.development@gmail.com
     */
    public function signature(): string
    {
        return $this->signature;
    }

    /**
     * Returns the command description
     *
     * @return string
     * @author Steffen Haase <shworx.development@gmail.com
     */
    public function description(): string
    {
        return $this->description;
    }


    /**
     * Prints a line
     *
     * @param string $message Message
     * @param bool $lineBreak [optional] Linebreak at the end (default: true)
     *
     * @return void
     * @author Steffen Haase <shworx.development@gmail.com
     */
    protected function line(string $message, bool $lineBreak = true): void
    {
        echo $message . ($lineBreak === true ? PHP_EOL : ' ');
    }

    /**
     * Print a "success" message
     *
     * @param string $message
     *
     * @return void
     * @author Steffen Haase <shworx.development@gmail.com
     */
    public function success(string $message): void
    {
        echo "\033[32m{$message}\033[0m" . PHP_EOL;
    }

    /**
     * Print a "error" message
     *
     * @param string $message
     *
     * @return void
     * @author Steffen Haase <shworx.development@gmail.com
     */
    protected function error(string $message): void
    {
        echo "\033[31m{$message}\033[0m" . PHP_EOL;
    }

    /**
     * Print a "warning" message
     *
     * @param string $message
     *
     * @return void
     * @author Steffen Haase <shworx.development@gmail.com
     */
    protected function warning(string $message): void
    {
        echo "\033[33m{$message}\033[0m" . PHP_EOL;
    }
}
