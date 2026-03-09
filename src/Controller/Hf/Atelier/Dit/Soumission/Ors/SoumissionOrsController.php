<?php

namespace App\Controller\Hf\Atelier\Dit\Soumission\Ors;

use App\Constants\Admin\Historisation\TypeDocumentConstants;
use App\Constants\Admin\Historisation\TypeOperationConstants;
use App\Factory\Hf\Atelier\Dit\Soumission\Ors\OrsFactory;
use App\Form\Hf\Atelier\Dit\Soumission\Ors\OrsType;
use App\Service\Hf\Atelier\Dit\PdfService;
use App\Service\Hf\Atelier\Dit\Soumission\Ors\CreationHandler;
use App\Service\Hf\Atelier\Dit\Soumission\Ors\OrsPdfService;
use App\Service\Historique_operation\HistoriqueOperationService;
use App\Service\Navigation\ContextAwareBreadcrumbBuilder;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
        OrsFactory $orsFactory,
        Request $request,
        OrsPdfService $orsPdfService,
        CreationHandler $creationHandler
    ) {
        // 1. gerer l'accés 
        $this->denyAccessUnlessGranted('ATELIER_DIT_SOUMISSION_ORS');

        // 2. creation et initialisation du formulaire
        $dto = $orsFactory->create($numDit, $numOr);
        $orsFactory->enrichissementDto($dto);

        $form = $this->createForm(OrsType::class, $dto);

        //3. Traitement du Formulaire
        $response = $this->handleFormSubmission($request, $form, $orsPdfService, $creationHandler);
        if ($response) {
            return $response;
        }

        $renderResponse = $this->render('hf/atelier/dit/soumission/ors/index.html.twig', [
            'numDit' => $numDit,
            'form' => $form->createView(),
            'breadcrumbs' => $this->breadcrumbBuilder->build('hf_atelier_dit_soumission_ors_index'),
        ]);

        // Si le formulaire a été soumis mais qu'on arrive ici (erreur de validation ou exception), 
        // on renvoie un code 422 pour Turbo (Hotwire).
        if ($form->isSubmitted()) {
            $renderResponse->setStatusCode(422);
        }

        return $renderResponse;
    }

    private function handleFormSubmission(
        Request $request,
        FormInterface $form,
        OrsPdfService $orsPdfService,
        CreationHandler $creationHandler
    ) {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->logger->info('formulaire soumis et valide.');
            $this->logger->debug('Données du formulaire', ['data' => $form->getData()]);
            $redirectResponse = $this->processValidForm($form, $orsPdfService, $creationHandler);
            if ($redirectResponse) {
                return $redirectResponse;
            }
        }
    }

    protected function processValidForm(
        FormInterface $form,
        OrsPdfService $orsPdfService,
        CreationHandler $creationHandler
    ): ?RedirectResponse {
        $numero = 'non-défini';
        $message = 'Soumission d\'un OR à validation.';
        $success = false;

        try {
            $ors = $creationHandler->handel($form, $orsPdfService);

            if (empty($ors)) {
                throw new \RuntimeException("Aucune donnée (intervention) n'a été trouvée pour cet OR. Veuillez vérifier que l'OR est bien complété sur Informix.");
            }

            $numero = $ors[0]->getNumeroOr();
            $success = true;
            $message = 'L\'OR a été soumis avec succès.';
            $this->logger->info($message, ['numero' => $numero]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $this->logger->error(
                'Erreur lors de la soumission de l\'OR : ' . $message,
                ['numero' => $numero, 'exception' => $e]
            );
        }

        $this->historiqueOperationService->enregistrer(
            $numero,
            TypeOperationConstants::TYPE_OPERATION_SOUMISSION_NAME,
            TypeDocumentConstants::TYPE_DOCUMENT_OR_CODE,
            $success,
            $message
        );

        if ($success) {
            $this->addFlash('success', $message);
            return $this->redirectToRoute('hf_atelier_dit_liste_index');
        }

        $this->addFlash('warning', $message);
        return null;
    }
}
