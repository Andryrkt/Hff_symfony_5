<?php

namespace App\Controller\Hf\Materiel\Casier\Creation;

use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormInterface;
use App\Model\Hf\Materiel\Casier\CasierModel;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Hf\Materiel\Casier\Creation\FirstFormType;
use Symfony\Component\Routing\Annotation\Route;
use App\Factory\Hf\Materiel\Casier\FirstFormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Service\Historique_operation\HistoriqueOperationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/hf/materiel/casier")
 */
class FirstFormController extends AbstractController
{
    private LoggerInterface $logger;
    private HistoriqueOperationService $historiqueOperationService;

    public function __construct(LoggerInterface $logger, HistoriqueOperationService $historiqueOperationService)
    {
        $this->logger = $logger;
        $this->historiqueOperationService = $historiqueOperationService;
    }

    /**
     * @Route("/", name="hf_materiel_casier_first_form_index")
     */
    public function index(FirstFormFactory $casierFirstFormFactory, Request $request, CasierModel $casierModel)
    {
        // 1. gerer l'accés 
        $this->denyAccessUnlessGranted('MATERIEL_CASIER_CREATE');

        // 2. initialisation de la CasierFirstFormDto
        $dto = $casierFirstFormFactory->create();

        // 3. creation du formualire
        $form = $this->createForm(FirstFormType::class, $dto);


        // 4. traitement du formualire
        $response = $this->traitemementForm($form, $request, $casierModel);
        if ($response instanceof RedirectResponse) {
            return $response;
        }

        // 5. rendu de la vue
        return $this->render('hf/materiel/casier/creation/first_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function traitemementForm(FormInterface $form, Request $request, CasierModel $casierModel)
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->logger->info('Premier formulaire soumis et valide.');

            // 1. Récupération des données du formulaire
            $dto = $form->getData();

            // 2. Vérification de l'existence du matériel
            $infoMaterielExistant = $casierModel->estMaterielExiste($dto);
            if (!$infoMaterielExistant) {
                $this->logger->warning('Le matériel n\'existe pas.');
                $message = 'Le matériel peut être déjà vendu ou vous avez mal saisi le numéro de série ou le numéro de parc ou ID matériel.';
                $this->historiqueOperationService->enregistrer(
                    '',
                    'CREATION',
                    'CASIER',
                    false,
                    $message
                );

                $this->addFlash('warning', $message);
                return $this->redirectToRoute('hf_materiel_casier_first_form_index');
            }

            $this->logger->debug('Données du formulaire', ['data' => $dto]);

            // 2. Stockage des données dans la session
            $session = $request->getSession();
            $session->set('casier_first_form_data', $dto);

            // 3. Redirection vers le second formulaire
            return $this->redirectToRoute('hf_materiel_casier_second_form_index');
        }
        return null;
    }
}
