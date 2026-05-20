<?php

declare(strict_types=1);

namespace App\Dispatch\Application\DTO;

use App\Dispatch\Domain\Enum\DispatchStatus;

final class DispatchStatusData
{
    public function __construct(
        public readonly string $dispatchId,
        public readonly DispatchStatus $status,
    ) {
    }
}
