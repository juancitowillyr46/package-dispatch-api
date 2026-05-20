<?php

declare(strict_types=1);

namespace App\Courier\Application\DTO;

final class CourierData
{
    public function __construct(
        public readonly string $fullName,
        public readonly ?string $phone = null,
        public readonly bool $active = true,
    ) {
    }
}
