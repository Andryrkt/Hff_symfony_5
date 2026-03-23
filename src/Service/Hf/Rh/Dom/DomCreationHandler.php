<?php

namespace App\Service\Hf\Rh\Dom;

use Psr\Log\LoggerInterface;

use App\Entity\Hf\Rh\Dom\Dom;
use App\Dto\Hf\Rh\Dom\SecondFormDto;
use App\Factory\Hf\Rh\Dom\DomFactory;
use App\Service\Hf\Rh\Dom\DomValidator;
use App\Service\Hf\Rh\Dom\DomDocumentManager;
use Symfony\Component\Form\FormInterface;
use App\Repository\Hf\Rh\Dom\DomRepository;
use App\Constants\Admin\Historisation\TypeDocumentConstants;
use App\Constants\Admin\Historisation\TypeOperationConstants;
use App\Service\Historique_operation\HistoriqueOperationService;

class DomCreationHandler
{
    private DomFactory $domFactory;
    private DomRepository $domRepository;
    private DomValidator $validator;
    private DomDocumentManager $documentManager;
    private HistoriqueOperationService $historiqueOperationService;
    private LoggerInterface $logger;

    public function __construct(
        DomFactory $domFactory,
        DomRepository $domRepository,
        DomValidator $validator,
        DomDocumentManager $documentManager,
        HistoriqueOperationService $historiqueOperationService,
        LoggerInterface $domSecondFormLogger
    ) {
        $this->domFactory = $domFactory;
        $this->domRepository = $domRepository;
        $this->validator = $validator;
        $this->documentManager = $documentManager;
        $this->historiqueOperationService = $historiqueOperationService;
        $this->logger = $domSecondFormLogger;
    }

    /**
     * Orchestre la création d'un ordre de mission (DOM)
     */
    public function handle(FormInterface $form, DomPdfService $pdfService): Dom
    {
        /** @var SecondFormDto $secondFormDto */
        $secondFormDto = $form->getData();

        // 1. Création de l'entité
        $dom = $this->domFactory->create($secondFormDto);

        // 2. Validation métier
        $this->validator->validate($dom);

        // 3. Préparation documentaire (Upload, PDF, Fusion)
        // Les noms des fichiers sont mis à jour dans l'objet $dom ici
        $fileInfo = $this->documentManager->prepareDocuments($form, $dom, $pdfService, $secondFormDto);

        // 4. Sauvegarde finale en base de données (inclut les noms des fichiers)
        $this->save($dom);

        // 5. Archivage vers Docuware
        $this->documentManager->archiveToDocuware($dom, $pdfService, $fileInfo['path'], $fileInfo['name']);

        return $dom;
    }

    /**
     * Persiste l'entité en base de données avec historisation
     */
    private function save(Dom $dom): void
    {
        $success = false;
        $message = 'Enregistrement dans la base de données.';
        try {
            $this->domRepository->add($dom, true);
            $success = true;
            $message = 'Enregistrement dans la base de données réussi.';
            $this->logger->info($message, ['numero_dom' => $dom->getNumeroOrdreMission()]);
        } catch (\Exception $e) {
            $message = 'Erreur lors de l\'enregistrement dans la base de données : ' . $e->getMessage();
            $this->logger->error($message, ['numero_dom' => $dom->getNumeroOrdreMission(), 'exception' => $e]);
            throw $e;
        } finally {
            $this->historiqueOperationService->enregistrer(
                $dom->getNumeroOrdreMission(),
                TypeOperationConstants::TYPE_OPERATION_DB_SAVE_NAME,
                TypeDocumentConstants::TYPE_DOCUMENT_DOM_CODE,
                $success,
                $message
            );
        }
    }
}
