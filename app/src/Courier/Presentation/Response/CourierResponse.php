<?php

declare(strict_types=1);

namespace App\Courier\Presentation\Response;

use App\Courier\Domain\Entity\Courier;
use App\Shared\Application\DTO\PaginatedResult;

final class CourierResponse
{
    public static function fromEntity(Courier $courier): array
    {
        return [
            'id' => $courier->getId(),
            'fullName' => $courier->getFullName(),
            'phone' => $courier->getPhone(),
            'active' => $courier->isActive(),
            'createdAt' => $courier->getCreatedAt()->format(DATE_ATOM),
            'updatedAt' => $courier->getUpdatedAt()->format(DATE_ATOM),
        ];
    }

    public static function collection(PaginatedResult $result): array
    {
        return [
            'items' => array_map(
                static fn (Courier $courier): array => self::fromEntity($courier),
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
