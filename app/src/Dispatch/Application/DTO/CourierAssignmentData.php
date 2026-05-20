<?php

declare(strict_types=1);

namespace App\Dispatch\Application\DTO;

final class CourierAssignmentData
{
    public function __construct(
        public readonly string $dispatchId,
        public readonly string $courierId,
    ) {
    }
}
