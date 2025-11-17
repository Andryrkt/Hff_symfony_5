<?php

namespace App\Controller\Rh\Dom;

use App\Entity\Rh\Dom\Dom;
use App\Dto\Rh\Dom\FirstFormDto;
use App\Dto\Rh\Dom\SecondFormDto;
use App\Factory\Rh\Dom\DomFactory;
use App\Form\Rh\Dom\SecondFormType;
use App\Service\Rh\Dom\DomPdfService;
use App\Repository\Rh\Dom\DomRepository;
use Symfony\Component\Form\FormInterface;
use App\Factory\Rh\Dom\SecondFormDtoFactory;
use App\Service\Navigation\ContextAwareBreadcrumbBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Utils\Fichier\UploderFileService;
use App\Service\Rh\Dom\DomGenerateFileNameService;
use App\Service\Utils\Fichier\TraitementDeFichier;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Repository\Admin\AgenceService\AgenceRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/rh/ordre-de-mission")
 */
class DomSecondController extends AbstractController
{
    private SecondFormDtoFactory $secondFormDtoFactory;
    private DomRepository $domRepository;
    private DomFactory $domFactory;

    public function __construct(
        SecondFormDtoFactory $firstFormDtoFactory,
        DomRepository $domRepository,
        DomFactory $domFactory
    ) {
        $this->secondFormDtoFactory = $firstFormDtoFactory;
        $this->domRepository = $domRepository;
        $this->domFactory = $domFactory;
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

        // recuperation de session
        $session = $request->getSession();

        // 2. recupération des donées du premier formulaire
        $firstFormDto = $this->recuperationDonnerPremierFormulaire($session);

        // 3 . initialisation de la FirstFormDto
        $secondFormDto = $this->secondFormDtoFactory->create($firstFormDto);

        // 4. creation du formulaire
        $form = $this->createForm(SecondFormType::class, $secondFormDto);

        // 5. traitement du formulaire
        $this->traitementFormulaire($form, $request, $pdfService);

        // 6. rendu de toutes les agences pendant le premier chargement
        $agencesJson = $this->serealisationAgence($agenceRepository, $serializer);

        // rendu du vue
        return $this->render('rh/dom/secondForm.html.twig', [
            'form'          => $form->createView(),
            'secondFormDto' => $secondFormDto,
            'agencesJson' => $agencesJson,
            'breadcrumbs' => $breadcrumbBuilder->build(['route' => 'dom_second_form']),
        ]);
    }

    private function traitementFormulaire(FormInterface $form, Request $request, DomPdfService $pdfService)
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** 1. recuperation des données dans le formulaire @var SecondFormDto $secondFormDto */
            $secondFormDto = $form->getData();

            // 2. creation de l'entité DOM 
            $dom = $this->domFactory->create($secondFormDto);

            // 3. traitement de fichier
            $this->traitementFichier($form, $dom, $pdfService, $secondFormDto);

            // 4. enregistrement  des données dans la base de donnée
            $this->domRepository->add($dom, true);

            $this->addFlash('success', 'La demande d\'ordre de mission a été créée avec succès.');

            return $this->redirectToRoute('dom_first_form');
        }
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
        if (empty($nomFichierEnregistrer)) {
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
}
