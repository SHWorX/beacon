<?php
/*
 * Project:     beacon
 * File:        ChangePasswordDto.php
 * Date:        2026-06-28
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\DTO;

use App\Models\User;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ChangePasswordDto
{
    public function __construct(
        public string $password_current,
//        #[Assert\Length(min: 8)]
//        #[Assert\Regex(
//            pattern: '/[a-z]/',
//            message: 'Password must contain at least one lowercase letter.'
//        )]
//        #[Assert\Regex(
//            pattern: '/[A-Z]/',
//            message: 'Password must contain at least one uppercase letter.'
//        )]
//        #[Assert\Regex(
//            pattern: '/[0-9]/',
//            message: 'Password must contain at least one number.'
//        )]
//        #[Assert\Regex(
//            pattern: '/[^a-zA-Z0-9]/',
//            message: 'Password must contain at least one special character.'
//        )]
        #[Assert\Regex(
            pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z0-9]).{8,}$/',
            message: 'Password does not follow the password policy.'
        )]
        public string $password,

        #[Assert\NotBlank]
        public string $password_confirm,
    ) { }

    /**
     * Passwords validation
     *
     * @param ExecutionContextInterface $context
     *
     * @return void
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    #[Assert\Callback]
    public function validatePasswords(
        ExecutionContextInterface $context,
    ): void {
        if ($this->password_current === $this->password) {
            $context
                ->buildViolation('The new password cannot be same as your current password.')
                ->atPath('password')
                ->addViolation();
        }

        if ($this->password !== $this->password_confirm) {
            $context
                ->buildViolation('Passwords do not match.')
                ->atPath('password_confirm')
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
            password_current: $data['password_current'] ?? '',
            password: $data['password'] ?? '',
            password_confirm: $data['password_confirm'] ?? '',
        );
    }

    /**
     * @return array
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function toArray(): array
    {
        return [
            'password_current' => $this->password_current,
            'password' => $this->password,
            'password_confirm' => $this->password_confirm,
        ];
    }

}