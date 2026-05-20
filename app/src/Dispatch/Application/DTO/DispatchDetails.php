<?php

declare(strict_types=1);

namespace App\Dispatch\Application\DTO;

use App\Dispatch\Domain\Entity\Dispatch;
use App\Dispatch\Domain\Entity\DispatchHistory;

final class DispatchDetails
{
    /**
     * @param DispatchHistory[] $history
     */
    public function __construct(
        public readonly Dispatch $dispatch,
        public readonly array $history,
    ) {
    }
}
