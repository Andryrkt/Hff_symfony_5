<?php

namespace App\Controller\Hf\Atelier\Dit\Liste;

use App\Constants\Hf\Atelier\Dit\ButtonsConstants;
use App\Constants\Hf\Atelier\Dit\LegendeConstant;
use App\Controller\Traits\PaginationAndSortingTrait;
use App\Dto\Hf\Atelier\Dit\SearchDto;
use App\Entity\Hf\Atelier\Dit\Soumission\Ors\Ors;
use App\Form\Hf\Atelier\Dit\SearchType;
use App\Form\Hf\Atelier\Dit\SoumissionDocumentAValidationType;
use App\Mapper\Hf\Atelier\Dit\Mapper;
use App\Service\Hf\Atelier\Dit\Soumission\Ors\OrsBlockingConditionService;
use App\Repository\Hf\Atelier\Dit\DitRepository;
use App\Service\Navigation\ContextAwareBreadcrumbBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        ContextAwareBreadcrumbBuilder $breadcrumbBuilder,
        OrsBlockingConditionService $orsBlockingConditionService
    ) {
        // 1. gerer l'accés 
        $this->denyAccessUnlessGranted('ATELIER_DIT_VIEW');

        // 2. creation et traitement du formulaire de recherhce
        $form = $this->createForm(SearchType::class, $searchDto, ['method' => 'GET']);
        $this->traitementFormulaireDeRecherche($request, $form);

        // 3. Récupération des données à afficher (sans mapping auto pour plus de perf)
        $paginationDatas = $this->getPaginatedData($request, $searchDto, $ditRepository, null);

        // Mapping optimisé en batch
        $paginationDatas['data'] = $ditMapper->reverseMapList($paginationDatas['data']);

        // 4. Création et traitement du formulaire de soumission de document (pour la modal)
        $soumissionForm = $this->createForm(SoumissionDocumentAValidationType::class);
        $response = $this->traitementFormulaireSoumissionDocumentAValidation($request, $soumissionForm, $orsBlockingConditionService);
        if ($response) {
            return $response;
        }

        $response = $this->render('hf/atelier/dit/liste/liste.html.twig', [
            'data' => $paginationDatas['data'],
            'paginationData' => $paginationDatas,
            'queryParams' => $request->query->all(),
            'currentSort' => $searchDto->sortBy,
            'currentOrder' => $searchDto->sortOrder,
            'routeName' => 'hf_atelier_dit_liste_index',
            'buttons' => ButtonsConstants::BUTTONS_ACTIONS,
            'legende' => LegendeConstant::LEGENDE_DIT,
            'breadcrumbs' => $breadcrumbBuilder->build('hf_atelier_dit_liste_index'),
            'form' => $form->createView(),
            'soumissionForm' => $soumissionForm->createView(),
        ]);

        // Forcer l'absence de cache pour éviter les snapshots HTML obsolètes
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }

    private function traitementFormulaireDeRecherche(Request $request, FormInterface $form): void
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $searchDto = $form->getData();
            // stocage des donner dans le session
            $session = $request->getSession();
            $session->set('search_dto', $searchDto);
        }
    }

    private function traitementFormulaireSoumissionDocumentAValidation(
        Request $request,
        FormInterface $soumissionForm,
        OrsBlockingConditionService $orsBlockingConditionService
    ): ?Response {
        $soumissionForm->handleRequest($request);

        if ($soumissionForm->isSubmitted() && $soumissionForm->isValid()) {
            $soumissionDoc = $soumissionForm->getData();
            $numeroDit = $soumissionDoc['numeroDit'];
            $numeroOr = $soumissionDoc['numeroOr'];
            $docDansDW = $soumissionDoc['docDansDW'];

            switch ($docDansDW) {
                case 'OR':
                    $blockingError = $orsBlockingConditionService->checkBlockingConditionsDepuisListe($numeroDit, $numeroOr);

                    if ($blockingError !== null) {
                        $this->addFlash('warning', $blockingError);
                        return $this->redirectToRoute('hf_atelier_dit_liste_index', [], 303);
                    }

                    return $this->redirectToRoute("hf_atelier_dit_soumission_ors_index", ['numDit' => $numeroDit, 'numOr' => $numeroOr], 303);

                case 'FACTURE':
                    return $this->redirectToRoute("hf_atelier_dit_soumission_facture_index", ['numDit' => $numeroDit], 303);

                case 'RI':
                    return $this->redirectToRoute("hf_atelier_dit_soumission_ri_index", ['numDit' => $numeroDit], 303);

                case 'DEVIS-VP':
                    return $this->redirectToRoute("hf_atelier_dit_soumission_devis_index", ['numDit' => $numeroDit, 'type' => 'VP'], 303);

                case 'DEVIS-VA':
                    return $this->redirectToRoute("hf_atelier_dit_soumission_devis_index", ['numDit' => $numeroDit, 'type' => 'VA'], 303);

                case 'BC':
                    return $this->redirectToRoute("hf_atelier_dit_soumission_bc_index", ['numDit' => $numeroDit], 303);

                default:
                    return null; // ou gérer le cas par défaut
            }
        }

        return null;
    }
}
