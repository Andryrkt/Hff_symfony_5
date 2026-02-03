<?php

namespace App\Controller\Hf\Atelier\Dit\Liste;

use App\Dto\Hf\Atelier\Dit\SearchDto;
use App\Mapper\Hf\Atelier\Dit\Mapper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Hf\Atelier\Dit\DitRepository;
use App\Constants\Hf\Atelier\Dit\ButtonsConstants;
use App\Controller\Traits\PaginationAndSortingTrait;
use App\Service\Navigation\ContextAwareBreadcrumbBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/hf/atelier/dit")
 */
class ListeController extends AbstractController
{
    use PaginationAndSortingTrait;

    /**
     * @Route("/liste", name="hf_atelier_dit_liste_index")
     */
    public function index(
        Request $request,
        DitRepository $ditRepository,
        Mapper $ditMapper,
        SearchDto $searchDto,
        ContextAwareBreadcrumbBuilder $breadcrumbBuilder
    ) {
        // 1. gerer l'accés 
        $this->denyAccessUnlessGranted('ATELIER_DIT_VIEW');

        // 4. Récupération des données à afficher
        $paginationDatas = $this->getPaginatedData($request, $searchDto, $ditRepository, $ditMapper);

        return $this->render('hf/atelier/dit/liste/liste.html.twig', [
            'data' => $paginationDatas['data'],
            'paginationData' => $paginationDatas,
            'queryParams' => $request->query->all(),
            'currentSort' => $searchDto->sortBy,
            'currentOrder' => $searchDto->sortOrder,
            'routeName' => 'hf_atelier_dit_liste_index',
            'buttons' => ButtonsConstants::BUTTONS_ACTIONS,
            'breadcrumbs' => $breadcrumbBuilder->build('hf_atelier_dit_liste_index')
        ]);
    }
}
