<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\EventSubscriber;

use App\Shared\Application\Service\ApiResponseFactory;
use App\Shared\Domain\Exception\ConflictException;
use App\Shared\Domain\Exception\DomainException;
use App\Shared\Domain\Exception\NotFoundException;
use App\Shared\Domain\Exception\UnauthorizedException;
use App\Shared\Domain\Exception\ValidationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

final class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly ApiResponseFactory $responseFactory,
        private readonly bool $debug = false,
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onException',
        ];
    }

    public function onException(ExceptionEvent $event): void
    {
        $request = $event->getRequest();
        if (!str_starts_with($request->getPathInfo(), '/api')) {
            return;
        }

        $throwable = $event->getThrowable();
        [$status, $message, $data] = $this->mapThrowable($throwable);

        $event->setResponse(
            $this->responseFactory->error(
                $data,
                $message,
                $status
            )
        );
    }

    /**
     * @return array{0:int,1:string,2:mixed}
     */
    private function mapThrowable(\Throwable $throwable): array
    {
        return match (true) {
            $throwable instanceof ValidationException => [JsonResponse::HTTP_UNPROCESSABLE_ENTITY, $throwable->getMessage(), ['errors' => $throwable->getErrors()]],
            $throwable instanceof NotFoundException => [JsonResponse::HTTP_NOT_FOUND, $throwable->getMessage(), null],
            $throwable instanceof ConflictException => [JsonResponse::HTTP_CONFLICT, $throwable->getMessage(), null],
            $throwable instanceof UnauthorizedException => [JsonResponse::HTTP_UNAUTHORIZED, $throwable->getMessage(), null],
            $throwable instanceof AccessDeniedHttpException => [JsonResponse::HTTP_FORBIDDEN, $throwable->getMessage() ?: 'Access denied', null],
            $throwable instanceof HttpExceptionInterface => [$throwable->getStatusCode(), $throwable->getMessage() ?: 'HTTP error', null],
            $throwable instanceof DomainException => [JsonResponse::HTTP_BAD_REQUEST, $throwable->getMessage(), null],
            default => [JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $this->debug ? $throwable->getMessage() : 'Unexpected error', null],
        };
    }
}
