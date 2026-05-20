<?php

declare(strict_types=1);

namespace App\Dispatch\Application\UseCase;

use App\Dispatch\Domain\Repository\DispatchRepositoryInterface;
use App\Shared\Application\DTO\PaginatedResult;

final class ListDispatchesUseCase
{
    public function __construct(private readonly DispatchRepositoryInterface $dispatchRepository)
    {
    }

    public function execute(int $page, int $perPage): PaginatedResult
    {
        return $this->dispatchRepository->paginate($page, $perPage);
    }
}
