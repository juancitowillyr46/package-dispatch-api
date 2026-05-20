<?php

declare(strict_types=1);

namespace App\Courier\Application\UseCase;

use App\Courier\Domain\Entity\Courier;
use App\Courier\Domain\Repository\CourierRepositoryInterface;
use App\Shared\Domain\Exception\NotFoundException;

final class GetCourierUseCase
{
    public function __construct(private readonly CourierRepositoryInterface $courierRepository)
    {
    }

    public function execute(string $id): Courier
    {
        $courier = $this->courierRepository->find($id);

        if (null === $courier) {
            throw new NotFoundException('Courier not found');
        }

        return $courier;
    }
}
