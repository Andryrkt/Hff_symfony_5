<?php

namespace App\Service\Hf\Rh\Dom;

use App\Entity\Hf\Rh\Dom\Dom;
use App\Dto\Hf\Rh\Dom\SecondFormDto;
use Symfony\Component\Form\FormInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Service\Utils\Fichier\UploderFileService;
use App\Service\Utils\Fichier\TraitementDeFichier;
use App\Constants\Admin\Historisation\TypeDocumentConstants;
use App\Constants\Admin\Historisation\TypeOperationConstants;
use App\Service\Historique_operation\HistoriqueOperationService;

/**
 * Service dédié à la gestion des documents liés aux ordres de mission (DOM)
 * Gère l'upload, la génération PDF, la fusion et l'archivage Docuware.
 */
class DomDocumentManager
{
    private ParameterBagInterface $params;
    private HistoriqueOperationService $historiqueOperationService;
    private LoggerInterface $logger;

    public function __construct(
        ParameterBagInterface $params,
        HistoriqueOperationService $historiqueOperationService,
        LoggerInterface $domSecondFormLogger
    ) {
        $this->params = $params;
        $this->historiqueOperationService = $historiqueOperationService;
        $this->logger = $domSecondFormLogger;
    }

    /**
     * Prépare tout le processus documentaire pour un DOM (Upload, PDF, Fusion)
     * Retourne les informations du fichier final pour un archivage ultérieur.
     */
    public function prepareDocuments(FormInterface $form, Dom $dom, DomPdfService $pdfService, SecondFormDto $secondFormDto): array
    {
        // 1. Sauvegarde des fichiers uploadés et préparation de la fusion
        [$uploadedFilesPaths, $uploadedFileNames, $finalPdfPath, $finalPdfName] = $this->saveUploadedFiles($form, $dom);

        // 2. Génération du PDF principal
        $pdfService->genererPDF($secondFormDto, $finalPdfPath);

        // 3. Fusion des fichiers (PDF principal + pièces jointes)
        $this->mergeFiles($dom, $uploadedFilesPaths, $finalPdfPath);

        // 4. Mise à jour des noms de fichiers dans l'entité
        if (!empty($uploadedFileNames)) {
            $dom->setPieceJoint01($uploadedFileNames[0] ?? null);
            $dom->setPieceJoint02($uploadedFileNames[1] ?? null);
        }

        return [
            'path' => $finalPdfPath,
            'name' => $finalPdfName
        ];
    }

    /**
     * Archive le document vers Docuware
     */
    public function archiveToDocuware(Dom $dom, DomPdfService $pdfService, string $finalPdfPath, string $finalPdfName): void
    {
        $docuwarePath = $this->params->get('docuware_directory') . '/ORDRE_DE_MISSION/' . $finalPdfName;

        $success = false;
        $message = 'Copie vers Docuware.';
        try {
            $pdfService->copyToDW($docuwarePath, $finalPdfPath);
            $success = true;
            $message = 'Copie vers Docuware réussie.';
            $this->logger->info($message, ['numero_dom' => $dom->getNumeroOrdreMission()]);
        } catch (\Exception $e) {
            $message = 'Erreur lors de la copie vers Docuware : ' . $e->getMessage();
            $this->logger->error($message, ['numero_dom' => $dom->getNumeroOrdreMission(), 'exception' => $e]);
            throw $e;
        } finally {
            $this->historiqueOperationService->enregistrer(
                $dom->getNumeroOrdreMission(),
                TypeOperationConstants::TYPE_OPERATION_DW_COPY_NAME,
                TypeDocumentConstants::TYPE_DOCUMENT_DOM_CODE,
                $success,
                $message
            );
        }
    }

    private function mergeFiles(Dom $dom, array $uploadedFilesPaths, string $finalPdfPath): void
    {
        $success = false;
        $message = 'Fusion des fichiers.';
        try {
            $fileProcessor = new TraitementDeFichier();
            $filesToMerge = $fileProcessor->insertFileAtPosition($uploadedFilesPaths, $finalPdfPath, 0);

            $fileProcessor->fusionFichers($filesToMerge, $finalPdfPath);
            $success = true;
            $message = 'Fusion des fichiers réussie.';
            $this->logger->info($message, ['numero_dom' => $dom->getNumeroOrdreMission()]);
        } catch (\Exception $e) {
            $message = 'Erreur lors de la fusion des fichiers : ' . $e->getMessage();
            $this->logger->error($message, ['numero_dom' => $dom->getNumeroOrdreMission(), 'exception' => $e]);
            throw $e;
        } finally {
            $this->historiqueOperationService->enregistrer(
                $dom->getNumeroOrdreMission(),
                TypeOperationConstants::TYPE_OPERATION_FILE_MERGE_NAME,
                TypeDocumentConstants::TYPE_DOCUMENT_DOM_CODE,
                $success,
                $message
            );
        }
    }

    private function saveUploadedFiles(FormInterface $form, Dom $dom): array
    {
        $numDom = $dom->getNumeroOrdreMission();
        $codeAgenceServiceUser = $dom->getAgenceEmetteurId()->getCode() . '' . $dom->getServiceEmetteurId()->getCode();

        $nameGenerator = new DomGenerateFileNameService();
        $mainPath = $this->params->get('documents_directory') . '/dom';
        $uploader = new UploderFileService($mainPath, $nameGenerator);
        $path = $mainPath . '/' . $numDom . '/';

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $success = false;
        $message = 'Upload des fichiers.';
        $uploadedFilesPaths = [];
        $uploadedFileNames = [];
        try {
            [$uploadedFilesPaths, $uploadedFileNames] = $uploader->getFichiers($form, [
                'repertoire' => $path,
                'generer_nom_callback' => function (UploadedFile $file, int $index) use ($nameGenerator, $numDom, $codeAgenceServiceUser) {
                    return $nameGenerator->generateFileUplodeName($file, $numDom, $codeAgenceServiceUser, $index);
                }
            ]);
            $success = true;
            $message = 'Upload des fichiers réussi : ' . (empty($uploadedFileNames) ? 'aucun fichier' : implode(', ', $uploadedFileNames));
            $this->logger->info($message, ['numero_dom' => $dom->getNumeroOrdreMission()]);
        } catch (\Exception $e) {
            $message = 'Erreur lors de l\'upload des fichiers : ' . $e->getMessage();
            $this->logger->error($message, ['numero_dom' => $dom->getNumeroOrdreMission(), 'exception' => $e]);
            throw $e;
        } finally {
            $this->historiqueOperationService->enregistrer(
                $dom->getNumeroOrdreMission(),
                TypeOperationConstants::TYPE_OPERATION_FILE_UPLOAD_NAME,
                TypeDocumentConstants::TYPE_DOCUMENT_DOM_CODE,
                $success,
                $message
            );
        }

        $finalPdfName = $nameGenerator->generateMainName($numDom, $codeAgenceServiceUser);
        $finalPdfPath = $path . $finalPdfName;

        return [$uploadedFilesPaths, $uploadedFileNames, $finalPdfPath, $finalPdfName];
    }
}
