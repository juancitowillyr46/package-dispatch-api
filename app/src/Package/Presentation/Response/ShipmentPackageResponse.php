<?php

declare(strict_types=1);

namespace App\Package\Presentation\Response;

use App\Package\Domain\Entity\ShipmentPackage;
use App\Shared\Application\DTO\PaginatedResult;

final class ShipmentPackageResponse
{
    public static function fromEntity(ShipmentPackage $package): array
    {
        return [
            'id' => $package->getId(),
            'trackingCode' => $package->getTrackingCode(),
            'recipientName' => $package->getRecipientName(),
            'recipientAddress' => $package->getRecipientAddress(),
            'weightKg' => $package->getWeightKg(),
            'description' => $package->getDescription(),
            'createdAt' => $package->getCreatedAt()->format(DATE_ATOM),
            'updatedAt' => $package->getUpdatedAt()->format(DATE_ATOM),
        ];
    }

    public static function collection(PaginatedResult $result): array
    {
        return [
            'items' => array_map(
                static fn (ShipmentPackage $package): array => self::fromEntity($package),
                $result->getItems()
            ),
            'pagination' => [
                'page' => $result->getPage(),
                'perPage' => $result->getPerPage(),
                'total' => $result->getTotal(),
                'totalPages' => $result->getTotalPages(),
            ],
        ];
    }
}
