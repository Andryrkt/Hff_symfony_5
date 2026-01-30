<?php

namespace App\Service\Hf\Materiel\Badm;

use Psr\Log\LoggerInterface;
use App\Entity\Hf\Materiel\Badm\Badm;
use Symfony\Component\Form\FormInterface;
use App\Dto\Hf\Materiel\Badm\SecondFormDto;
use App\Mapper\Hf\Materiel\Badm\BadmMapper;
use App\Repository\Hf\Materiel\Badm\BadmRepository;
use App\Constants\Admin\Historisation\TypeDocumentConstants;
use App\Constants\Admin\Historisation\TypeOperationConstants;
use App\Service\Historique_operation\HistoriqueOperationService;

class BadmCreationHandler
{
    private BadmMapper $badmMapper;
    private BadmRepository $badmRepository;
    private BadmDocumentManager $documentManager;
    private HistoriqueOperationService $historiqueOperationService;
    private LoggerInterface $logger;

    public function __construct(
        BadmMapper $badmMapper,
        BadmRepository $badmRepository,
        BadmDocumentManager $documentManager,
        HistoriqueOperationService $historiqueOperationService,
        LoggerInterface $badmCreationLogger
    ) {
        $this->badmMapper = $badmMapper;
        $this->badmRepository = $badmRepository;
        $this->documentManager = $documentManager;
        $this->historiqueOperationService = $historiqueOperationService;
        $this->logger = $badmCreationLogger;
    }

    /**
     * Orchestre la création d'un Bon d'Approvisionnement Demande Matériel (BADM)
     */
    public function handle(FormInterface $form, BadmPdfService $pdfService): Badm
    {
        /** @var SecondFormDto $secondFormDto */
        $secondFormDto = $form->getData();

        // 1. Mapping du DTO vers l'entité
        $badm = $this->badmMapper->map(new Badm(), $secondFormDto);

        // 2. Préparation documentaire (Upload, PDF)
        $fileInfo = $this->documentManager->prepareDocuments($form, $badm, $pdfService, $secondFormDto);

        // 3. Sauvegarde finale en base de données
        $this->save($badm);

        // 4. Archivage vers Docuware
        $this->documentManager->archiveToDocuware($badm, $pdfService, $fileInfo['path'], $fileInfo['name']);

        return $badm;
    }

    /**
     * Persiste l'entité en base de données avec historisation
     */
    private function save(Badm $badm): void
    {
        $success = false;
        $message = 'Enregistrement dans la base de données.';
        try {
            $this->badmRepository->add($badm, true);
            $success = true;
            $message = 'Enregistrement dans la base de données réussi.';
            $this->logger->info($message, ['numero_badm' => $badm->getNumeroBadm()]);
        } catch (\Throwable $e) {
            $message = 'Erreur lors de l\'enregistrement dans la base de données : ' . $e->getMessage();
            $this->logger->error($message, ['numero_badm' => $badm->getNumeroBadm(), 'exception' => $e]);
            throw $e;
        } finally {
            $this->historiqueOperationService->enregistrer(
                $badm->getNumeroBadm(),
                TypeOperationConstants::TYPE_OPERATION_DB_SAVE_NAME,
                TypeDocumentConstants::TYPE_DOCUMENT_BADM_CODE,
                $success,
                $message
            );
        }
    }
}
