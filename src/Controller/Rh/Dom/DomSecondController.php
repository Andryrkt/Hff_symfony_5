<?php

namespace App\Controller\Rh\Dom;

use App\Dto\Rh\Dom\FirstFormDto;
use App\Dto\Rh\Dom\SecondFormDto;
use App\Form\Rh\Dom\SecondFormType;
use App\Factory\Rh\Dom\SecondFormDtoFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function secondForm(Request $request)
    {
        // 1. gerer l'accés 
        $this->denyAccessUnlessGranted('RH_ORDRE_MISSION_CREATE');

        // 2. recupération des donées du premier formulaire
        $session = $request->getSession();
        /** @var FirstFormDto $firstFormDto */
        $firstFormDto = $session->get('dom_first_form_data');

        if (!$firstFormDto) {
            // Handle case where first form data is not in session, e.g., redirect to first form
            return $this->redirectToRoute('dom_first_form');
        }

        // 3 . initialisation de la FirstFormDto
        $secondFormDto = $this->secondFormDtoFactory->create($firstFormDto);

        //4. creation du formulaire
        $form = $this->createForm(SecondFormType::class, $secondFormDto);

        // rendu du vue
        return $this->render('rh/dom/secondForm.html.twig', [
            'form'          => $form->createView(),
            'firstFormDto' => $firstFormDto // Keep firstFormDto for display if needed
        ]);
    }
}
