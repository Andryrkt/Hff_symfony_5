<?php

namespace App\Controller;

use App\Service\navigation\BreadcrumbBuilderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(BreadcrumbBuilderService $breadcrumb): Response
    {
        $breadcrumb
            ->add('Accueil', 'app_home', [], [
                ['label' => 'Tableau de bord', 'route' => 'app_home'],
                ['label' => 'Contact', 'route' => 'app_home'],
            ])
            ->add('Produits', 'app_home', [], [
                ['label' => 'Catégorie A', 'route' => 'app_home', 'params' => ['cat' => 'A']],
                ['label' => 'Catégorie B', 'route' => 'app_home', 'params' => ['cat' => 'B']],
            ])
            ->add("Détail du produit #42");

        return $this->render('home/index.html.twig', [
            'breadcrumb' => $breadcrumb->get(),
        ]);
    }
}
