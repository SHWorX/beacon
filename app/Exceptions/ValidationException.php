<?php
/*
 * Project:     Beacon
 * File:        ValidationException.php
 * Date:        2026-06-02
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Exceptions;

use Exception;

class ValidationException extends Exception implements ExceptionInterface
{
    public function __construct(
        private readonly array $errors,
        private readonly object $dto
    ) {
        parent::__construct('Validation failed');
    }

    /**
     * Returns the validation errors
     *
     * @return array
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Returns the validated DTO
     *
     * @return object
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function dto(): object
    {
        return $this->dto;
    }
}