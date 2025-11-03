<?php

namespace App\Controller\Rh\Dom;


use App\Factory\Rh\Dom\FirstFormDtoFactory;
use App\Form\Rh\Dom\FirstFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/rh/ordre-de-mission")
 */
class DomFirstController extends AbstractController
{
    private $firstFormDtoFactory;

    public function __construct(FirstFormDtoFactory $firstFormDtoFactory)
    {
        $this->firstFormDtoFactory = $firstFormDtoFactory;
    }

    /**
     * @Route("/dom-first-form", name="dom_first_form")
     */
    public function firstForm(Request $request)
    {
        //gerer l'accés 
        $this->denyAccessUnlessGranted('RH_ORDRE_MISSION_CREATE');

        $dom = $this->firstFormDtoFactory->create();


        //CREATION DU FORMULAIRE
        $form = $this->createForm(FirstFormType::class, $dom);
        //TRAITEMENT DU FORMULAIRE
        //$this->traitemementForm($form, $request, $dom);

        //RENDU DE LA VUE
        return $this->render('rh/dom/firstForm.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function traitemementForm($form, $request, $dom)
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $salarier = $form->get('salarie')->getData();
            $dom->setSalarier($salarier);

            $formData = $form->getData()->toArray();

            //$this->getSessionService()->set('form1Data', $formData);

            // Redirection vers le second formulaire
            return $this->redirectToRoute('dom_second_form');
        }
    }



}
