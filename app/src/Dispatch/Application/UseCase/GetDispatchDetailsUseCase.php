<?php

declare(strict_types=1);

namespace App\Dispatch\Application\UseCase;

use App\Dispatch\Application\DTO\DispatchDetails;
use App\Dispatch\Domain\Repository\DispatchHistoryRepositoryInterface;
use App\Dispatch\Domain\Repository\DispatchRepositoryInterface;
use App\Shared\Domain\Exception\NotFoundException;

final class GetDispatchDetailsUseCase
{
    public function __construct(
        private readonly DispatchRepositoryInterface $dispatchRepository,
        private readonly DispatchHistoryRepositoryInterface $historyRepository,
    ) {
    }

    public function execute(string $id): DispatchDetails
    {
        $dispatch = $this->dispatchRepository->find($id);

        if (null === $dispatch) {
            throw new NotFoundException('Dispatch not found');
        }

        return new DispatchDetails(
            $dispatch,
            $this->historyRepository->findByDispatchId($id)
        );
    }
}
