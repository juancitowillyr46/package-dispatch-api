<?php

declare(strict_types=1);

namespace App\Package\Application\DTO;

final class ShipmentPackageData
{
    public function __construct(
        public readonly string $trackingCode,
        public readonly string $recipientName,
        public readonly string $recipientAddress,
        public readonly float $weightKg,
        public readonly ?string $description = null,
    ) {
    }
}
