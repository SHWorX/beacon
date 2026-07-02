<?php
/*
 * Project:     beacon
 * File:        EnableTwoFactorDto.php
 * Date:        2026-07-02
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class EnableTwoFactorDto
{
    public function __construct(
        #[Assert\NotNull]
        public string $setup_id,

        #[Assert\NotBlank]
        #[Assert\Length(min: 6, max: 6)]
        public string $code,
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
            setup_id: $data['setup_id'] ?? '',
            code: $data['code'] ?? '',
        );
    }

    /**
     * @return array
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function toArray(): array
    {
        return [
            'setup_id' => $this->setup_id,
            'code' => $this->code,
        ];
    }
}
