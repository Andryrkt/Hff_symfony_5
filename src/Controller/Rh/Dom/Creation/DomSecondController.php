<?php

namespace App\Controller\Rh\Dom\Creation;

use DateTime;
use App\Entity\Rh\Dom\Dom;
use App\Dto\Rh\Dom\FirstFormDto;
use App\Dto\Rh\Dom\SecondFormDto;
use App\Factory\Rh\Dom\DomFactory;
use App\Form\Rh\Dom\SecondFormType;
use App\Service\Rh\Dom\DomPdfService;
use App\Entity\Rh\Dom\SousTypeDocument;
use App\Repository\Rh\Dom\DomRepository;
use Symfony\Component\Form\FormInterface;
use App\Factory\Rh\Dom\SecondFormDtoFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Utils\Fichier\UploderFileService;
use App\Service\Rh\Dom\DomGenerateFileNameService;
use App\Service\Utils\Fichier\TraitementDeFichier;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Repository\Admin\AgenceService\AgenceRepository;
use App\Service\Navigation\ContextAwareBreadcrumbBuilder;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Service\Historique_operation\HistoriqueOperationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/rh/ordre-de-mission")
 */
class DomSecondController extends AbstractController
{
    private SecondFormDtoFactory $secondFormDtoFactory;
    private DomRepository $domRepository;
    private DomFactory $domFactory;
    private HistoriqueOperationService $historiqueOperationService;
    private LoggerInterface $logger;

    public function __construct(
        SecondFormDtoFactory $secondFormDtoFactory,
        DomRepository $domRepository,
        DomFactory $domFactory,
        HistoriqueOperationService $historiqueOperationService,
        LoggerInterface $domSecondFormLogger
    ) {
        $this->secondFormDtoFactory = $secondFormDtoFactory;
        $this->domRepository = $domRepository;
        $this->domFactory = $domFactory;
        $this->historiqueOperationService = $historiqueOperationService;
        $this->logger = $domSecondFormLogger;
    }

    /**
     * @Route("/dom-second-form", name="dom_second_form")
     */
    public function secondForm(
        Request $request,
        AgenceRepository $agenceRepository,
        SerializerInterface $serializer,
        DomPdfService $pdfService,
        ContextAwareBreadcrumbBuilder $breadcrumbBuilder
    ) {
        $this->logger->info('Affichage du second formulaire de création de DOM.');
        $this->denyAccessUnlessGranted('RH_ORDRE_MISSION_CREATE');

        $firstFormDto = $this->getFirstFormDataFromSession($request->getSession());
        if ($firstFormDto instanceof RedirectResponse) {
            $this->logger->warning('Données du premier formulaire non trouvées en session.');
            return $firstFormDto;
        }

        $secondFormDto = $this->secondFormDtoFactory->create($firstFormDto);
        $form = $this->createForm(SecondFormType::class, $secondFormDto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->logger->info('Second formulaire soumis et valide.');
            $this->logger->debug('Données du formulaire', ['data' => $form->getData()]);
            $redirectResponse = $this->processValidForm($form, $pdfService);
            if ($redirectResponse) {
                return $redirectResponse;
            }
        }

        return $this->render('rh/dom/secondForm.html.twig', [
            'form'          => $form->createView(),
            'secondFormDto' => $form->getData(),
            'agencesJson'   => $this->serializeAgences($agenceRepository, $serializer),
            'breadcrumbs'   => $breadcrumbBuilder->build('dom_second_form'),
        ]);
    }

    private function processValidForm(FormInterface $form, DomPdfService $pdfService): ?RedirectResponse
    {
        /** @var SecondFormDto $secondFormDto */
        $secondFormDto = $form->getData();
        $dom = $this->domFactory->create($secondFormDto);

        $message = 'Création de l\'ordre de mission.';
        $success = false;

        try {
            $this->validateDom($dom);
            $this->saveDomWithFiles($form, $dom, $pdfService, $secondFormDto);
            $success = true;
            $message = 'La demande d\'ordre de mission a été créée avec succès.';
            $this->logger->info($message, ['numero_dom' => $dom->getNumeroOrdreMission()]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $this->logger->error(
                'Erreur lors de la création de l\'ordre de mission : ' . $message,
                ['numero_dom' => $dom->getNumeroOrdreMission(), 'exception' => $e]
            );
        }

        $this->historiqueOperationService->enregistrer(
            $dom->getNumeroOrdreMission(),
            'CREATION',
            'DOM',
            $success,
            $message
        );

        if ($success) {
            $this->addFlash('success', $message);
            return $this->redirectToRoute('doms_liste');
        }

        $this->addFlash('warning', $message);
        return null;
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
                'DB_SAVE',
                'DOM',
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
                'FILE_MERGE',
                'DOM',
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
        $docuwarePath = $this->getParameter('docuware_directory') . '/ORDRE_DE_MISSION/' . $finalPdfName;
        
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
                'DW_COPY',
                'DOM',
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
        $mainPath = $this->getParameter('documents_directory') . '/dom';
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
                'FILE_UPLOAD',
                'DOM',
                $success,
                $message
            );
        }

        $finalPdfName = $nameGenerator->generateMainName($numDom, $codeAgenceServiceUser);
        $finalPdfPath = $path . $finalPdfName;

        return [$uploadedFilesPaths, $uploadedFileNames, $finalPdfPath, $finalPdfName];
    }

    /**
     * return FirstFormDto|RedirectResponse
     */
    private function getFirstFormDataFromSession(SessionInterface $session)
    {
        $firstFormDto = $session->get('dom_first_form_data');
        if (!$firstFormDto) {
            $this->addFlash('warning', 'La session a expiré, veuillez recommencer.');
            return $this->redirectToRoute('dom_first_form');
        }

        return $firstFormDto;
    }

    private function serializeAgences(AgenceRepository $agenceRepository, SerializerInterface $serializer): string
    {
        $agences = $agenceRepository->findAll();
        return $serializer->serialize($agences, 'json', ['groups' => 'agence:read']);
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

            // Two ranges [start1, end1] and [start2, end2] overlap if (start1 <= end2) and (start2 <= end1).
            if (($dateDebutInput <= $dateFin) && ($dateDebut <= $dateFinInput)) {
                return true;
            }
        }

        return false;
    }
}
