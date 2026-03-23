<?php

namespace App\Service\Hf\Atelier\Dit;

use Psr\Log\LoggerInterface;
use App\Entity\Hf\Atelier\Dit\Dit;
use Symfony\Component\Form\FormInterface;
use App\Service\Utils\Fichier\UploderFileService;
use App\Service\Hf\Atelier\Dit\GenerateFileNameService;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Constants\Admin\Historisation\TypeDocumentConstants;
use App\Constants\Admin\Historisation\TypeOperationConstants;
use App\Service\Historique_operation\HistoriqueOperationService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class DocumentManager
{
    private ParameterBagInterface $params;
    private HistoriqueOperationService $historiqueOperationService;
    private LoggerInterface $logger;


    public function __construct(ParameterBagInterface $params, HistoriqueOperationService $historiqueOperationService, LoggerInterface $ditCreationLogger)
    {
        $this->params = $params;
        $this->historiqueOperationService = $historiqueOperationService;
        $this->logger = $ditCreationLogger;
    }

    /**
     * Prépare tout le processus documentaire (Upload, PDF)
     * Retourne les informations du fichier final pour un archivage ultérieur.
     */
    public function prepareDocuments(FormInterface $form, Dit $dit, PdfService $pdfService, $dto): array
    {
        // 1. Sauvegarde des fichiers uploadés
        [$uploadedFilesPaths, $uploadedFileNames, $finalPdfPath, $finalPdfName] = $this->saveUploadedFiles($form, $dit);

        // 2. Génération du PDF principal (page de garde)
        $pdfService->genererPDF($dto, $finalPdfPath);

        // 3. Mise à jour des noms de fichiers dans l'entité
        if (!empty($uploadedFileNames)) {
            // --------------- Piece Joint ----------------------
            $dit->setPieceJoint01($uploadedFileNames[0] ?? null);
            $dit->setPieceJoint02($uploadedFileNames[1] ?? null);
            $dit->setPieceJoint03($uploadedFileNames[2] ?? null);
        }

        return [
            'path' => $finalPdfPath,
            'name' => $finalPdfName
        ];
    }

    /**
     * Archive le document vers Docuware
     */
    public function archiveToDocuware(Dit $dit, PdfService $pdfService, string $finalPdfPath, string $finalPdfName): void
    {
        $numero = $dit->getNumeroDit();
        $docuwarePath = $this->params->get('docuware_directory') . '/dit/' . $numero . '/' . $finalPdfName;

        $success = false;
        $message = 'Copie vers Docuware.';
        try {
            $pdfService->copyToDW($docuwarePath, $finalPdfPath);
            $success = true;
            $message = 'Copie vers Docuware réussie.';
            $this->logger->info($message, ['numero' => $numero]);
        } catch (\Throwable $e) {
            $message = 'Erreur lors de la copie vers Docuware : ' . $e->getMessage();
            $this->logger->error($message, ['numero' => $numero, 'exception' => $e]);
            throw $e;
        } finally {
            $this->historiqueOperationService->enregistrer(
                $numero,
                TypeOperationConstants::TYPE_OPERATION_DW_COPY_NAME,
                TypeDocumentConstants::TYPE_DOCUMENT_DIT_CODE,
                $success,
                $message
            );
        }
    }

    private function saveUploadedFiles(FormInterface $form, Dit $dit): array
    {
        $numero = $dit->getNumeroDit();
        $codeAgenceEmetteur = $dit->getAgenceEmetteurId() ? $dit->getAgenceEmetteurId()->getCode() : 'UNKNOWN';
        $codeServiceEmetteur = $dit->getServiceEmetteurId() ? $dit->getServiceEmetteurId()->getCode() : 'UNKNOWN';
        $codeAgenceServiceUser = $codeAgenceEmetteur . $codeServiceEmetteur;

        $nameGenerator = new GenerateFileNameService();
        $mainPath = $this->params->get('documents_directory') . '/dit';
        $uploader = new UploderFileService($mainPath, $nameGenerator);
        $path = $mainPath . '/' . $numero . '/';

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
                'generer_nom_callback' => function (UploadedFile $file, int $index) use ($nameGenerator, $numero, $codeAgenceServiceUser) {
                    return $nameGenerator->generateFileUplodeName($file, $numero, $codeAgenceServiceUser, $index);
                }
            ]);
            $success = true;
            $message = 'Upload des fichiers réussi : ' . (empty($uploadedFileNames) ? 'aucun fichier' : implode(', ', $uploadedFileNames));
            $this->logger->info($message, ['numero' => $numero]);
        } catch (\Throwable $e) {
            $message = 'Erreur lors de l\'upload des fichiers : ' . $e->getMessage();
            $this->logger->error($message, ['numero' => $numero, 'exception' => $e]);
            throw $e;
        } finally {
            $this->historiqueOperationService->enregistrer(
                $numero,
                TypeOperationConstants::TYPE_OPERATION_FILE_UPLOAD_NAME,
                TypeDocumentConstants::TYPE_DOCUMENT_DIT_CODE,
                $success,
                $message
            );
        }

        $finalPdfName = $nameGenerator->generateMainName($numero, $codeAgenceServiceUser);
        $finalPdfPath = $path . $finalPdfName;

        return [$uploadedFilesPaths, $uploadedFileNames, $finalPdfPath, $finalPdfName];
    }
}
