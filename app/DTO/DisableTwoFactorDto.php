<?php
/*
 * Project:     beacon
 * File:        DisableTwoFactorDto.php
 * Date:        2026-07-02
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class DisableTwoFactorDto
{
    public function __construct(
        #[Assert\NotBlank]
        public string $password,
    ) {}

    /**
     * @param array $data
     *
     * @return self
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public static function fromArray(array $data): self
    {
        return new self(
            password: $data['password'] ?? '',
        );
    }

    /**
     * @return array
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function toArray(): array
    {
        return [
            'password' => $this->password,
        ];
    }
}
