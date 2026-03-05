<?php

namespace App\Controller\Hf\Atelier\Dit\Soumission\Ors;

use App\Factory\Hf\Atelier\Dit\Soumission\Ors\OrsFactory;
use App\Form\Hf\Atelier\Dit\Soumission\Ors\OrsType;
use App\Service\Hf\Atelier\Dit\CreationHandler;
use App\Service\Hf\Atelier\Dit\PdfService;
use App\Service\Historique_operation\HistoriqueOperationService;
use App\Service\Navigation\ContextAwareBreadcrumbBuilder;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/hf/atelier/dit/soumission/ors", name="hf_atelier_dit_soumission_ors_")
 */
class SoumissionOrsController extends AbstractController
{
    protected HistoriqueOperationService $historiqueOperationService;
    protected LoggerInterface $logger;
    protected ContextAwareBreadcrumbBuilder $breadcrumbBuilder;

    public function __construct(
        HistoriqueOperationService $historiqueOperationService,
        LoggerInterface $logger,
        ContextAwareBreadcrumbBuilder $breadcrumbBuilder
    ) {
        $this->historiqueOperationService = $historiqueOperationService;
        $this->logger = $logger;
        $this->breadcrumbBuilder = $breadcrumbBuilder;
    }

    /**
     * @Route("/{numDit}/{numOr}", name="index")
     */
    public function index(
        string $numDit,
        string $numOr,
        OrsFactory $orsFactory
    ) {
        // 1. gerer l'accés 
        $this->denyAccessUnlessGranted('ATELIER_DIT_SOUMISSION_ORS');

        // 2. creation et initialisation du formulaire
        $dto = $orsFactory->create($numDit, $numOr);
        $form = $this->createForm(OrsType::class, $dto);

        return $this->render('hf/atelier/dit/soumission/ors/index.html.twig', [
            'numDit' => $numDit,
            'form' => $form->createView(),
            'breadcrumbs' => $this->breadcrumbBuilder->build('hf_atelier_dit_soumission_ors_index'),
        ]);
    }

    private function handleFormSubmission(
        Request $request,
        FormInterface $form,
        PdfService $pdfService,
        CreationHandler $creationHandler
    ) {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->logger->info('formulaire soumis et valide.');
            $this->logger->debug('Données du formulaire', ['data' => $form->getData()]);
            // $redirectResponse = $this->processValidForm($form, $pdfService, $creationHandler);
            // if ($redirectResponse) {
            //     return $redirectResponse;
            // }
        }
    }
}
