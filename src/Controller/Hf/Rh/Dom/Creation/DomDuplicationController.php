<?php

namespace App\Controller\Hf\Rh\Dom\Creation;

use Psr\Log\LoggerInterface;
use App\Form\Hf\Rh\Dom\SecondFormType;
use App\Service\Hf\Rh\Dom\DomPdfService;
use Symfony\Component\Form\FormInterface;
use App\Repository\Hf\Rh\Dom\DomRepository;
use App\Service\Hf\Rh\Dom\DomCreationHandler;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Admin\AgenceSerializerService;
use App\Factory\Hf\Rh\Dom\SecondFormDtoFactory;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Service\Navigation\ContextAwareBreadcrumbBuilder;
use App\Service\Historique_operation\HistoriqueOperationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/hf/rh/ordre-de-mission")
 */
final class DomDuplicationController extends AbstractDomFormController
{
    /**
     * @Route("/duplication/{numeroOrdreMission}", name="dom_duplication")
     */
    public function index(
        string $numeroOrdreMission,
        DomRepository $domRepository,
        SecondFormDtoFactory $secondFormDtoFactory,
        AgenceSerializerService $agenceSerializerService,
        ContextAwareBreadcrumbBuilder $breadcrumbBuilder,
        DomPdfService $pdfService,
        Request $request
    ) {

        // recuperation des données du numéro d'Ordre de mission
        $dom = $domRepository->findOneBy(['numeroOrdreMission' => $numeroOrdreMission]);

        // hydratation du secondFormDto
        $secondFormDto = $secondFormDtoFactory->createFromDom($dom);

        // Mesure: Création du formulaire Symfony
        $form = $this->createForm(SecondFormType::class, $secondFormDto);

        // traitement du formulaire
        $this->traitementFormulaire($request, $form, $pdfService);

        return $this->render('hf/rh/dom/creation/dom_duplication.html.twig', [
            'form' => $form->createView(),
            'secondFormDto' => $secondFormDto,
            'agencesJson' => $agenceSerializerService->serializeAgencesForDropdown(),
            'breadcrumbs'   => $breadcrumbBuilder->build('dom_duplication', [
                'numeroOrdreMission' => $numeroOrdreMission
            ]),
        ]);
    }
}
