<?php

declare(strict_types=1);

namespace App\Dispatch\Domain\Repository;

use App\Dispatch\Domain\Entity\Dispatch;
use App\Shared\Application\DTO\PaginatedResult;

interface DispatchRepositoryInterface
{
    public function save(Dispatch $dispatch): void;

    public function find(string $id): ?Dispatch;

    public function lock(string $id): ?Dispatch;

    public function paginate(int $page, int $perPage): PaginatedResult;

    public function findActiveByCourierId(string $courierId): ?Dispatch;

    public function findActiveByPackageId(string $packageId): ?Dispatch;
}
