<?php

namespace App\Controller\Hf\Rh\Dom\Creation;

use Psr\Log\LoggerInterface;
use App\Service\Hf\Rh\Dom\DomPdfService;
use Symfony\Component\Form\FormInterface;
use App\Service\Hf\Rh\Dom\DomCreationHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Constants\Admin\Historisation\TypeDocumentConstants;
use App\Constants\Admin\Historisation\TypeOperationConstants;
use App\Service\Historique_operation\HistoriqueOperationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class AbstractDomFormController extends AbstractController
{
    protected LoggerInterface $logger;
    protected DomCreationHandler $domCreationHandler;
    protected HistoriqueOperationService $historiqueOperationService;

    public function __construct(
        LoggerInterface $domSecondFormLogger,
        DomCreationHandler $domCreationHandler,
        HistoriqueOperationService $historiqueOperationService
    ) {
        $this->logger = $domSecondFormLogger;
        $this->domCreationHandler = $domCreationHandler;
        $this->historiqueOperationService = $historiqueOperationService;
    }

    protected function traitementFormulaire(Request $request, FormInterface $form, DomPdfService $pdfService)
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->logger->info('Second formulaire soumis et valide.');
            $this->logger->debug('Données du formulaire', ['data' => $form->getData()]);
            $redirectResponse = $this->processValidForm($form, $pdfService);
            if ($redirectResponse) {
                return $redirectResponse;
            }
        }
    }

    protected function processValidForm(FormInterface $form, DomPdfService $pdfService): ?RedirectResponse
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
            TypeOperationConstants::TYPE_OPERATION_CREATION_NAME,
            TypeDocumentConstants::TYPE_DOCUMENT_DOM_CODE,
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
