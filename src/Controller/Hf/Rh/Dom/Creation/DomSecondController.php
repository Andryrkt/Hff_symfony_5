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
class DomSecondController extends AbstractController
{
    private LoggerInterface $logger;
    private DomCreationHandler $domCreationHandler;
    private HistoriqueOperationService $historiqueOperationService;

    public function __construct(
        LoggerInterface $domSecondFormLogger,
        DomCreationHandler $domCreationHandler,
        HistoriqueOperationService $historiqueOperationService
    ) {
        $this->logger = $domSecondFormLogger;
        $this->domCreationHandler = $domCreationHandler;
        $this->historiqueOperationService = $historiqueOperationService;
    }

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

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->logger->info('Second formulaire soumis et valide.');
            $this->logger->debug('Données du formulaire', ['data' => $form->getData()]);
            $redirectResponse = $this->processValidForm($form, $pdfService);
            if ($redirectResponse) {
                return $redirectResponse;
            }
        }

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

    private function processValidForm(FormInterface $form, DomPdfService $pdfService): ?RedirectResponse
    {
        $numeroDom = 'non-défini';
        $message = 'Création de l\'ordre de mission.';
        $success = false;

        try {
            $dom = $this->domCreationHandler->handle($form, $pdfService);
            $numeroDom = $dom->getNumeroOrdreMission();
            $success = true;
            $message = 'La demande d\'ordre de mission a été créée avec succès.';
            $this->logger->info($message, ['numero_dom' => $numeroDom]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $this->logger->error(
                'Erreur lors de la création de l\'ordre de mission : ' . $message,
                ['numero_dom' => $numeroDom, 'exception' => $e]
            );
        }

        $this->historiqueOperationService->enregistrer(
            $numeroDom,
            'CREATION',
            'DOM',
            $success,
            $message
        );

        if ($success) {
            $this->addFlash('success', $message);
            return $this->redirectToRoute('dom_liste_index');
        }

        $this->addFlash('warning', $message);
        return null;
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
