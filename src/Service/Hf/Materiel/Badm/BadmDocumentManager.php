<?php

namespace App\Service\Hf\Materiel\Badm;

use App\Entity\Hf\Materiel\Badm\Badm;
use App\Dto\Hf\Materiel\Badm\SecondFormDto;
use Symfony\Component\Form\FormInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Service\Utils\Fichier\UploderFileService;
use App\Service\Utils\Fichier\TraitementDeFichier;
use App\Service\Hf\Rh\Dom\DomGenerateFileNameService;
use App\Constants\Admin\Historisation\TypeDocumentConstants;
use App\Constants\Admin\Historisation\TypeOperationConstants;
use App\Service\Historique_operation\HistoriqueOperationService;

/**
 * Service dédié à la gestion des documents liés aux Bons d'Approvisionnement Demande Matériel (BADM)
 * Gère l'upload, la génération PDF, et l'archivage Docuware.
 */
class BadmDocumentManager
{
    private ParameterBagInterface $params;
    private HistoriqueOperationService $historiqueOperationService;
    private LoggerInterface $logger;

    public function __construct(
        ParameterBagInterface $params,
        HistoriqueOperationService $historiqueOperationService,
        LoggerInterface $badmCreationLogger
    ) {
        $this->params = $params;
        $this->historiqueOperationService = $historiqueOperationService;
        $this->logger = $badmCreationLogger;
    }

    /**
     * Prépare tout le processus documentaire pour un BADM (Upload, PDF)
     * Retourne les informations du fichier final pour un archivage ultérieur.
     */
    public function prepareDocuments(FormInterface $form, Badm $badm, BadmPdfService $pdfService, SecondFormDto $secondFormDto): array
    {
        // 1. Sauvegarde des fichiers uploadés
        [$uploadedFilesPaths, $uploadedFileNames, $finalPdfPath, $finalPdfName] = $this->saveUploadedFiles($form, $badm);

        // 2. Génération du PDF principal (page de garde)
        $pdfService->genererPDF($secondFormDto, $finalPdfPath);

        // 3. Mise à jour des noms de fichiers dans l'entité
        if (!empty($uploadedFileNames)) {
            $badm->setNomImage($uploadedFileNames[0] ?? null);
            $badm->setNomFichier($uploadedFileNames[1] ?? null);
        }

        return [
            'path' => $finalPdfPath,
            'name' => $finalPdfName
        ];
    }

    /**
     * Archive le document vers Docuware
     */
    public function archiveToDocuware(Badm $badm, BadmPdfService $pdfService, string $finalPdfPath, string $finalPdfName): void
    {
        $docuwarePath = $this->params->get('docuware_directory') . '/BADM/' . $finalPdfName;

        $success = false;
        $message = 'Copie vers Docuware.';
        try {
            $pdfService->copyToDW($docuwarePath, $finalPdfPath);
            $success = true;
            $message = 'Copie vers Docuware réussie.';
            $this->logger->info($message, ['numero_badm' => $badm->getNumeroBadm()]);
        } catch (\Throwable $e) {
            $message = 'Erreur lors de la copie vers Docuware : ' . $e->getMessage();
            $this->logger->error($message, ['numero_badm' => $badm->getNumeroBadm(), 'exception' => $e]);
            throw $e;
        } finally {
            $this->historiqueOperationService->enregistrer(
                $badm->getNumeroBadm(),
                TypeOperationConstants::TYPE_OPERATION_DW_COPY_NAME,
                TypeDocumentConstants::TYPE_DOCUMENT_BADM_CODE,
                $success,
                $message
            );
        }
    }

    private function saveUploadedFiles(FormInterface $form, Badm $badm): array
    {
        $numBadm = $badm->getNumeroBadm();
        $codeAgenceEmetteur = $badm->getAgenceEmetteurId() ? $badm->getAgenceEmetteurId()->getCode() : 'UNKNOWN';
        $codeServiceEmetteur = $badm->getServiceEmetteurId() ? $badm->getServiceEmetteurId()->getCode() : 'UNKNOWN';
        $codeAgenceServiceUser = $codeAgenceEmetteur . $codeServiceEmetteur;

        $nameGenerator = new DomGenerateFileNameService();
        $mainPath = $this->params->get('documents_directory') . '/badm';
        $uploader = new UploderFileService($mainPath, $nameGenerator);
        $path = $mainPath . '/' . $numBadm . '/';

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
                'generer_nom_callback' => function (UploadedFile $file, int $index) use ($nameGenerator, $numBadm, $codeAgenceServiceUser) {
                    return $nameGenerator->generateFileUplodeName($file, $numBadm, $codeAgenceServiceUser, $index);
                }
            ]);
            $success = true;
            $message = 'Upload des fichiers réussi : ' . (empty($uploadedFileNames) ? 'aucun fichier' : implode(', ', $uploadedFileNames));
            $this->logger->info($message, ['numero_badm' => $badm->getNumeroBadm()]);
        } catch (\Throwable $e) {
            $message = 'Erreur lors de l\'upload des fichiers : ' . $e->getMessage();
            $this->logger->error($message, ['numero_badm' => $badm->getNumeroBadm(), 'exception' => $e]);
            throw $e;
        } finally {
            $this->historiqueOperationService->enregistrer(
                $badm->getNumeroBadm(),
                TypeOperationConstants::TYPE_OPERATION_FILE_UPLOAD_NAME,
                TypeDocumentConstants::TYPE_DOCUMENT_BADM_CODE,
                $success,
                $message
            );
        }

        $finalPdfName = $nameGenerator->generateMainName($numBadm, $codeAgenceServiceUser);
        $finalPdfPath = $path . $finalPdfName;

        return [$uploadedFilesPaths, $uploadedFileNames, $finalPdfPath, $finalPdfName];
    }
}
