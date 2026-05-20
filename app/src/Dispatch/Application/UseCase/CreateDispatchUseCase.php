<?php

declare(strict_types=1);

namespace App\Dispatch\Application\UseCase;

use App\Dispatch\Application\DTO\DispatchData;
use App\Dispatch\Domain\Entity\Dispatch;
use App\Dispatch\Domain\Entity\DispatchHistory;
use App\Dispatch\Domain\Repository\DispatchHistoryRepositoryInterface;
use App\Dispatch\Domain\Repository\DispatchRepositoryInterface;
use App\Package\Domain\Repository\ShipmentPackageRepositoryInterface;
use App\Shared\Domain\Exception\ConflictException;
use App\Shared\Domain\Exception\NotFoundException;
use App\Shared\Domain\Service\TransactionManagerInterface;

final class CreateDispatchUseCase
{
    public function __construct(
        private readonly TransactionManagerInterface $transactionManager,
        private readonly ShipmentPackageRepositoryInterface $packageRepository,
        private readonly DispatchRepositoryInterface $dispatchRepository,
        private readonly DispatchHistoryRepositoryInterface $historyRepository,
    ) {
    }

    public function execute(DispatchData $input): Dispatch
    {
        return $this->transactionManager->run(function () use ($input): Dispatch {
            $package = $this->packageRepository->find($input->packageId);
            if (null === $package) {
                throw new NotFoundException('Package not found');
            }

            if (null !== $this->dispatchRepository->findActiveByPackageId($package->getId())) {
                throw new ConflictException('This package already has an active dispatch');
            }

            $dispatch = new Dispatch(
                $package,
                $input->referenceNumber,
                $input->originAddress,
                $input->destinationAddress,
                $input->notes
            );

            $this->dispatchRepository->save($dispatch);
            $this->historyRepository->save(new DispatchHistory($dispatch, null, $dispatch->getStatus()->value));

            return $dispatch;
        });
    }
}
