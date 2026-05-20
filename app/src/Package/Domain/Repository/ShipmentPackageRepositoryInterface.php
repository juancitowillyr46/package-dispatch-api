<?php

declare(strict_types=1);

namespace App\Package\Domain\Repository;

use App\Package\Domain\Entity\ShipmentPackage;
use App\Shared\Application\DTO\PaginatedResult;

interface ShipmentPackageRepositoryInterface
{
    public function save(ShipmentPackage $package): void;

    public function find(string $id): ?ShipmentPackage;

    public function paginate(int $page, int $perPage): PaginatedResult;
}
