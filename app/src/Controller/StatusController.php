<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path:'api', name:'app_api')]
final class StatusController extends AbstractController
{
    #[Route(path:'', name: 'app_api_index', methods:['GET'])]
    public function index(Request $request): JsonResponse
    {
        return $this->json(data: [
            'status' => 'server is running',
            'host' => $request->getHttpHost(),
            'protocol' => $request->getScheme(),
        ]);
    }

    #[Route(path:'/ping', name: 'app_api_ping', methods:['GET'])]
    public function ping(): JsonResponse
    {
        return $this->json(data: [
            'status' => 'pong',
        ]);
    }
}