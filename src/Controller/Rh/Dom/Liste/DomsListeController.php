<?php

namespace App\Controller\Rh\Dom\Liste;

use App\Repository\Rh\Dom\DomRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/rh/ordre-de-mission")
 */
class DomsListeController extends AbstractController
{


    /**
     * affichage de l'architecture de la liste du DOM
     * @Route("/liste-dom", name="liste_dom_index")
     */
    public function index(DomRepository $domRepository)
    {
        // 1. gerer l'accés
        $this->denyAccessUnlessGranted('RH_ORDRE_MISSION_CREATE');

        // 2. recupération des données à afficher
        $page = 1;
        $limit = 10;
        $paginationData = $domRepository->findPaginatedAndFiltered($page, $limit);

        return $this->render(
            'rh/dom/liste/liste.html.twig',
            [
                'data' => $paginationData['data']
            ]
        );
    }
}
