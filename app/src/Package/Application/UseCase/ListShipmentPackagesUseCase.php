<?php

declare(strict_types=1);

namespace App\Package\Application\UseCase;

use App\Package\Domain\Repository\ShipmentPackageRepositoryInterface;
use App\Shared\Application\DTO\PaginatedResult;

final class ListShipmentPackagesUseCase
{
    public function __construct(private readonly ShipmentPackageRepositoryInterface $packageRepository)
    {
    }

    public function execute(int $page, int $perPage): PaginatedResult
    {
        return $this->packageRepository->paginate($page, $perPage);
    }
}
