<?php

namespace App\Service\Hf\Atelier\Dit\Soumission\Ors;

use App\Constants\Admin\Historisation\TypeDocumentConstants;
use App\Constants\Admin\Historisation\TypeOperationConstants;
use App\Factory\Hf\Atelier\Dit\Soumission\Ors\OrsFactory;
use App\Mapper\Hf\Atelier\Dit\Soumission\Ors\OrsMapper;
use App\Repository\Hf\Atelier\Dit\Ors\OrsRepository;
use App\Service\Historique_operation\HistoriqueOperationService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormInterface;

class CreationHandler
{
    private OrsMapper $mapper;
    private HistoriqueOperationService $historiqueOperationService;
    private LoggerInterface $logger;
    private OrsFactory $factory;
    private OrsRepository $orsRepository;
    private OrsDocumentManager $documentManager;

    public function __construct(
        OrsMapper $mapper,
        HistoriqueOperationService $historiqueOperationService,
        LoggerInterface $logger,
        OrsFactory $factory,
        OrsRepository $orsRepository,
        OrsDocumentManager $documentManager
    ) {
        $this->mapper = $mapper;
        $this->historiqueOperationService = $historiqueOperationService;
        $this->logger = $logger;
        $this->factory = $factory;
        $this->orsRepository = $orsRepository;
        $this->documentManager = $documentManager;
    }

    public function handel(
        FormInterface $form,
        OrsPdfService $pdfService
    ) {
        $dto = $form->getData();

        // 0. Enrichissement du DTO avec les infos matérielles (maintenant que idMateriel est connu)
        $this->factory->enrichissementDto($dto);

        // 1. Mapping du DTO vers un tableau d'entités
        $ors = $this->mapper->map($dto);

        // 2. Préparation documentaire (Upload, PDF)
        $fileInfo = $this->documentManager->prepareDocuments($form, $pdfService, $dto);

        // 3. Sauvegarde de l'entité
        $this->save($ors);

        // 4. Archivage vers Docuware
        // $this->documentManager->archiveToDocuware($dit, $pdfService, $fileInfo['path'], $fileInfo['name']);

        return $ors;
    }

    /**
     * Persiste l'entité en base de données avec historisation
     */
    private function save(array $ors): void
    {
        $success = false;
        $message = 'Enregistrement dans la base de données.';
        $numero = !empty($ors) ? $ors[0]->getNumeroOr() : 'non-défini';

        try {
            $this->orsRepository->add($ors, true);
            $success = true;
            $message = 'Enregistrement dans la base de données réussi.';
            $this->logger->info($message, ['numero' => $numero]);
        } catch (\Throwable $e) {
            $message = 'Erreur lors de l\'enregistrement dans la base de données : ' . $e->getMessage();
            $this->logger->error($message, ['numero' => $numero, 'exception' => $e]);
            throw $e;
        } finally {
            $this->historiqueOperationService->enregistrer(
                (string) $numero,
                TypeOperationConstants::TYPE_OPERATION_DB_SAVE_NAME,
                TypeDocumentConstants::TYPE_DOCUMENT_OR_CODE,
                $success,
                $message
            );
        }
    }
}
