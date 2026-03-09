<?php

namespace App\Service\Hf\Atelier\Dit\Soumission\Ors;

use App\Constants\Admin\Historisation\TypeDocumentConstants;
use App\Constants\Admin\Historisation\TypeOperationConstants;
use App\Dto\Hf\Atelier\Dit\Soumission\Ors\OrsDto;
use App\Service\Hf\Atelier\Dit\Soumission\Ors\OrsGenerateFileNameService;
use App\Service\Historique_operation\HistoriqueOperationService;
use App\Service\Utils\Fichier\UploderFileService;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class OrsDocumentManager
{
    private ParameterBagInterface $params;
    private HistoriqueOperationService $historiqueOperationService;
    private LoggerInterface $logger;


    public function __construct(
        ParameterBagInterface $params,
        HistoriqueOperationService $historiqueOperationService,
        LoggerInterface $ditCreationLogger
    ) {
        $this->params = $params;
        $this->historiqueOperationService = $historiqueOperationService;
        $this->logger = $ditCreationLogger;
    }

    /**
     * Prépare tout le processus documentaire (Upload, PDF)
     * Retourne les informations du fichier final pour un archivage ultérieur.
     */
    public function prepareDocuments(FormInterface $form, OrsPdfService $pdfService, OrsDto $dto): array
    {
        // 1. Sauvegarde des fichiers uploadés
        [$uploadedFilesPaths, $uploadedFileNames, $finalPdfPath, $finalPdfName] = $this->saveUploadedFiles($form, $dto);

        // 2. Génération du PDF principal (page de garde)
        $pdfService->genererPDF($dto, $finalPdfPath);

        return [
            'path' => $finalPdfPath,
            'name' => $finalPdfName
        ];
    }

    private function saveUploadedFiles(FormInterface $form, OrsDto $orsDto): array
    {
        $numero = $orsDto->numeroDit;
        $numeroOr = $orsDto->numeroOr;
        $numeroVersion = $orsDto->numeroVersion;

        $nameGenerator = new OrsGenerateFileNameService();
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
                'generer_nom_callback' => function (UploadedFile $file, int $index) use ($nameGenerator, $numero, $numeroOr, $numeroVersion) {
                    return $nameGenerator->generateFileUplodeName($file, $numero, $numeroOr, $numeroVersion, $index);
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
                TypeDocumentConstants::TYPE_DOCUMENT_OR_CODE,
                $success,
                $message
            );
        }

        $finalPdfName = $nameGenerator->generateMainName($numero, $numeroOr, $numeroVersion);
        $finalPdfPath = $path . $finalPdfName;

        return [$uploadedFilesPaths, $uploadedFileNames, $finalPdfPath, $finalPdfName];
    }
}
