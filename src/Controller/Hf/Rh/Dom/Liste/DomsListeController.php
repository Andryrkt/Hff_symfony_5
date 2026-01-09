<?php

declare(strict_types=1);

namespace App\Controller\Hf\Rh\Dom\Liste;

use App\Dto\Hf\Rh\Dom\DomSearchDto;
use App\Form\Hf\Rh\Dom\Liste\DomSearchType;
use App\Repository\Hf\Rh\Dom\DomRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Admin\AgenceSerializerService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Constants\Hf\Rh\Dom\ButtonsDomConstants;
use App\Controller\Traits\PaginationAndSortingTrait;

use App\Service\Navigation\ContextAwareBreadcrumbBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/hf/rh/ordre-de-mission")
 */
class DomsListeController extends AbstractController
{
    use PaginationAndSortingTrait;

    /**
     * affichage de l'architecture de la liste du DOM
     * @Route("/liste-dom", name="dom_liste_index")
     */
    public function index(
        Request $request,
        DomRepository $domRepository,
        AgenceSerializerService $agenceSerializerService,
        ContextAwareBreadcrumbBuilder $breadcrumbBuilder

    ): Response {
        // 1. gerer l'accés
        $this->denyAccessUnlessGranted('RH_ORDRE_MISSION_VIEW');

        $domSearchDto = new DomSearchDto();
        // 2. creation du formulaire de recherhce
        $form = $this->createForm(DomSearchType::class, $domSearchDto, [
            'method' => 'GET'
        ]);

        // 3. traitement du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $domSearchDto = $form->getData();
            // stocage des donner dans le session
            $session = $request->getSession();
            $session->set('dom_search_dto', $domSearchDto);
        }

        // 4. recupération des données à afficher avec filtrage par agence
        $page = $this->handlePaginationAndSorting($request, $domSearchDto);
        $paginationData = $domRepository->findPaginatedAndFiltered($page, $domSearchDto->limit, $domSearchDto);

        return $this->render(
            'hf/rh/dom/liste/liste.html.twig',
            [
                'paginationData' => $paginationData,
                'routeName' => 'dom_liste_index',
                'queryParams' => $request->query->all(),
                'currentSort' => $domSearchDto->sortBy,
                'currentOrder' => $domSearchDto->sortOrder,
                'form' => $form->createView(),
                'agencesJson' => $agenceSerializerService->serializeAgencesForDropdown(),
                'breadcrumbs' => $breadcrumbBuilder->build('dom_liste_index'),
                'buttons' => ButtonsDomConstants::BUTTONS_ACTIONS,
            ]
        );
    }
}
