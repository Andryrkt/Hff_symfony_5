<?php

namespace App\Controller\Hf\Materiel\Casier\Liste;

use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Hf\Materiel\Casier\CasierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/hf/materiel/casier")
 */
class ListeController extends AbstractController
{
    /**
     * @Route("/liste", name="hf_materiel_casier_liste_index")
     */
    public function index(CasierRepository $casierRepository)
    {
        // 1. gerer l'accés
        $this->denyAccessUnlessGranted('MATERIEL_CASIER_VIEW');

        // recuperation des données
        $casiers = $casierRepository->getPaginatedAndFiltered();

        return $this->render('hf/materiel/casier/liste/liste.html.twig', [
            'casiers' => $casiers,
        ]);
    }
}
