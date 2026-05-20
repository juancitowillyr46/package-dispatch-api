<?php

declare(strict_types=1);

namespace App\Shared\Presentation\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AuthLoginController
{
    #[Route('/api/login_check', name: 'api_login_check', methods: ['POST'])]
    public function __invoke(): Response
    {
        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
