<?php

namespace App\Controller\Rh\Dom;

use App\Dto\Rh\Dom\FirstFormDto;
use App\Dto\Rh\Dom\SecondFormDto;
use App\Factory\Rh\Dom\DomFactory;
use App\Form\Rh\Dom\SecondFormType;
use App\Service\Rh\Dom\DomPdfService;
use App\Repository\Rh\Dom\DomRepository;
use App\Factory\Rh\Dom\SecondFormDtoFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
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
        DomPdfService $pdfService
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
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var SecondFormDto $secondFormDto */
            $secondFormDto = $form->getData();

            // creation de l'entité DOM 
            $dom = $this->domFactory->create($secondFormDto);

            // enregistrement  des données dans la base de donnée
            $this->domRepository->add($dom, true);

            // creation de page de garde
            $filePathName = '';
            $pdfService->genererPDF($secondFormDto, $filePathName);

            $this->addFlash('success', 'La demande d\'ordre de mission a été créée avec succès.');

            return $this->redirectToRoute('dom_first_form');
        }

        // 6. rendu de toutes les agences pendant le premier chargement
        $agencesJson = $this->serealisationAgence($agenceRepository, $serializer);

        // rendu du vue
        return $this->render('rh/dom/secondForm.html.twig', [
            'form'          => $form->createView(),
            'secondFormDto' => $secondFormDto,
            'agencesJson' => $agencesJson,
        ]);
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
