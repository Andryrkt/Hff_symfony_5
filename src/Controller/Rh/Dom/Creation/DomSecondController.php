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

    public function __construct(
        SecondFormDtoFactory $firstFormDtoFactory,
        DomRepository $domRepository,
        DomFactory $domFactory,
        HistoriqueOperationService $historiqueOperationService
    ) {
        $this->secondFormDtoFactory = $firstFormDtoFactory;
        $this->domRepository = $domRepository;
        $this->domFactory = $domFactory;
        $this->historiqueOperationService = $historiqueOperationService;
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
        // 1. gerer l'accés
        $this->denyAccessUnlessGranted('RH_ORDRE_MISSION_CREATE');


        // 2. recupération de session et des donées du premier formulaire
        $firstFormDto = $this->recuperationDonnerPremierFormulaire($request->getSession());
        if ($firstFormDto instanceof RedirectResponse) {
            return $firstFormDto;
        }

        // 3 . initialisation de la FirstFormDto
        $secondFormDto = $this->secondFormDtoFactory->create($firstFormDto);

        // 4. creation du formulaire
        $form = $this->createForm(SecondFormType::class, $secondFormDto);

        // 5. traitement du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // 5a. Formulaire valide : traitement métier
                $redirectResponse = $this->processValidForm($form, $pdfService);
                if ($redirectResponse) {
                    return $redirectResponse;
                }
            } else {
                // 5b. Formulaire invalide : fusion des valeurs par défaut
                $defaultsDto = $this->secondFormDtoFactory->create($firstFormDto);
                $this->mergeDefaultsIntoDto($form->getData(), $defaultsDto);
            }
        }

        // 6. rendu de toutes les agences pendant le premier chargement
        $agencesJson = $this->serealisationAgence($agenceRepository, $serializer);

        // rendu du vue
        return $this->render('rh/dom/secondForm.html.twig', [
            'form'          => $form->createView(),
            'secondFormDto' => $form->getData(),
            'agencesJson' => $agencesJson,
            'breadcrumbs' => $breadcrumbBuilder->build('dom_second_form'),
        ]);
    }

    private function processValidForm(FormInterface $form, DomPdfService $pdfService): ?RedirectResponse
    {
        /** @var SecondFormDto $secondFormDto */
        $secondFormDto = $form->getData();
        $dom = $this->domFactory->create($secondFormDto);

        $message = null;
        $success = false;

        $typeMission = $dom->getSousTypeDocument()->getCodeSousType();
        $isComplementOrTropPercu = in_array($typeMission, [SousTypeDocument::CODE_COMPLEMENT, SousTypeDocument::CODE_TROP_PERCU]);

        if (!$isComplementOrTropPercu && $this->verifierSiDateExistant($dom->getMatricule(), $dom->getDateDebut(), $dom->getDateFin())) {
            $message = sprintf(
                '%s %s %s a déjà une mission enregistrée sur ces dates, veuillez vérifier.',
                $dom->getMatricule(),
                $dom->getNom(),
                $dom->getPrenom()
            );
        } else {
            $isFraisExceptionnel = $typeMission === SousTypeDocument::CODE_FRAIS_EXCEPTIONNEL;
            $totalGeneral = (int) str_replace('.', '', $dom->getTotalGeneralPayer());
            $isAmountValid = $totalGeneral <= 500000;

            if ($isFraisExceptionnel || $isAmountValid) {
                try {
                    $this->TraitementFichierEtEnregistrementDAnsBd($form, $dom, $pdfService, $secondFormDto);
                    $success = true;
                    $message = 'La demande d\'ordre de mission a été créée avec succès.';
                } catch (\Exception $e) {
                    $message = "Une erreur est survenue lors de l'enregistrement de la demande.";
                    $this->historiqueOperationService->enregistrer(
                        $dom->getNumeroOrdreMission(),
                        'CREATION',
                        'DOM',
                        $success,
                        $message
                    );
                }
            } else {
                $message = "Assurez-vous que le Montant Total est inférieur à 500.000 Ar.";
                $this->historiqueOperationService->enregistrer(
                    $dom->getNumeroOrdreMission(),
                    'CREATION',
                    'DOM',
                    $success,
                    $message
                );
            }
        }

        $this->historiqueOperationService->enregistrer(
            $dom->getNumeroOrdreMission(),
            'CREATION',
            'DOM',
            $success,
            $message ?? 'Création de l\'ordre de mission.'
        );

        if ($success) {
            $this->addFlash('success', $message);
            return $this->redirectToRoute('doms_liste');
        } else {
            $this->addFlash('warning', $message);
            return null;
        }
    }

    private function mergeDefaultsIntoDto(SecondFormDto $submittedDto, SecondFormDto $defaultsDto): void
    {
        $submittedReflection = new \ReflectionObject($submittedDto);
        $defaultsReflection = new \ReflectionObject($defaultsDto);

        foreach ($submittedReflection->getProperties() as $prop) {
            $prop->setAccessible(true);

            $submittedValue = $prop->isInitialized($submittedDto) ? $prop->getValue($submittedDto) : null;

            if ($submittedValue === null) {
                $propName = $prop->getName();
                if ($defaultsReflection->hasProperty($propName)) {
                    $defaultProp = $defaultsReflection->getProperty($propName);
                    $defaultProp->setAccessible(true);
                    if ($defaultProp->isInitialized($defaultsDto)) {
                        $defaultValue = $defaultProp->getValue($defaultsDto);
                        $prop->setValue($submittedDto, $defaultValue);
                    }
                }
            }
        }
    }

    private function TraitementFichierEtEnregistrementDAnsBd(FormInterface $form, Dom $dom, DomPdfService $pdfService, SecondFormDto $secondFormDto)
    {
        // 3. traitement de fichier
        $this->traitementFichier($form, $dom, $pdfService, $secondFormDto);

        // 4. enregistrement  des données dans la base de donnée
        $this->domRepository->add($dom, true);
    }

    private function traitementFichier(FormInterface $form, Dom $dom, DomPdfService $pdfService, SecondFormDto $secondFormDto): void
    {
        /** 
         * 1. gestion des pieces jointes et generer le nom du fichier PDF
         * Enregistrement de fichier uploder
         * @var array $nomEtCheminFichiersEnregistrer
         * @var array $nomFichierEnregistrer 
         * @var string $nomAvecCheminFichier (page de garde)
         * @var string $nomFichier (page de garde)
         */
        [$nomEtCheminFichiersEnregistrer, $nomFichierEnregistrer, $nomAvecCheminFichier, $nomFichier] = $this->saveFileUploder($form, $dom);

        // 2. ajout des nom de fichier dans le DOM
        if (!empty($nomFichierEnregistrer)) {
            $dom->setPieceJoint01($nomAvecCheminFichier[0] ?? null);
            $dom->setPieceJoint02($nomAvecCheminFichier[1] ?? null);
        }

        // 3. creation de page de garde
        $pdfService->genererPDF($secondFormDto, $nomAvecCheminFichier);

        // 4. ajout du page de garde à la dernière position
        $traitementDeFichier = new TraitementDeFichier();
        $nomEtCheminFichiersEnregistrer = $traitementDeFichier->insertFileAtPosition($nomEtCheminFichiersEnregistrer, $nomAvecCheminFichier, 0);

        // 5. fusion du page de garde et des pieces jointes (conversion avant la fusion)
        $traitementDeFichier->fusionFichers($nomEtCheminFichiersEnregistrer, $nomAvecCheminFichier);

        // 6. copie du pdf fusioné dans DW
        $this->copyToDW($pdfService, $nomAvecCheminFichier, $nomFichier);
    }

    private function copyToDW(DomPdfService $pdfService, string  $nomAvecCheminFichier, string  $nomFichier): void
    {
        $cheminVersDw = $this->getParameter('docuware_directory') . '/ORDRE_DE_MISSION/' . $nomFichier;
        $pdfService->copyToDW($cheminVersDw, $nomAvecCheminFichier);
    }

    private function saveFileUploder(FormInterface $form, Dom $dom)
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

        /**
         * recupère les noms + chemins dans un tableau et les noms dans une autre
         * @var array $nomEtCheminFichiersEnregistrer
         * @var array $nomFichierEnregistrer
         */
        [$nomEtCheminFichiersEnregistrer, $nomFichierEnregistrer] = $uploader->getFichiers($form, [
            'repertoire' => $path,
            'generer_nom_callback' => function (
                UploadedFile $file,
                int $index
            ) use ($nameGenerator, $numDom, $codeAgenceServiceUser) {
                return $nameGenerator->generateFileUplodeName($file, $numDom, $codeAgenceServiceUser, $index);
            }
        ]);

        $nomFichier = $nameGenerator->generateMainName($numDom, $codeAgenceServiceUser);
        $nomAvecCheminFichier = $path . $nomFichier;

        return [$nomEtCheminFichiersEnregistrer, $nomFichierEnregistrer, $nomAvecCheminFichier, $nomFichier];
    }

    private function recuperationDonnerPremierFormulaire(SessionInterface $session)
    {
        /** @var FirstFormDto $firstFormDto */
        $firstFormDto = $session->get('dom_first_form_data');
        if (!$firstFormDto) {
            // Handle case where first form data is not in session, e.g., redirect to first form
            return $this->redirectToRoute('dom_first_form');
        }

        return $firstFormDto;
    }

    private function serealisationAgence(AgenceRepository $agenceRepository, SerializerInterface $serializer)
    {
        // 1. Récupérer toutes les agences avec leurs services
        $agences = $agenceRepository->findAll();

        // 2. Sérialiser les données en JSON en utilisant les groupes que nous avons définis
        $agencesJson = $serializer->serialize($agences, 'json', ['groups' => 'agence:read']);

        return $agencesJson;
    }

    private function verifierSiDateExistant(string $matricule,  $dateDebutInput, $dateFinInput): bool
    {
        $Dates = $this->domRepository->getInfoDOMMatrSelet($matricule);

        if (empty($Dates)) {
            return false; // Pas de périodes dans la base
        }

        // Convertir les dates d'entrée si elles sont en chaînes
        $dateDebutInputObj = $dateDebutInput instanceof DateTime ? $dateDebutInput : new DateTime($dateDebutInput);
        $dateFinInputObj = $dateFinInput instanceof DateTime ? $dateFinInput : new DateTime($dateFinInput);

        foreach ($Dates as $periode) {
            // Convertir les dates en objets DateTime pour faciliter la comparaison
            $dateDebut = new DateTime($periode['Date_Debut']); //date dans la base de donner
            $dateFin = new DateTime($periode['Date_Fin']); //date dans la base de donner
            $dateDebutInputObj = $dateDebutInput; // date entrer par l'utilisateur
            $dateFinInputObj = $dateFinInput; // date entrer par l'utilisateur

            // Vérifier si la date à vérifier est comprise entre la date de début et la date de fin
            if (($dateFinInputObj >= $dateDebut && $dateFinInputObj <= $dateFin) || ($dateDebutInputObj >= $dateDebut && $dateDebutInputObj <= $dateFin) || ($dateDebutInputObj === $dateFin)) { // Correction des noms de variables
                $trouve = true;

                return $trouve;
            }
        }

        return false; // Pas de chevauchement
    }
}
