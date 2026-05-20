<?php

declare(strict_types=1);

namespace App\Courier\Application\UseCase;

use App\Courier\Domain\Repository\CourierRepositoryInterface;
use App\Shared\Application\DTO\PaginatedResult;

final class ListCouriersUseCase
{
    public function __construct(private readonly CourierRepositoryInterface $courierRepository)
    {
    }

    public function execute(int $page, int $perPage): PaginatedResult
    {
        return $this->courierRepository->paginate($page, $perPage);
    }
}
