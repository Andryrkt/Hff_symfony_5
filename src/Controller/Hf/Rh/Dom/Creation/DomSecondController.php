<?php

namespace App\Controller\Hf\Rh\Dom\Creation;

use App\Form\Hf\Rh\Dom\SecondFormType;
use Symfony\Component\Form\FormInterface;
use App\Factory\Hf\Rh\Dom\SecondFormDtoFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Hf\Rh\Dom\DomCreationHandler;
use App\Service\Hf\Rh\Dom\DomPdfService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Service\Navigation\ContextAwareBreadcrumbBuilder;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Service\Historique_operation\HistoriqueOperationService;
use App\Service\Admin\AgenceSerializerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/rh/ordre-de-mission")
 */
final class DomSecondController extends AbstractDomFormController
{
    /**
     * @Route("/dom-second-form", name="dom_second_form")
     */
    public function secondForm(
        Request $request,
        DomPdfService $pdfService,
        ContextAwareBreadcrumbBuilder $breadcrumbBuilder,
        SecondFormDtoFactory $secondFormDtoFactory,
        AgenceSerializerService $agenceSerializerService
    ) {
        // Démarrer le diagnostic de performance
        $this->denyAccessUnlessGranted('RH_ORDRE_MISSION_CREATE');

        // Mesure: Récupération des données de session
        $firstFormDto = $this->getFirstFormDataFromSession($request->getSession());

        if ($firstFormDto instanceof RedirectResponse) {
            $this->logger->warning('Données du premier formulaire non trouvées en session.');
            return $firstFormDto;
        }

        // Mesure: Création du SecondFormDto
        $secondFormDto =  $secondFormDtoFactory->create($firstFormDto);

        // Mesure: Création du formulaire Symfony
        $form = $this->createForm(SecondFormType::class, $secondFormDto);

        // traitement du formulaire
        $this->traitementFormulaire($request, $form, $pdfService);

        // Mesure: Création de la vue du formulaire
        $formView = $form->createView();

        $this->logger->info('✅ Fin du chargement du second formulaire de DOM');

        return $this->render('hf/rh/dom/creation/secondForm.html.twig', [
            'form'          => $formView,
            'secondFormDto' => $form->getData(),
            'agencesJson'   => $agenceSerializerService->serializeAgencesForDropdown(),
            'breadcrumbs'   => $breadcrumbBuilder->build('dom_second_form'),
        ]);
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
}
