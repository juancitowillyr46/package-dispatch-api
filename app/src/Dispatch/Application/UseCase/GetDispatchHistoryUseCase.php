<?php

declare(strict_types=1);

namespace App\Dispatch\Application\UseCase;

use App\Dispatch\Domain\Entity\DispatchHistory;
use App\Dispatch\Domain\Repository\DispatchHistoryRepositoryInterface;
use App\Dispatch\Domain\Repository\DispatchRepositoryInterface;
use App\Shared\Domain\Exception\NotFoundException;

final class GetDispatchHistoryUseCase
{
    public function __construct(
        private readonly DispatchRepositoryInterface $dispatchRepository,
        private readonly DispatchHistoryRepositoryInterface $historyRepository,
    ) {
    }

    /**
     * @return DispatchHistory[]
     */
    public function execute(string $id): array
    {
        if (null === $this->dispatchRepository->find($id)) {
            throw new NotFoundException('Dispatch not found');
        }

        return $this->historyRepository->findByDispatchId($id);
    }
}
