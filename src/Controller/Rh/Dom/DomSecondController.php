<?php

namespace App\Controller\Rh\Dom;

use App\Dto\Rh\Dom\FirstFormDto;
use App\Dto\Rh\Dom\SecondFormDto;
use App\Form\Rh\Dom\SecondFormType;
use Symfony\Component\Form\FormInterface;
use App\Factory\Rh\Dom\SecondFormDtoFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\Admin\AgenceService\AgenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @Route("/rh/ordre-de-mission")
 */
class DomSecondController extends AbstractController
{
    private $secondFormDtoFactory;

    public function __construct(SecondFormDtoFactory $firstFormDtoFactory)
    {
        $this->secondFormDtoFactory = $firstFormDtoFactory;
    }

    /**
     * @Route("/dom-second-form", name="dom_second_form")
     */
    public function secondForm(Request $request, AgenceRepository $agenceRepository, SerializerInterface $serializer)
    {
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
                // 1. récupère les données du formulaire
                $data = $form->getData();
                dd($data);

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
