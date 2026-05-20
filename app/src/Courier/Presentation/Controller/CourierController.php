<?php

declare(strict_types=1);

namespace App\Courier\Presentation\Controller;

use App\Courier\Application\DTO\CourierData;
use App\Courier\Application\UseCase\CreateCourierUseCase;
use App\Courier\Application\UseCase\GetCourierUseCase;
use App\Courier\Application\UseCase\ListCouriersUseCase;
use App\Courier\Presentation\Request\CreateCourierRequest;
use App\Courier\Presentation\Response\CourierResponse;
use App\Shared\Application\DTO\PaginationQuery;
use App\Shared\Application\Service\ApiResponseFactory;
use App\Shared\Application\Service\RequestValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/couriers')]
final class CourierController
{
    public function __construct(
        private readonly ApiResponseFactory $responseFactory,
        private readonly RequestValidator $requestValidator,
        private readonly CreateCourierUseCase $createCourierUseCase,
        private readonly GetCourierUseCase $getCourierUseCase,
        private readonly ListCouriersUseCase $listCouriersUseCase,
    ) {
    }

    #[Route('', name: 'courier_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $payload = $request->toArray();
        $dto = new CreateCourierRequest();
        $dto->fullName = (string) ($payload['fullName'] ?? '');
        $dto->phone = isset($payload['phone']) ? (string) $payload['phone'] : null;
        $dto->active = (bool) ($payload['active'] ?? true);

        $this->requestValidator->validate($dto);

        $courier = $this->createCourierUseCase->execute(
            new CourierData($dto->fullName, $dto->phone, $dto->active)
        );

        return $this->responseFactory->success(CourierResponse::fromEntity($courier), null, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'courier_show', methods: ['GET'])]
    public function show(string $id): Response
    {
        $courier = $this->getCourierUseCase->execute($id);

        return $this->responseFactory->success(CourierResponse::fromEntity($courier));
    }

    #[Route('', name: 'courier_list', methods: ['GET'])]
    public function list(Request $request): Response
    {
        $query = new PaginationQuery(
            max(1, $request->query->getInt('page', 1)),
            min(100, max(1, $request->query->getInt('perPage', 15)))
        );

        $result = $this->listCouriersUseCase->execute($query->page, $query->perPage);

        return $this->responseFactory->success(CourierResponse::collection($result));
    }
}
