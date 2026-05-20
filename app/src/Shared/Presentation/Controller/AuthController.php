<?php

declare(strict_types=1);

namespace App\Shared\Presentation\Controller;

use App\Shared\Application\Service\ApiResponseFactory;
use App\Shared\Infrastructure\Security\Entity\User;
use App\Shared\Domain\Exception\UnauthorizedException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/auth')]
final class AuthController
{
    public function __construct(
        private readonly ApiResponseFactory $responseFactory,
        private readonly Security $security,
    ) {
    }

    #[Route('/me', name: 'auth_me', methods: ['GET'])]
    public function me(): Response
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new UnauthorizedException('Unauthorized');
        }

        return $this->responseFactory->success([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ]);
    }
}
