<?php

namespace App\Service\Hf\Atelier\Dit;

use Psr\Log\LoggerInterface;
use App\Entity\Hf\Atelier\Dit\Dit;
use App\Mapper\Hf\Atelier\Dit\Mapper;
use App\Factory\Hf\Atelier\Dit\FormFactory;
use Symfony\Component\Form\FormInterface;
use App\Repository\Hf\Atelier\Dit\DitRepository;
use App\Constants\Admin\Historisation\TypeDocumentConstants;
use App\Constants\Admin\Historisation\TypeOperationConstants;
use App\Service\Historique_operation\HistoriqueOperationService;

class CreationHandler
{
    private Mapper $mapper;
    private DocumentManager $documentManager;
    private DitRepository $ditRepository;
    private HistoriqueOperationService $historiqueOperationService;
    private LoggerInterface $logger;
    private FormFactory $formFactory;

    public function __construct(
        Mapper $mapper,
        DocumentManager $documentManager,
        DitRepository $ditRepository,
        HistoriqueOperationService $historiqueOperationService,
        LoggerInterface $logger,
        FormFactory $formFactory
    ) {
        $this->mapper = $mapper;
        $this->documentManager = $documentManager;
        $this->ditRepository = $ditRepository;
        $this->historiqueOperationService = $historiqueOperationService;
        $this->logger = $logger;
        $this->formFactory = $formFactory;
    }

    public function handel(FormInterface $form, PdfService $pdfService): Dit
    {
        $dto = $form->getData();

        // 0. Enrichissement du DTO avec les infos matérielles (maintenant que idMateriel est connu)
        $this->formFactory->enrichDtoWithMaterielInfo($dto);

        // 1. Mapping du DTO vers l'entité
        $dit = $this->mapper->map($dto);

        // 2. Préparation documentaire (Upload, PDF)
        $fileInfo = $this->documentManager->prepareDocuments($form, $dit, $pdfService, $dto);

        // 3. Sauvegarde de l'entité
        $this->save($dit);

        // 4. Archivage vers Docuware
        $this->documentManager->archiveToDocuware($dit, $pdfService, $fileInfo['path'], $fileInfo['name']);

        return $dit;
    }

    /**
     * Persiste l'entité en base de données avec historisation
     */
    private function save(Dit $dit): void
    {
        $success = false;
        $message = 'Enregistrement dans la base de données.';
        try {
            $this->ditRepository->add($dit, true);
            $success = true;
            $message = 'Enregistrement dans la base de données réussi.';
            $numero = $dit->getNumeroDit();
            $this->logger->info($message, ['numero' => $numero]);
        } catch (\Throwable $e) {
            $message = 'Erreur lors de l\'enregistrement dans la base de données : ' . $e->getMessage();
            $this->logger->error($message, ['numero' => $numero, 'exception' => $e]);
            throw $e;
        } finally {
            $this->historiqueOperationService->enregistrer(
                $numero,
                TypeOperationConstants::TYPE_OPERATION_DB_SAVE_NAME,
                TypeDocumentConstants::TYPE_DOCUMENT_DIT_CODE,
                $success,
                $message
            );
        }
    }
}
