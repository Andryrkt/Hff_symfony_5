<?php

namespace App\Controller\Hf\Rh\Dom\Liste;

use App\Form\Hf\Rh\Dom\Liste\DomSearchType;
use App\Repository\Hf\Rh\Dom\DomRepository;
use App\Service\Admin\AgenceSerializerService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/rh/ordre-de-mission")
 */
class DomsListeController extends AbstractController
{

    private AgenceSerializerService $agenceSerializerService;

    /**
     * affichage de l'architecture de la liste du DOM
     * @Route("/liste-dom", name="liste_dom_index")
     */
    public function index(DomRepository $domRepository, AgenceSerializerService $agenceSerializerService)
    {
        // 1. gerer l'accés
        $this->denyAccessUnlessGranted('RH_ORDRE_MISSION_VIEW');

        // 2. creation du formulaire de recherhce
        $form = $this->createForm(DomSearchType::class, null, [
            'method' => 'GET'
        ]);


        // 3. recupération des données à afficher
        $page = 1;
        $limit = 10;
        $paginationData = $domRepository->findPaginatedAndFiltered($page, $limit);

        return $this->render(
            'hf/rh/dom/liste/liste.html.twig',
            [
                'paginationData' => $paginationData,
                'form' => $form->createView(),
                'agencesJson'   => $agenceSerializerService->serializeAllAgences()
            ]
        );
    }
}
