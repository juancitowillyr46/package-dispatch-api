<?php

declare(strict_types=1);

namespace App\Courier\Domain\Repository;

use App\Courier\Domain\Entity\Courier;
use App\Shared\Application\DTO\PaginatedResult;

interface CourierRepositoryInterface
{
    public function save(Courier $courier): void;

    public function find(string $id): ?Courier;

    public function lock(string $id): ?Courier;

    public function paginate(int $page, int $perPage): PaginatedResult;
}
