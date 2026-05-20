<?php

declare(strict_types=1);

namespace App\Dispatch\Application\UseCase;

use App\Courier\Domain\Repository\CourierRepositoryInterface;
use App\Dispatch\Application\DTO\CourierAssignmentData;
use App\Dispatch\Domain\Entity\Dispatch;
use App\Dispatch\Domain\Entity\DispatchHistory;
use App\Dispatch\Domain\Enum\DispatchStatus;
use App\Dispatch\Domain\Repository\DispatchHistoryRepositoryInterface;
use App\Dispatch\Domain\Repository\DispatchRepositoryInterface;
use App\Shared\Domain\Exception\ConflictException;
use App\Shared\Domain\Exception\NotFoundException;
use App\Shared\Domain\Service\TransactionManagerInterface;

final class AssignCourierToDispatchUseCase
{
    public function __construct(
        private readonly TransactionManagerInterface $transactionManager,
        private readonly DispatchRepositoryInterface $dispatchRepository,
        private readonly CourierRepositoryInterface $courierRepository,
        private readonly DispatchHistoryRepositoryInterface $historyRepository,
    ) {
    }

    public function execute(CourierAssignmentData $input): Dispatch
    {
        return $this->transactionManager->run(function () use ($input) {
            $dispatch = $this->dispatchRepository->lock($input->dispatchId);
            if (null === $dispatch) {
                throw new NotFoundException('Dispatch not found');
            }

            $courier = $this->courierRepository->lock($input->courierId);
            if (null === $courier) {
                throw new NotFoundException('Courier not found');
            }

            if (!$courier->isActive()) {
                throw new ConflictException('Courier is not active');
            }

            if (null !== $this->dispatchRepository->findActiveByCourierId($courier->getId())) {
                throw new ConflictException('Courier already has an active dispatch');
            }

            $previousStatus = $dispatch->getStatus()->value;
            if ($dispatch->getStatus() !== DispatchStatus::Pending) {
                throw new ConflictException('Only pending dispatches can be assigned');
            }

            $dispatch->assignCourier($courier, new \DateTimeImmutable());
            $this->dispatchRepository->save($dispatch);
            $this->historyRepository->save(new DispatchHistory($dispatch, $previousStatus, $dispatch->getStatus()->value));

            return $dispatch;
        });
    }
}
