<?php

declare(strict_types=1);

namespace App\Dispatch\Application\UseCase;

use App\Dispatch\Application\DTO\DispatchStatusData;
use App\Dispatch\Domain\Entity\Dispatch;
use App\Dispatch\Domain\Entity\DispatchHistory;
use App\Dispatch\Domain\Enum\DispatchStatus;
use App\Dispatch\Domain\Repository\DispatchHistoryRepositoryInterface;
use App\Dispatch\Domain\Repository\DispatchRepositoryInterface;
use App\Shared\Domain\Exception\ConflictException;
use App\Shared\Domain\Exception\NotFoundException;
use App\Shared\Domain\Service\TransactionManagerInterface;

final class UpdateDispatchStatusUseCase
{
    public function __construct(
        private readonly TransactionManagerInterface $transactionManager,
        private readonly DispatchRepositoryInterface $dispatchRepository,
        private readonly DispatchHistoryRepositoryInterface $historyRepository,
    ) {
    }

    public function execute(DispatchStatusData $input): Dispatch
    {
        return $this->transactionManager->run(function () use ($input) {
            $dispatch = $this->dispatchRepository->lock($input->dispatchId);
            if (null === $dispatch) {
                throw new NotFoundException('Dispatch not found');
            }

            $previousStatus = $dispatch->getStatus()->value;

            if (!$dispatch->getStatus()->canTransitionTo($input->status)) {
                throw new ConflictException('Invalid dispatch status transition');
            }

            match ($input->status) {
                DispatchStatus::InTransit => $dispatch->markInTransit(new \DateTimeImmutable()),
                DispatchStatus::Delivered => $dispatch->markDelivered(new \DateTimeImmutable()),
                default => throw new ConflictException('Unsupported target status'),
            };

            $this->dispatchRepository->save($dispatch);
            $this->historyRepository->save(new DispatchHistory($dispatch, $previousStatus, $dispatch->getStatus()->value));

            return $dispatch;
        });
    }
}
