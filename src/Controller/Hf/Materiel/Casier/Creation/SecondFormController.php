<?php

namespace App\Controller\Hf\Materiel\Casier\Creation;

use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormInterface;
use App\Model\Hf\Materiel\Casier\CasierModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Factory\Hf\Materiel\Casier\SecondFormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Form\Hf\Materiel\Casier\Creation\SecondFormType;
use App\Service\Hf\Materiel\Casier\CasierCreationHandler;
use App\Constants\Admin\Historisation\TypeDocumentConstants;
use App\Constants\Admin\Historisation\TypeOperationConstants;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Service\Historique_operation\HistoriqueOperationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/hf/materiel/casier")
 */
class SecondFormController extends AbstractController
{
    protected LoggerInterface $logger;
    protected CasierCreationHandler $casierCreationHandler;
    protected HistoriqueOperationService $historiqueOperationService;

    public function __construct(
        LoggerInterface $domSecondFormLogger,
        HistoriqueOperationService $historiqueOperationService,
        CasierCreationHandler $casierCreationHandler
    ) {
        $this->logger = $domSecondFormLogger;
        $this->casierCreationHandler = $casierCreationHandler;
        $this->historiqueOperationService = $historiqueOperationService;
    }

    /**
     * @Route("/second-form", name="hf_materiel_casier_second_form_index")
     */
    public function index(Request $request, CasierModel $casierModel, SecondFormFactory $secondFormFactory)
    {
        // 1. gerer l'accés 
        $this->denyAccessUnlessGranted('MATERIEL_CASIER_CREATE');

        // 2.Recupération de l'information envoyer par le premier formulaire dans la session 
        $firstFormDto = $this->getFirstFormDataFromSession($request->getSession());

        // 3. Récupération de l'information sur le matériel
        $caracteristiqueMateriel = $casierModel->getCaracteristiqueMateriel($firstFormDto);

        // 4. Initialisation du secondFormDto
        $secondFormDto = $secondFormFactory->create($caracteristiqueMateriel);

        // 5. creation du formulaire
        $form = $this->createForm(SecondFormType::class, $secondFormDto);

        // 6. traitement du formulaire
        $this->traitementFormulaire($request, $form);

        return $this->render('hf/materiel/casier/creation/second_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Recupération de l'information envoyer par le premier formulaire dans la session
     * 
     * return FirstFormDto|RedirectResponse
     */
    private function getFirstFormDataFromSession(SessionInterface $session)
    {
        $firstFormDto = $session->get('casier_first_form_data');
        if (!$firstFormDto) {
            $this->addFlash('warning', 'La session a expiré, veuillez recommencer.');
            return $this->redirectToRoute('hf_materiel_casier_first_form_index');
        }

        return $firstFormDto;
    }

    private function traitementFormulaire(Request $request, FormInterface $form)
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->logger->info('Second formulaire soumis et valide.');
            $this->logger->debug('Données du formulaire', ['data' => $form->getData()]);
            $redirectResponse = $this->processValidForm($form);
            if ($redirectResponse) {
                return $redirectResponse;
            }
        }
    }

    private function processValidForm(FormInterface $form): ?RedirectResponse
    {
        $numeroCasier = 'non-défini';
        $message = 'Création de l\'ordre de mission.';
        $success = false;

        try {
            $casier = $this->casierCreationHandler->handle($form);
            $numeroCasier = $casier->getNumero();
            $success = true;
            $message = 'Le casier a été créé avec succès.';
            $this->logger->info($message, ['numero_casier' => $numeroCasier]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $this->logger->error(
                'Erreur lors de la création du casier : ' . $message,
                ['numero_casier' => $numeroCasier, 'exception' => $e]
            );
        }

        $this->historiqueOperationService->enregistrer(
            $numeroCasier,
            TypeOperationConstants::TYPE_OPERATION_CREATION_NAME,
            TypeDocumentConstants::TYPE_DOCUMENT_CAS_CODE,
            $success,
            $message
        );

        if ($success) {
            $this->addFlash('success', $message);
            return $this->redirectToRoute('casier_liste_index');
        }

        $this->addFlash('warning', $message);
        return null;
    }
}
