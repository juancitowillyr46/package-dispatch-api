<?php

declare(strict_types=1);

namespace App\Dispatch\Presentation\Response;

use App\Dispatch\Domain\Entity\Dispatch;
use App\Shared\Application\DTO\PaginatedResult;

final class DispatchResponse
{
    public static function fromEntity(Dispatch $dispatch, array $history = []): array
    {
        return [
            'id' => $dispatch->getId(),
            'referenceNumber' => $dispatch->getReferenceNumber(),
            'status' => $dispatch->getStatus()->value,
            'originAddress' => $dispatch->getOriginAddress(),
            'destinationAddress' => $dispatch->getDestinationAddress(),
            'notes' => $dispatch->getNotes(),
            'assignedAt' => $dispatch->getAssignedAt()?->format(DATE_ATOM),
            'pickedUpAt' => $dispatch->getPickedUpAt()?->format(DATE_ATOM),
            'deliveredAt' => $dispatch->getDeliveredAt()?->format(DATE_ATOM),
            'package' => [
                'id' => $dispatch->getPackage()->getId(),
                'trackingCode' => $dispatch->getPackage()->getTrackingCode(),
                'recipientName' => $dispatch->getPackage()->getRecipientName(),
            ],
            'courier' => $dispatch->getCourier() ? [
                'id' => $dispatch->getCourier()->getId(),
                'fullName' => $dispatch->getCourier()->getFullName(),
            ] : null,
            'history' => array_map(
                static fn ($item): array => DispatchHistoryResponse::fromEntity($item),
                $history
            ),
            'createdAt' => $dispatch->getCreatedAt()->format(DATE_ATOM),
            'updatedAt' => $dispatch->getUpdatedAt()->format(DATE_ATOM),
        ];
    }

    public static function collection(PaginatedResult $result): array
    {
        return [
            'items' => array_map(
                static fn (Dispatch $dispatch): array => self::fromEntity($dispatch),
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
