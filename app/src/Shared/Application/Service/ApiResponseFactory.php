<?php

declare(strict_types=1);

namespace App\Shared\Application\Service;

use App\Shared\Application\DTO\PaginatedResult;
use Symfony\Component\HttpFoundation\JsonResponse;

final class ApiResponseFactory
{
    public function success(mixed $data = [], ?string $message = null, int $status = JsonResponse::HTTP_OK): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ], $status);
    }

    public function error(mixed $data = null, string $message = 'An error occurred', int $status = JsonResponse::HTTP_BAD_REQUEST): JsonResponse
    {
        return new JsonResponse([
            'success' => false,
            'data' => $data,
            'message' => $message,
        ], $status);
    }

    public function paginated(PaginatedResult $result): JsonResponse
    {
        return $this->success([
            'items' => $result->getItems(),
            'pagination' => [
                'page' => $result->getPage(),
                'perPage' => $result->getPerPage(),
                'total' => $result->getTotal(),
                'totalPages' => $result->getTotalPages(),
            ],
        ]);
    }
}
