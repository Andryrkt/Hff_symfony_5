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
    private SecondFormDtoFactory $secondFormDtoFactory;
    private LoggerInterface $logger;
    private DomCreationHandler $domCreationHandler;
    private HistoriqueOperationService $historiqueOperationService;
    private AgenceSerializerService $agenceSerializerService;

    public function __construct(
        SecondFormDtoFactory $secondFormDtoFactory,
        LoggerInterface $domSecondFormLogger,
        DomCreationHandler $domCreationHandler,
        HistoriqueOperationService $historiqueOperationService,
        AgenceSerializerService $agenceSerializerService
    ) {
        $this->secondFormDtoFactory = $secondFormDtoFactory;
        $this->logger = $domSecondFormLogger;
        $this->domCreationHandler = $domCreationHandler;
        $this->historiqueOperationService = $historiqueOperationService;
        $this->agenceSerializerService = $agenceSerializerService;
    }

    /**
     * @Route("/dom-second-form", name="dom_second_form")
     */
    public function secondForm(
        Request $request,
        DomPdfService $pdfService,
        ContextAwareBreadcrumbBuilder $breadcrumbBuilder
    ) {
        $this->logger->info('Affichage du second formulaire de création de DOM.');
        //$this->denyAccessUnlessGranted('RH_ORDRE_MISSION_CREATE');

        $firstFormDto = $this->getFirstFormDataFromSession($request->getSession());
        if ($firstFormDto instanceof RedirectResponse) {
            $this->logger->warning('Données du premier formulaire non trouvées en session.');
            return $firstFormDto;
        }

        $secondFormDto = $this->secondFormDtoFactory->create($firstFormDto);
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

        return $this->render('hf/rh/dom/creation/secondForm.html.twig', [
            'form'          => $form->createView(),
            'secondFormDto' => $form->getData(),
            'agencesJson'   => $this->agenceSerializerService->serializeAgencesForDropdown(),
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
            return $this->redirectToRoute('liste_dom_index');
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
