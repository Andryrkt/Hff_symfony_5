<?php

namespace App\Controller\Dom;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DomFirstController extends AbstractController
{
    /**
     * @Route("/dom/first", name="dom_first")
     */
    public function index(): Response
    {
        return $this->render('dom/dom_first/index.html.twig', [
            'controller_name' => 'DomFirstController',
        ]);
    }
}
