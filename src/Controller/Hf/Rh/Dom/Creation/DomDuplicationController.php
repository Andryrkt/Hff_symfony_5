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
class DomDuplicationController extends AbstractController
{
    private LoggerInterface $logger;
    private DomCreationHandler $domCreationHandler;
    private HistoriqueOperationService $historiqueOperationService;

    public function __construct(
        LoggerInterface $domSecondFormLogger,
        DomCreationHandler $domCreationHandler,
        HistoriqueOperationService $historiqueOperationService
    ) {
        $this->logger = $domSecondFormLogger;
        $this->domCreationHandler = $domCreationHandler;
        $this->historiqueOperationService = $historiqueOperationService;
    }

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

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->logger->info('Second formulaire soumis et valide.');
            $this->logger->debug('Données du formulaire', ['data' => $form->getData()]);
            $redirectResponse = $this->processValidForm($form, $pdfService);
            if ($redirectResponse) {
                return $redirectResponse;
            }
        }

        return $this->render('hf/rh/dom/creation/dom_duplication.html.twig', [
            'form' => $form->createView(),
            'secondFormDto' => $secondFormDto,
            'agencesJson' => $agenceSerializerService->serializeAgencesForDropdown(),
            'breadcrumbs'   => $breadcrumbBuilder->build('dom_duplication', [
                'numeroOrdreMission' => $numeroOrdreMission
            ]),
        ]);
    }

    private function processValidForm(FormInterface $form, DomPdfService $pdfService): ?RedirectResponse
    {
        $numeroDom = 'non-défini';
        $message = 'Création de l\'ordre de mission.';
        $success = false;

        try {
            $dom = $this->domCreationHandler->handle($form, $pdfService);
            $numeroDom = $dom->getNumeroOrdreMission();
            $success = true;
            $message = 'La demande d\'ordre de mission a été créée avec succès.';
            $this->logger->info($message, ['numero_dom' => $numeroDom]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $this->logger->error(
                'Erreur lors de la création de l\'ordre de mission : ' . $message,
                ['numero_dom' => $numeroDom, 'exception' => $e]
            );
        }

        $this->historiqueOperationService->enregistrer(
            $numeroDom,
            'CREATION',
            'DOM',
            $success,
            $message
        );

        if ($success) {
            $this->addFlash('success', $message);
            return $this->redirectToRoute('dom_liste_index');
        }

        $this->addFlash('warning', $message);
        return null;
    }
}
