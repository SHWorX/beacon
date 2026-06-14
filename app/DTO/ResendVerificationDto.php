<?php
/*
 * Project:     Beacon
 * File:        ResendVerificationDto.php
 * Date:        2026-06-13
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\DTO;

use App\Models\User;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ResendVerificationDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email,
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
        );
    }

    /**
     * @return array
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function toArray(): array
    {
        return [
            'email' => $this->email,
        ];
    }
}
