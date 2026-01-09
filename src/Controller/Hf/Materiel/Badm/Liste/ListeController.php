<?php

namespace App\Controller\Hf\Materiel\Badm\Liste;


use App\Dto\Hf\Materiel\Badm\SearchDto;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Admin\AgenceSerializerService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Hf\Materiel\Badm\BadmRepository;
use App\Controller\Traits\PaginationAndSortingTrait;
use App\Mapper\Hf\Materiel\Badm\BadmMapper;
use App\Service\Navigation\ContextAwareBreadcrumbBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/hf/materiel/badm")
 */
class ListeController extends AbstractController
{
    use PaginationAndSortingTrait;

    /**
     * @Route("/liste", name="hf_materiel_badm_liste_index")
     */
    public function index(
        Request $request,
        BadmRepository $badmRepository,
        SearchDto $searchDto,
        BadmMapper $badmMapper,
        AgenceSerializerService $agenceSerializerService,
        ContextAwareBreadcrumbBuilder $breadcrumbBuilder
    ): Response {
        // 1. gerer l'accés 
        $this->denyAccessUnlessGranted('MATERIEL_BADM_VIEW');

        // 3. Récupération des données à afficher
        $page = $this->handlePaginationAndSorting($request, $searchDto);
        $paginationDatas = $badmRepository->findPaginatedAndFiltered($page, $searchDto->limit, $searchDto);
        $paginationDatas['data'] = array_map(function ($item) use ($badmMapper) {
            return $badmMapper->reverseMap($item);
        }, $paginationDatas['data']); // transform en secondFormDto


        return $this->render('hf/materiel/badm/liste/liste.html.twig', [
            'paginationData' => $paginationDatas,
            'queryParams' => $request->query->all(),
            'currentSort' => $searchDto->sortBy,
            'currentOrder' => $searchDto->sortOrder,
            'routeName' => 'hf_materiel_badm_liste_index',
            'agencesJson' => $agenceSerializerService->serializeAgencesForDropdown(),
            'breadcrumbs' => $breadcrumbBuilder->build('hf_materiel_badm_liste_index'),
            'buttons' => [],
        ]);
    }
}
