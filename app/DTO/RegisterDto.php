<?php
/*
 * Project:     Beacon
 * File:        RegisterDto.php
 * Date:        2026-06-13
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\DTO;

use App\Models\User;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class RegisterDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 4, max: 100)]
        public string $username,

        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email,

        #[Assert\NotBlank]
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
     * Username exists validation
     *
     * @param ExecutionContextInterface $context
     *
     * @return void
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    #[Assert\Callback]
    public function validateUsername(
        ExecutionContextInterface $context,
    ): void {
        $user = User::query()->where('username', $this->username)->first();

        if ($user !== null) {
            $context
                ->buildViolation('Username already exists.')
                ->atPath('username')
                ->addViolation();
        }
    }

    /**
     * Email exists validation
     *
     * @param ExecutionContextInterface $context
     *
     * @return void
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    #[Assert\Callback]
    public function validateEmail(
        ExecutionContextInterface $context,
    ): void {
        $user = User::query()->where('email', $this->email)->first();

        if ($user !== null) {
            $context
                ->buildViolation('Email is already registered.')
                ->atPath('email')
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
            username: $data['username'],
            email: trim($data['email'] ?? ''),
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
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password,
            'confirm_password' => $this->confirm_password,
        ];
    }
}
