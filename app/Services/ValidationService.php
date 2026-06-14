<?php
/*
 * Project:     Beacon
 * File:        ValidationService.php
 * Date:        2026-06-01
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Services;

use App\Exceptions\ValidationException;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationService
{
    private ValidatorInterface $validator;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
    }

    /**
     * Validate the given Data Transfer Object
     *
     * @param object $dto Data Transfer Object (DTO)
     *
     * @return void
     * @throws ValidationException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function validate(object $dto): void
    {
        $errors = [];

        $violations = $this->validator->validate($dto);
        foreach ($violations as $violation) {
            $field = $violation->getPropertyPath();
            $field = str_replace(['[', ']'], '', $field);
            $field = str_replace(['.', '->'], '_', $field);

            $errors[$field][] = $violation->getMessage();
        }

        if ($errors !== []) {
            throw new ValidationException($errors, $dto);
        }
    }
}