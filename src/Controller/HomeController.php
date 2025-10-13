<?php

namespace App\Controller;

use App\Service\Navigation\Home\HomeBreadcrumbBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(HomeBreadcrumbBuilder $breadcrumbBuilder): Response
    {
        $breadcrumb = $breadcrumbBuilder->build();

        return $this->render('home/index.html.twig', [
            'breadcrumb' => $breadcrumb,
        ]);
    }
}
