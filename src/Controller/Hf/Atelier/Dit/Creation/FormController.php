<?php

namespace App\Controller\Hf\Atelier\Dit\Creation;

use Psr\Log\LoggerInterface;
use App\Form\Hf\Atelier\Dit\DitFormType;
use App\Factory\Hf\Atelier\Dit\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Navigation\ContextAwareBreadcrumbBuilder;
use App\Service\Historique_operation\HistoriqueOperationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/hf/atelier/dit")
 */
class FormController extends AbstractController
{

    private HistoriqueOperationService $historiqueOperationService;
    protected LoggerInterface $logger;
    private ContextAwareBreadcrumbBuilder $breadcrumbBuilder;


    public function __construct(
        HistoriqueOperationService $historiqueOperationService,
        LoggerInterface $logger,
        ContextAwareBreadcrumbBuilder $breadcrumbBuilder
    ) {
        $this->historiqueOperationService = $historiqueOperationService;
        $this->logger = $logger;
        $this->breadcrumbBuilder = $breadcrumbBuilder;
    }

    /**
     * @Route("/form", name="hf_atelier_dit_form_index")
     */
    public function index(
        FormFactory $formFactory,
        Request $request
    ) {
        // 1. gerer l'accés 
        $this->denyAccessUnlessGranted('ATELIER_DIT_CREATE');

        // 2. initialisation
        $dto = $formFactory->create();

        // 3. creation du formulaire
        $form = $this->createForm(DitFormType::class, $dto);

        // 4. gerer la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dto = $form->getData();
            // $formFactory->save();
            return $this->redirectToRoute('hf_atelier_dit_list_index');
        }

        return $this->render('hf/atelier/dit/creation/form.html.twig', [
            'form' => $form->createView(),
            'breadcrumbs' => $this->breadcrumbBuilder->build('hf_atelier_dit_form_index'),
        ]);
    }
}
