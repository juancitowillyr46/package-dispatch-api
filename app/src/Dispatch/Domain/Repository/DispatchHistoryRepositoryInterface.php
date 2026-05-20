<?php

declare(strict_types=1);

namespace App\Dispatch\Domain\Repository;

use App\Dispatch\Domain\Entity\DispatchHistory;

interface DispatchHistoryRepositoryInterface
{
    public function save(DispatchHistory $history): void;

    /**
     * @return DispatchHistory[]
     */
    public function findByDispatchId(string $dispatchId): array;
}
