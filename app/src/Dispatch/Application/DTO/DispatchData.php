<?php

declare(strict_types=1);

namespace App\Dispatch\Application\DTO;

final class DispatchData
{
    public function __construct(
        public readonly string $packageId,
        public readonly string $referenceNumber,
        public readonly string $originAddress,
        public readonly string $destinationAddress,
        public readonly ?string $notes = null,
    ) {
    }
}
