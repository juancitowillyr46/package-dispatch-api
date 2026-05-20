<?php

declare(strict_types=1);

namespace App\Shared\Presentation\Controller;

use App\Shared\Application\Service\ApiResponseFactory;
use App\Shared\Infrastructure\Security\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class AuthLoginController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly JWTTokenManagerInterface $tokenManager,
        private readonly ApiResponseFactory $responseFactory,
    ) {
    }

    #[Route('/api/login_check', name: 'api_login_check', methods: ['POST'])]
    public function __invoke(Request $request): Response
    {
        $payload = $request->request->all();

        if ([] === $payload) {
            try {
                $payload = $request->toArray();
            } catch (JsonException) {
                return $this->responseFactory->error(null, 'Invalid JSON.', Response::HTTP_BAD_REQUEST);
            }
        }

        if (!is_array($payload)) {
            return $this->responseFactory->error(null, 'Invalid JSON.', Response::HTTP_BAD_REQUEST);
        }

        $email = isset($payload['email']) ? trim((string) $payload['email']) : '';
        $password = isset($payload['password']) ? (string) $payload['password'] : '';

        if ('' === $email || '' === $password) {
            return $this->responseFactory->error(null, 'Email and password are required.', Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userRepository->findByEmail($email);

        if (null === $user || !$this->passwordHasher->isPasswordValid($user, $password)) {
            return $this->responseFactory->error(null, 'Invalid credentials.', Response::HTTP_UNAUTHORIZED);
        }

        return $this->responseFactory->success([
            'token' => $this->tokenManager->create($user),
        ]);
    }
}
