<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ErrorTestController extends AbstractController
{
    /**
     * @Route("/test-error", name="app_test_error")
     */
    public function testError(): Response
    {
        // Cette erreur ne se déclenchera que si l'environnement est "prod"
        if ($this->getParameter('kernel.environment') === 'prod') {
            throw new \Exception('Ceci est une erreur de test simulée pour la notification.');
        }

        return new Response('Cette route ne déclenche une erreur qu\'en environnement de production.');
    }
}
