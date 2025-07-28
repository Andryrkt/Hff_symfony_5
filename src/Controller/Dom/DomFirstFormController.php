<?php

namespace App\Controller\Dom;

use App\Dto\Dom\DomFirstFormData;
use App\Form\Dom\DomFirstFormType;
use App\Service\Dom\DomWizardManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DomFirstFormController extends AbstractController
{
    /**
     * @Route("/dom/first", name="dom_first")
     * 
     * @param Request $request
     * @param DomWizardManager $wizardManager
     * @return void
     */
    public function index(Request $request, DomWizardManager $domWizardManager): Response
    {
        // Récupération des données existantes ou nouveau DTO
        $dto = $domWizardManager->getStep1Data() ?? new DomFirstFormData();

        $form = $this->createForm(DomFirstFormType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $domWizardManager->saveStep1Data($dto);

            return $this->redirectToRoute('dom_step2');
        }

        return $this->render('dom/domFirstForm.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
