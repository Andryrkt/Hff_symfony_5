<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/test/error", name="test_error")
     */
    public function error(): Response
    {
        throw new \RuntimeException('Ceci est une erreur de test simulée pour vérifier l\'envoi d\'e-mail.');
    }
}
