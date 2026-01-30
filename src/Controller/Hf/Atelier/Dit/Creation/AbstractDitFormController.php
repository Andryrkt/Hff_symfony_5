<?php

namespace App\Controller\Hf\Atelier\Dit\Creation;

use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormInterface;
use App\Service\Hf\Atelier\Dit\PdfService;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Hf\Atelier\Dit\CreationHandler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Service\Navigation\ContextAwareBreadcrumbBuilder;
use App\Constants\Admin\Historisation\TypeDocumentConstants;
use App\Constants\Admin\Historisation\TypeOperationConstants;
use App\Service\Historique_operation\HistoriqueOperationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class AbstractDitFormController extends AbstractController
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


    protected function traitementFormulaire(
        Request $request,
        FormInterface $form,
        PdfService $pdfService,
        CreationHandler $creationHandler
    ) {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->logger->info('formulaire soumis et valide.');
            $this->logger->debug('Données du formulaire', ['data' => $form->getData()]);
            $redirectResponse = $this->processValidForm($form, $pdfService, $creationHandler);
            if ($redirectResponse) {
                return $redirectResponse;
            }
        }
    }

    protected function processValidForm(
        FormInterface $form,
        PdfService $pdfService,
        CreationHandler $creationHandler
    ): ?RedirectResponse {
        $numero = 'non-défini';
        $message = 'Création de demande d\'intervention.';
        $success = false;

        try {
            $dit = $creationHandler->handel($form, $pdfService);
            $numero = $dit->getNumeroDit();
            $success = true;
            $message = 'La demande d\'intervention a été créée avec succès.';
            $this->logger->info($message, ['numero' => $numero]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $this->logger->error(
                'Erreur lors de la création de demande d\'intervention : ' . $message,
                ['numero' => $numero, 'exception' => $e]
            );
        }

        $this->historiqueOperationService->enregistrer(
            $numero,
            TypeOperationConstants::TYPE_OPERATION_CREATION_NAME,
            TypeDocumentConstants::TYPE_DOCUMENT_DIT_CODE,
            $success,
            $message
        );

        if ($success) {
            $this->addFlash('success', $message);
            return $this->redirectToRoute('hf_atelier_dit_list_index');
        }

        $this->addFlash('warning', $message);
        return null;
    }
}
