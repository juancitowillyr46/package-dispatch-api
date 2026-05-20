<?php

declare(strict_types=1);

namespace App\Dispatch\Presentation\Response;

use App\Dispatch\Domain\Entity\DispatchHistory;

final class DispatchHistoryResponse
{
    public static function fromEntity(DispatchHistory $history): array
    {
        return [
            'id' => $history->getId(),
            'previousStatus' => $history->getPreviousStatus(),
            'newStatus' => $history->getNewStatus(),
            'changedAt' => $history->getChangedAt()->format(DATE_ATOM),
        ];
    }
}
