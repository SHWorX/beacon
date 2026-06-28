<?php
/*
 * Project:     beacon
 * File:        ChangeEmailDto.php
 * Date:        2026-06-28
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\DTO;

use App\Models\User;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ChangeEmailDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email,
    ) { }

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
