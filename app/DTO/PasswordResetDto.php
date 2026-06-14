<?php
/*
 * Project:     Beacon
 * File:        PasswordResetDto.php
 * Date:        2026-06-14
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class PasswordResetDto
{
    public function __construct(
        #[Assert\NotBlank]
        public string $token,

        #[Assert\Regex(
            pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z0-9]).{8,}$/',
            message: 'Password does not follow the password policy.'
        )]
        public string $password,

        #[Assert\NotBlank]
        public string $confirm_password,
    ) { }

    /**
     * Password match validation
     *
     * @param ExecutionContextInterface $context
     *
     * @return void
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    #[Assert\Callback]
    public function validatePasswordsMatch(
        ExecutionContextInterface $context,
    ): void {
        if ($this->password !== $this->confirm_password) {
            $context
                ->buildViolation('Passwords do not match.')
                ->atPath('confirm_password')
                ->addViolation();
        }
    }

    /**
     * @param array $data
     *
     * @return self
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public static function fromArray(array $data): self
    {
        return new self(
            token: $data['token'] ?? '',
            password: $data['password'] ?? '',
            confirm_password: $data['confirm_password'] ?? '',
        );
    }

    /**
     * @return array
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function toArray(): array
    {
        return [
            'token' => $this->token,
            'password' => $this->password,
            'confirm_password' => $this->confirm_password,
        ];
    }
}
