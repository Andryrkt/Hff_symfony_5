<?php

namespace App\Controller\Hf\Materiel\Casier\Liste;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Hf\Materiel\Casier\Liste\SearchType;
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
    public function index(CasierRepository $casierRepository, Request $request)
    {
        // 1. gerer l'accés
        $this->denyAccessUnlessGranted('MATERIEL_CASIER_VIEW');

        // creation du formulaire de recherhce
        $form = $this->createForm(SearchType::class);

        //traitement du formulaire
        $form->handleRequest($request);
        $criteria = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $criteria = $form->getData();
        }
        // recuperation des données
        $casiers = $casierRepository->getCasiersTemporaire($criteria);

        return $this->render('hf/materiel/casier/liste/liste.html.twig', [
            'casiers' => $casiers,
            'form' => $form->createView(),
        ]);
    }
}
