<?php

namespace App\Controller\Dom;

use App\Dto\Dom\DomSecondFormData;
use App\Form\Dom\DomSecondFormType;
use App\Service\Dom\DomWizardManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DomSecondFormController extends AbstractController
{
    /**
     * @Route("/dom/second", name="dom_second")
     *
     * @param Request $request
     * @param DomWizardManager $domWizardManager
     * @return void
     */
    public function index(Request $request, DomWizardManager $domWizardManager, EntityManagerInterface $em)
    {
        // Validation de l'accès à l'étape
        if (!$domWizardManager->hasStep1Data()) {
            $this->addFlash('error', 'Veuillez compléter la première formulaire d\'abord');
            return $this->redirectToRoute('dom_first');
        }

        // Récupérer les données de l'étape 1
        $step1Data = $domWizardManager->getStep1DataArray();

        // Créer une instance du DTO du second formulaire
        $domSecondFormData = new DomSecondFormData();

        // Hydrater le DTO avec les données de l'étape 1 si nécessaire
        // Par exemple, si vous avez des champs communs entre les étapes :
        if (isset($step1Data)) {
            $domSecondFormData->populateFromStep1($step1Data, $em);
        }

        $form = $this->createForm(DomSecondFormType::class, $domSecondFormData);

        return $this->render('dom/domSecondForm.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
