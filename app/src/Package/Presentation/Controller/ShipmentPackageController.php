<?php

declare(strict_types=1);

namespace App\Package\Presentation\Controller;

use App\Package\Application\DTO\ShipmentPackageData;
use App\Package\Application\UseCase\CreateShipmentPackageUseCase;
use App\Package\Application\UseCase\GetShipmentPackageUseCase;
use App\Package\Application\UseCase\ListShipmentPackagesUseCase;
use App\Package\Presentation\Request\CreateShipmentPackageRequest;
use App\Package\Presentation\Response\ShipmentPackageResponse;
use App\Shared\Application\DTO\PaginationQuery;
use App\Shared\Application\Service\ApiResponseFactory;
use App\Shared\Application\Service\RequestValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/packages')]
final class ShipmentPackageController
{
    public function __construct(
        private readonly ApiResponseFactory $responseFactory,
        private readonly RequestValidator $requestValidator,
        private readonly CreateShipmentPackageUseCase $createShipmentPackageUseCase,
        private readonly GetShipmentPackageUseCase $getShipmentPackageUseCase,
        private readonly ListShipmentPackagesUseCase $listShipmentPackagesUseCase,
    ) {
    }

    #[Route('', name: 'package_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $payload = $request->toArray();
        $dto = new CreateShipmentPackageRequest();
        $dto->trackingCode = (string) ($payload['trackingCode'] ?? '');
        $dto->recipientName = (string) ($payload['recipientName'] ?? '');
        $dto->recipientAddress = (string) ($payload['recipientAddress'] ?? '');
        $dto->weightKg = (float) ($payload['weightKg'] ?? 0);
        $dto->description = isset($payload['description']) ? (string) $payload['description'] : null;

        $this->requestValidator->validate($dto);

        $package = $this->createShipmentPackageUseCase->execute(
            new ShipmentPackageData(
                $dto->trackingCode,
                $dto->recipientName,
                $dto->recipientAddress,
                $dto->weightKg,
                $dto->description
            )
        );

        return $this->responseFactory->success(ShipmentPackageResponse::fromEntity($package), null, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'package_show', methods: ['GET'])]
    public function show(string $id): Response
    {
        $package = $this->getShipmentPackageUseCase->execute($id);

        return $this->responseFactory->success(ShipmentPackageResponse::fromEntity($package));
    }

    #[Route('', name: 'package_list', methods: ['GET'])]
    public function list(Request $request): Response
    {
        $query = new PaginationQuery(
            max(1, $request->query->getInt('page', 1)),
            min(100, max(1, $request->query->getInt('perPage', 15)))
        );

        $result = $this->listShipmentPackagesUseCase->execute($query->page, $query->perPage);

        return $this->responseFactory->success(ShipmentPackageResponse::collection($result));
    }
}
