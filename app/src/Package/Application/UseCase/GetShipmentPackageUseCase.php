<?php

declare(strict_types=1);

namespace App\Package\Application\UseCase;

use App\Package\Domain\Entity\ShipmentPackage;
use App\Package\Domain\Repository\ShipmentPackageRepositoryInterface;
use App\Shared\Domain\Exception\NotFoundException;

final class GetShipmentPackageUseCase
{
    public function __construct(private readonly ShipmentPackageRepositoryInterface $packageRepository)
    {
    }

    public function execute(string $id): ShipmentPackage
    {
        $package = $this->packageRepository->find($id);

        if (null === $package) {
            throw new NotFoundException('Package not found');
        }

        return $package;
    }
}
