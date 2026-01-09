<?php

namespace App\Service\Hf\Materiel\Badm;

use Psr\Log\LoggerInterface;
use App\Entity\Hf\Materiel\Badm\Badm;
use Symfony\Component\Form\FormInterface;
use App\Dto\Hf\Materiel\Badm\SecondFormDto;
use App\Mapper\Hf\Materiel\Badm\BadmMapper;
use App\Service\Utils\Fichier\UploderFileService;
use App\Service\Utils\Fichier\TraitementDeFichier;
use App\Repository\Hf\Materiel\Badm\BadmRepository;
use App\Service\Hf\Rh\Dom\DomGenerateFileNameService;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Constants\Admin\Historisation\TypeDocumentConstants;
use App\Constants\Admin\Historisation\TypeOperationConstants;
use App\Service\Historique_operation\HistoriqueOperationService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class BadmCreationHandler
{
    private BadmMapper $badmMapper;
    private BadmRepository $badmRepository;
    private HistoriqueOperationService $historiqueOperationService;
    private LoggerInterface $logger;
    private ParameterBagInterface $params;

    public function __construct(
        BadmMapper $badmMapper,
        BadmRepository $badmRepository,
        HistoriqueOperationService $historiqueOperationService,
        LoggerInterface $badmCreationLogger,
        ParameterBagInterface $params
    ) {
        $this->badmMapper = $badmMapper;
        $this->badmRepository = $badmRepository;
        $this->historiqueOperationService = $historiqueOperationService;
        $this->logger = $badmCreationLogger;
        $this->params = $params;
    }
    public function handle(FormInterface $form, BadmPdfService $pdfService): Badm
    {
        /** @var SecondFormDto $secondFormDto */
        $secondFormDto = $form->getData();

        $badm = $this->badmMapper->map(new Badm(), $secondFormDto);

        $this->saveWithFiles($form, $badm, $pdfService, $secondFormDto);
        return $badm;
    }

    private function saveWithFiles(FormInterface $form, Badm $badm, BadmPdfService $pdfService, SecondFormDto $secondFormDto): void
    {
        $this->processFiles($form, $badm, $pdfService, $secondFormDto);

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

    private function processFiles(FormInterface $form, Badm $badm, BadmPdfService $pdfService, SecondFormDto $secondFormDto): void
    {
        [$uploadedFilesPaths, $uploadedFileNames, $finalPdfPath, $finalPdfName] = $this->saveUploadedFiles($form, $badm);

        $pdfService->genererPDF($secondFormDto, $finalPdfPath);

        $success = false;
        $message = 'Fusion des fichiers.';
        try {
            $fileProcessor = new TraitementDeFichier();
            $filesToMerge = $fileProcessor->insertFileAtPosition($uploadedFilesPaths, $finalPdfPath, 0);

            $fileProcessor->fusionFichers($filesToMerge, $finalPdfPath);
            $success = true;
            $message = 'Fusion des fichiers réussie.';
            $this->logger->info($message, ['numero_badm' => $badm->getNumeroBadm()]);
        } catch (\Throwable $e) {
            $message = 'Erreur lors de la fusion des fichiers : ' . $e->getMessage();
            $this->logger->error($message, ['numero_badm' => $badm->getNumeroBadm(), 'exception' => $e]);
            throw $e;
        } finally {
            $this->historiqueOperationService->enregistrer(
                $badm->getNumeroBadm(),
                TypeOperationConstants::TYPE_OPERATION_FILE_MERGE_NAME,
                TypeDocumentConstants::TYPE_DOCUMENT_BADM_CODE,
                $success,
                $message
            );
        }


        if (!empty($uploadedFileNames)) {
            $badm->setNomImage($uploadedFileNames[0] ?? null);
            $badm->setNomFichier($uploadedFileNames[1] ?? null);
        }

        $this->copyToDocuware($badm, $pdfService, $finalPdfPath, $finalPdfName);
    }

    private function copyToDocuware(Badm $badm, BadmPdfService $pdfService, string  $finalPdfPath, string  $finalPdfName): void
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
                'generer_nom_callback' => function (
                    UploadedFile $file,
                    int $index
                ) use ($nameGenerator, $numBadm, $codeAgenceServiceUser) {
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
