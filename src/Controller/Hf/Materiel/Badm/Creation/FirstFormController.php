<?php

namespace App\Controller\Hf\Materiel\Badm\Creation;

use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormInterface;
use App\Model\Hf\Materiel\Badm\BadmModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Factory\Hf\Materiel\Badm\FirstFormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Form\Hf\Materiel\Badm\Creation\FirstFormType;
use App\Service\Navigation\ContextAwareBreadcrumbBuilder;
use App\Service\Historique_operation\HistoriqueOperationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("/hf/materiel/badm")
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
     * @Route("/", name="hf_materiel_badm_first_form_index")
     */
    public function index(
        FirstFormFactory $badmFirstFormFactory,
        Request $request,
        BadmModel $badmModel,
        ContextAwareBreadcrumbBuilder $breadcrumbBuilder
    ) {
        // 1. gerer l'accés 
        $this->denyAccessUnlessGranted('MATERIEL_BADM_CREATE');

        // 2. initialisation de la BadmFirstFormDto
        $dto = $badmFirstFormFactory->create();

        // 3. creation du formualire
        $form = $this->createForm(FirstFormType::class, $dto);


        // 4. traitement du formualire
        $response = $this->traitemementForm($form, $request, $badmModel);
        if ($response instanceof RedirectResponse) {
            return $response;
        }

        // 5. rendu de la vue
        return $this->render('hf/materiel/badm/creation/first_form.html.twig', [
            'form' => $form->createView(),
            'breadcrumbs' => $breadcrumbBuilder->build('hf_materiel_badm_first_form_index'),
        ]);
    }

    private function traitemementForm(FormInterface $form, Request $request, BadmModel $badmModel)
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->logger->info('Premier formulaire soumis et valide.');

            // 1. Récupération des données du formulaire
            $dto = $form->getData();

            // 2. Vérification de l'existence du matériel
            $infoMaterielExistant = $badmModel->estMaterielExiste($dto);
            if (!$infoMaterielExistant) {
                $this->logger->warning('Le matériel n\'existe pas.');
                $message = 'Le matériel peut être déjà vendu ou vous avez mal saisi le numéro de série ou le numéro de parc ou ID matériel.';
                $this->historiqueOperationService->enregistrer(
                    '',
                    'CREATION',
                    'BADM',
                    false,
                    $message
                );

                $this->addFlash('warning', $message);
                return $this->redirectToRoute('hf_materiel_badm_first_form_index');
            }

            $this->logger->debug('Données du formulaire', ['data' => $dto]);

            // 2. Stockage des données dans la session
            $session = $request->getSession();
            $session->set('badm_first_form_data', $dto);

            // 3. Redirection vers le second formulaire
            return $this->redirectToRoute('hf_materiel_badm_second_form_index');
        }
        return null;
    }
}
