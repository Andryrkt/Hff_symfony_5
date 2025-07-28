<?php

namespace App\Controller\Dom;

use App\Form\Dom\DomSecondFormType;
use App\Service\Dom\DomWizardManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DomSecondFormController extends AbstractController
{
    /**
     * @Route("/dom/second", name="dom_second")
     *
     * @param Request $request
     * @param DomWizardManager $wizardManager
     * @return void
     */
    public function index(Request $request, DomWizardManager $wizardManager)
    {
        // Validation de l'accès à l'étape
        if (!$wizardManager->hasStep1Data()) {
            $this->addFlash('error', 'Veuillez compléter la première formulaire d\'abord');
            return $this->redirectToRoute('dom_first');
        }

        // Récupération des données de l'étape 1
        $dto = $wizardManager->getStep1Data();
        $form = $this->createForm(DomSecondFormType::class, $dto);

        // [...] Gestion du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {


            // Redirection vers la page de confirmation ou l'étape suivante
            return $this->redirectToRoute('dom_confirmation');
        }
    }
}
