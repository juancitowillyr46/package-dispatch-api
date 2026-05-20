<?php

declare(strict_types=1);

namespace App\Shared\Presentation\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController
{
    #[Route('/', name: 'home', methods: ['GET'])]
    public function index(): Response
    {
        return new Response('Bienvenido a la API Dispatch', Response::HTTP_OK, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    #[Route('/api', name: 'api_home', methods: ['GET'])]
    public function apiIndex(): Response
    {
        return new Response('Bienvenido a la API Dispatch', Response::HTTP_OK, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }
}
