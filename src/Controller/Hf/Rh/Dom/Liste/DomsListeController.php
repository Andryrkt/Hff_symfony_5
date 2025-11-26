<?php

namespace App\Controller\Hf\Rh\Dom\Liste;

use App\Dto\Hf\Rh\Dom\DomSearchDto;
use App\Form\Hf\Rh\Dom\Liste\DomSearchType;
use App\Repository\Hf\Rh\Dom\DomRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Admin\AgenceSerializerService;
use App\Service\Security\AgenceAccessService;
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
    public function index(
        Request $request,
        DomRepository $domRepository,
        AgenceSerializerService $agenceSerializerService,
        AgenceAccessService $agenceAccessService
    ) {
        // 1. gerer l'accés
        $this->denyAccessUnlessGranted('RH_ORDRE_MISSION_VIEW');

        // 2. Récupérer les agences autorisées pour l'utilisateur connecté
        $agenceIdsAutorises = $agenceAccessService->getAuthorizedAgenceIds($this->getUser());

        $domSearchDto = new DomSearchDto();
        // 3. creation du formulaire de recherhce
        $form = $this->createForm(DomSearchType::class, $domSearchDto, [
            'method' => 'GET'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $domSearchDto = $form->getData();
        }

        // 4. recupération des données à afficher avec filtrage par agence
        $page = $request->query->getInt('page', 1);
        $limit = 50;
        $paginationData = $domRepository->findPaginatedAndFiltered($page, $limit, $domSearchDto, $agenceIdsAutorises);

        return $this->render(
            'hf/rh/dom/liste/liste.html.twig',
            [
                'paginationData' => $paginationData,
                'form' => $form->createView(),
                'agencesJson'   => $agenceSerializerService->serializeAgencesForDropdown(),
                'routeName' => 'liste_dom_index',
                'queryParams' => $request->query->all()
            ]
        );
    }
}
