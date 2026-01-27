<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiLoginController extends AbstractController
{
    /**
     * @Route("/api/login", name="api_login", methods={"POST"})
     */
    public function login(): JsonResponse
    {
        // Ce contrôleur ne sera jamais appelé car l'authentification
        // est gérée par le firewall json_login
        // Il sert uniquement à définir la route
        return new JsonResponse(['message' => 'Login endpoint']);
    }
}
