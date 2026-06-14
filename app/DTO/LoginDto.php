<?php
/*
 * Project:     Beacon
 * File:        LoginDto.php
 * Date:        2026-06-10
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class LoginDto
{
    public function __construct(
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
//        #[Assert\Regex(
//            pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z0-9]).{8,}$/',
//            message: 'Password does not follow the password policy.'
//        )]
        public string $password,

        public bool $remember = false,
    ) { }

    /**
     * @param array $data
     *
     * @return self
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public static function fromArray(array $data): self
    {
        return new self(
            email: trim($data['email'] ?? ''),
            password: $data['password'] ?? '',
            remember: $data['remember'] ?? false,
        );
    }

    /**
     *
     * @return array
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
            'remember' => $this->remember,
        ];
    }
}