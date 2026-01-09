<?php

namespace App\Controller\Hf\Materiel\Badm\Creation;

use Psr\Log\LoggerInterface;
use App\Model\Hf\Materiel\Badm\BadmModel;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Hf\Materiel\Badm\BadmPdfService;
use App\Factory\Hf\Materiel\Badm\SecondFormFactory;
use App\Service\Hf\Materiel\Badm\BadmCreationHandler;
use App\Form\Hf\Materiel\Badm\Creation\SecondFormType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Service\Navigation\ContextAwareBreadcrumbBuilder;
use App\Constants\Admin\Historisation\TypeDocumentConstants;
use App\Constants\Admin\Historisation\TypeOperationConstants;
use App\Service\Hf\Materiel\Badm\BadmBlockingConditionService;
use App\Service\Historique_operation\HistoriqueOperationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/hf/materiel/badm")
 */
class SecondFormController extends AbstractController
{
    private BadmBlockingConditionService $badmBlockingConditionService;
    private HistoriqueOperationService $historiqueOperationService;
    protected LoggerInterface $logger;
    private BadmCreationHandler $badmCreationHandler;
    private BadmPdfService $pdfService;

    public function __construct(
        BadmBlockingConditionService $badmBlockingConditionService,
        HistoriqueOperationService $historiqueOperationService,
        LoggerInterface $logger,
        BadmCreationHandler $badmCreationHandler,
        BadmPdfService $pdfService
    ) {
        $this->badmBlockingConditionService = $badmBlockingConditionService;
        $this->historiqueOperationService = $historiqueOperationService;
        $this->logger = $logger;
        $this->badmCreationHandler = $badmCreationHandler;
        $this->pdfService = $pdfService;
    }

    /**
     * @Route("/second-form", name="hf_materiel_badm_second_form_index")
     */
    public function index(
        Request $request,
        BadmModel $badmModel,
        SecondFormFactory $secondFormFactory,
        ContextAwareBreadcrumbBuilder $breadcrumbBuilder
    ) {
        // 1. gerer l'accés 
        $this->denyAccessUnlessGranted('MATERIEL_BADM_CREATE');

        // 2. Récupération de l'information envoyer par le premier formulaire dans la session 
        $firstFormDto = $this->getFirstFormDataFromSession($request);

        // 3. Récupération des infos matériel depuis IPS
        $infoMaterielDansIps = $badmModel->getInfoMateriel($firstFormDto);

        // 4. CONDITION DE BLOCAGE 
        $blockingMessage = !$this->isGranted('ROLE_ADMIN') ? $this->badmBlockingConditionService->checkBlockingConditionsAvantSoumissionForm($firstFormDto, $infoMaterielDansIps) : null;
        if ($blockingMessage) {
            $this->addFlash('warning', $blockingMessage);
            return $this->redirectToRoute('hf_materiel_badm_first_form_index');
        }

        // 5. Initialisation du secondFormDto
        $secondFormDto = $secondFormFactory->create($firstFormDto, $infoMaterielDansIps);

        // 6. creation du formulaire
        $form = $this->createForm(SecondFormType::class, $secondFormDto);

        // 7. traitement du formulaire
        $response = $this->traitementFormulaire($request, $form);
        if ($response) {
            return $response;
        }

        return $this->render('hf/materiel/badm/creation/second_form.html.twig', [
            'form' => $form->createView(),
            'secondFormDto' => $secondFormDto,
            'breadcrumbs' => $breadcrumbBuilder->build('hf_materiel_badm_second_form_index'),
        ]);
    }

    /**
     * Recupération de l'information envoyer par le premier formulaire dans la session
     * 
     * return FirstFormDto|RedirectResponse
     */
    private function getFirstFormDataFromSession(Request $request)
    {
        $firstFormDto = $request->getSession()->get('badm_first_form_data');
        if (!$firstFormDto) {
            $this->addFlash('warning', 'La session a expiré, veuillez recommencer.');
            return $this->redirectToRoute('hf_materiel_badm_first_form_index');
        }

        return $firstFormDto;
    }

    private function traitementFormulaire(Request $request, FormInterface $form): ?RedirectResponse
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->logger->info('Second formulaire soumis et valide.');
            $this->logger->debug('Données du formulaire', ['data' => $form->getData()]);

            // 1. CONDITION DE BLOCAGE 
            $secondFormDto = $form->getData();
            $blockingMessage = $this->badmBlockingConditionService->checkBlockingConditionsApresSoumissionForm($secondFormDto);
            if ($blockingMessage) {
                $this->addFlash('warning', $blockingMessage);
                return $this->redirectToRoute('hf_materiel_badm_first_form_index');
            }

            // 2. processValidForm
            $redirectResponse = $this->processValidForm($form);
            if ($redirectResponse) {
                return $redirectResponse;
            }
        }

        return null;
    }

    private function processValidForm(FormInterface $form): ?RedirectResponse
    {
        $numeroBadm = 'non-défini';
        $message = 'Création du Badm.';
        $success = false;

        try {
            $badm = $this->badmCreationHandler->handle($form, $this->pdfService);
            $numeroBadm = $badm->getNumeroBadm();
            $success = true;
            $message = 'Le Badm a été créé avec succès.';
            $this->logger->info($message, ['numero_badm' => $numeroBadm]);
        } catch (\Throwable $e) {
            $message = $e->getMessage();
            $this->logger->error(
                'Erreur lors de la création du Badm : ' . $message,
                ['numero_badm' => $numeroBadm, 'exception' => $e]
            );
        }

        $this->historiqueOperationService->enregistrer(
            $numeroBadm,
            TypeOperationConstants::TYPE_OPERATION_CREATION_NAME,
            TypeDocumentConstants::TYPE_DOCUMENT_BADM_CODE,
            $success,
            $message
        );

        if ($success) {
            $this->addFlash('success', $message);
            return $this->redirectToRoute('hf_materiel_badm_liste_index');
        }

        $this->addFlash('warning', $message);
        return null;
    }
}
