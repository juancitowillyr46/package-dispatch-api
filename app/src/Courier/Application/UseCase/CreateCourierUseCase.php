<?php

declare(strict_types=1);

namespace App\Courier\Application\UseCase;

use App\Courier\Application\DTO\CourierData;
use App\Courier\Domain\Entity\Courier;
use App\Courier\Domain\Repository\CourierRepositoryInterface;

final class CreateCourierUseCase
{
    public function __construct(private readonly CourierRepositoryInterface $courierRepository)
    {
    }

    public function execute(CourierData $input): Courier
    {
        $courier = new Courier($input->fullName, $input->phone, $input->active);
        $this->courierRepository->save($courier);

        return $courier;
    }
}
