<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class ErrorController extends AbstractController
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @Route("/error/403", name="error_403")
     */
    public function error403(): Response
    {
        return new Response(
            $this->twig->render('error/403.html.twig'),
            Response::HTTP_FORBIDDEN
        );
    }

    /**
     * @Route("/error/404", name="error_404")
     */
    public function error404(): Response
    {
        return new Response(
            $this->twig->render('error/404.html.twig'),
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * @Route("/error/500", name="error_500")
     */
    public function error500(): Response
    {
        return new Response(
            $this->twig->render('error/500.html.twig'),
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
} 