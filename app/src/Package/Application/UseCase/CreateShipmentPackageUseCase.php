<?php

declare(strict_types=1);

namespace App\Package\Application\UseCase;

use App\Package\Application\DTO\ShipmentPackageData;
use App\Package\Domain\Entity\ShipmentPackage;
use App\Package\Domain\Repository\ShipmentPackageRepositoryInterface;

final class CreateShipmentPackageUseCase
{
    public function __construct(private readonly ShipmentPackageRepositoryInterface $packageRepository)
    {
    }

    public function execute(ShipmentPackageData $input): ShipmentPackage
    {
        $package = new ShipmentPackage(
            $input->trackingCode,
            $input->recipientName,
            $input->recipientAddress,
            $input->weightKg,
            $input->description
        );

        $this->packageRepository->save($package);

        return $package;
    }
}
