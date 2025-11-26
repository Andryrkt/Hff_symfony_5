<?php

namespace App\Service\Hf\Rh\Dom;

use Psr\Log\LoggerInterface;
use App\Entity\Hf\Rh\Dom\Dom;
use App\Dto\Hf\Rh\Dom\SecondFormDto;
use App\Factory\Hf\Rh\Dom\DomFactory;
use Symfony\Component\Form\FormInterface;
use App\Entity\Hf\Rh\Dom\SousTypeDocument;
use App\Repository\Hf\Rh\Dom\DomRepository;
use App\Service\Utils\Fichier\UploderFileService;
use App\Service\Utils\Fichier\TraitementDeFichier;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Constants\Admin\Historisation\TypeDocumentConstants;
use App\Constants\Admin\Historisation\TypeOperationConstants;
use App\Service\Historique_operation\HistoriqueOperationService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class DomCreationHandler
{
    private DomFactory $domFactory;
    private DomRepository $domRepository;
    private HistoriqueOperationService $historiqueOperationService;
    private LoggerInterface $logger;
    private ParameterBagInterface $params;

    public function __construct(
        DomFactory $domFactory,
        DomRepository $domRepository,
        HistoriqueOperationService $historiqueOperationService,
        LoggerInterface $domSecondFormLogger,
        ParameterBagInterface $params
    ) {
        $this->domFactory = $domFactory;
        $this->domRepository = $domRepository;
        $this->historiqueOperationService = $historiqueOperationService;
        $this->logger = $domSecondFormLogger;
        $this->params = $params;
    }

    public function handle(FormInterface $form, DomPdfService $pdfService): Dom
    {
        /** @var SecondFormDto $secondFormDto */
        $secondFormDto = $form->getData();
        $dom = $this->domFactory->create($secondFormDto);

        $this->validateDom($dom);
        $this->saveDomWithFiles($form, $dom, $pdfService, $secondFormDto);

        return $dom;
    }

    private function validateDom(Dom $dom): void
    {
        $typeMission = $dom->getSousTypeDocument()->getCodeSousType();
        $isComplementOrTropPercu = in_array($typeMission, [SousTypeDocument::CODE_COMPLEMENT, SousTypeDocument::CODE_TROP_PERCU]);

        if (!$isComplementOrTropPercu && $this->hasExistingMissionOnDates($dom->getMatricule(), $dom->getDateDebut(), $dom->getDateFin())) {
            throw new \LogicException(sprintf(
                '%s %s %s a déjà une mission enregistrée sur ces dates, veuillez vérifier.',
                $dom->getMatricule(),
                $dom->getNom(),
                $dom->getPrenom()
            ));
        }

        $isFraisExceptionnel = $typeMission === SousTypeDocument::CODE_FRAIS_EXCEPTIONNEL;
        $totalGeneral = (int) str_replace('.', '', $dom->getTotalGeneralPayer());
        $isAmountValid = $totalGeneral <= 500000;

        if (!$isFraisExceptionnel && !$isAmountValid) {
            throw new \LogicException("Assurez-vous que le Montant Total est inférieur à 500.000 Ar.");
        }
    }

    private function saveDomWithFiles(FormInterface $form, Dom $dom, DomPdfService $pdfService, SecondFormDto $secondFormDto): void
    {
        $this->processFiles($form, $dom, $pdfService, $secondFormDto);

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

    private function processFiles(FormInterface $form, Dom $dom, DomPdfService $pdfService, SecondFormDto $secondFormDto): void
    {
        [$uploadedFilesPaths, $uploadedFileNames, $finalPdfPath, $finalPdfName] = $this->saveUploadedFiles($form, $dom);

        $pdfService->genererPDF($secondFormDto, $finalPdfPath);

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


        if (!empty($uploadedFileNames)) {
            $dom->setPieceJoint01($uploadedFileNames[0] ?? null);
            $dom->setPieceJoint02($uploadedFileNames[1] ?? null);
        }

        $this->copyToDocuware($dom, $pdfService, $finalPdfPath, $finalPdfName);
    }

    private function copyToDocuware(Dom $dom, DomPdfService $pdfService, string  $finalPdfPath, string  $finalPdfName): void
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
                'generer_nom_callback' => function (
                    UploadedFile $file,
                    int $index
                ) use ($nameGenerator, $numDom, $codeAgenceServiceUser) {
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

    private function hasExistingMissionOnDates(string $matricule, \DateTimeInterface $dateDebutInput, \DateTimeInterface $dateFinInput): bool
    {
        $existingMissionsDates = $this->domRepository->getInfoDOMMatrSelet($matricule);

        if (empty($existingMissionsDates)) {
            return false;
        }

        foreach ($existingMissionsDates as $missionDates) {
            $dateDebut = new \DateTime($missionDates['Date_Debut']);
            $dateFin = new \DateTime($missionDates['Date_Fin']);

            if (($dateDebutInput <= $dateFin) && ($dateDebut <= $dateFinInput)) {
                return true;
            }
        }

        return false;
    }
}
