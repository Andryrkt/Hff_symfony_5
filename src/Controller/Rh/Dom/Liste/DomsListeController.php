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
     * @Route("/liste", name="doms_liste")
     */
    public function listeDom(DomRepository $domRepository)
    {
        // 1. gerer l'accés
        $this->denyAccessUnlessGranted('RH_ORDRE_MISSION_CREATE');

        // 2. recupération des données à afficher
        $page = 1;
        $limit = 10;
        $data = $domRepository->findPaginatedAndFiltered($page, $limit);

        return $this->render(
            'rh/dom/liste.html.twig',
            [
                'data' => $data
            ]
        );
    }
}
