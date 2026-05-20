<?php

declare(strict_types=1);

namespace App\Dispatch\Presentation\Controller;

use App\Dispatch\Application\DTO\CourierAssignmentData;
use App\Dispatch\Application\DTO\DispatchData;
use App\Dispatch\Application\DTO\DispatchStatusData;
use App\Dispatch\Application\UseCase\AssignCourierToDispatchUseCase;
use App\Dispatch\Application\UseCase\CreateDispatchUseCase;
use App\Dispatch\Application\UseCase\GetDispatchDetailsUseCase;
use App\Dispatch\Application\UseCase\ListDispatchesUseCase;
use App\Dispatch\Application\UseCase\UpdateDispatchStatusUseCase;
use App\Dispatch\Domain\Enum\DispatchStatus;
use App\Dispatch\Presentation\Request\AssignCourierRequest;
use App\Dispatch\Presentation\Request\CreateDispatchRequest;
use App\Dispatch\Presentation\Request\UpdateDispatchStatusRequest;
use App\Dispatch\Presentation\Response\DispatchResponse;
use App\Shared\Application\DTO\PaginationQuery;
use App\Shared\Application\Service\ApiResponseFactory;
use App\Shared\Application\Service\RequestValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/dispatches')]
final class DispatchController
{
    public function __construct(
        private readonly ApiResponseFactory $responseFactory,
        private readonly RequestValidator $requestValidator,
        private readonly CreateDispatchUseCase $createDispatchUseCase,
        private readonly AssignCourierToDispatchUseCase $assignCourierUseCase,
        private readonly UpdateDispatchStatusUseCase $updateDispatchStatusUseCase,
        private readonly GetDispatchDetailsUseCase $getDispatchDetailsUseCase,
        private readonly ListDispatchesUseCase $listDispatchesUseCase,
    ) {
    }

    #[Route('', name: 'dispatch_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $payload = $request->toArray();
        $dto = new CreateDispatchRequest();
        $dto->packageId = (string) ($payload['packageId'] ?? '');
        $dto->referenceNumber = (string) ($payload['referenceNumber'] ?? '');
        $dto->originAddress = (string) ($payload['originAddress'] ?? '');
        $dto->destinationAddress = (string) ($payload['destinationAddress'] ?? '');
        $dto->notes = isset($payload['notes']) ? (string) $payload['notes'] : null;

        $this->requestValidator->validate($dto);

        $dispatch = $this->createDispatchUseCase->execute(
            new DispatchData(
                $dto->packageId,
                $dto->referenceNumber,
                $dto->originAddress,
                $dto->destinationAddress,
                $dto->notes
            )
        );

        return $this->responseFactory->success(DispatchResponse::fromEntity($dispatch), null, Response::HTTP_CREATED);
    }

    #[Route('/{id}/assign', name: 'dispatch_assign_courier', methods: ['PATCH'])]
    public function assignCourier(string $id, Request $request): Response
    {
        $payload = $request->toArray();
        $dto = new AssignCourierRequest();
        $dto->courierId = (string) ($payload['courierId'] ?? '');

        $this->requestValidator->validate($dto);

        $dispatch = $this->assignCourierUseCase->execute(
            new CourierAssignmentData($id, $dto->courierId)
        );

        return $this->responseFactory->success(DispatchResponse::fromEntity($dispatch));
    }

    #[Route('/{id}/status', name: 'dispatch_update_status', methods: ['PATCH'])]
    public function updateStatus(string $id, Request $request): Response
    {
        $payload = $request->toArray();
        $dto = new UpdateDispatchStatusRequest();
        $dto->status = (string) ($payload['status'] ?? '');

        $this->requestValidator->validate($dto);

        $status = DispatchStatus::from($dto->status);

        if (DispatchStatus::Assigned === $status) {
            return $this->responseFactory->error(null, 'Use the assign endpoint to set assigned status', Response::HTTP_CONFLICT);
        }

        $dispatch = $this->updateDispatchStatusUseCase->execute(
            new DispatchStatusData($id, $status)
        );

        return $this->responseFactory->success(DispatchResponse::fromEntity($dispatch));
    }

    #[Route('/{id}', name: 'dispatch_show', methods: ['GET'])]
    public function show(string $id): Response
    {
        $details = $this->getDispatchDetailsUseCase->execute($id);

        return $this->responseFactory->success(
            DispatchResponse::fromEntity($details->dispatch, $details->history)
        );
    }

    #[Route('', name: 'dispatch_list', methods: ['GET'])]
    public function list(Request $request): Response
    {
        $query = new PaginationQuery(
            max(1, $request->query->getInt('page', 1)),
            min(100, max(1, $request->query->getInt('perPage', 15)))
        );

        $result = $this->listDispatchesUseCase->execute($query->page, $query->perPage);

        return $this->responseFactory->success(DispatchResponse::collection($result));
    }
}
