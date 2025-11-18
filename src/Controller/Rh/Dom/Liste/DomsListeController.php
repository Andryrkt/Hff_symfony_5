<?php

namespace App\Controller\Rh\Dom\Liste;


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
    public function listeDom()
    {
        // 1. gerer l'accés
        $this->denyAccessUnlessGranted('RH_ORDRE_MISSION_CREATE');

        $this->addFlash(
            'success',
            'Page de la liste des ordres de mission'
        );

        return $this->render(
            'rh/dom/liste.html.twig'
        );
    }
}
