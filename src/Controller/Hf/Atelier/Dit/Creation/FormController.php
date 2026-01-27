<?php

namespace App\Controller\Hf\Atelier\Dit\Creation;

use Psr\Log\LoggerInterface;
use App\Form\Hf\Atelier\Dit\DitFormType;
use App\Factory\Hf\Atelier\Dit\FormFactory;
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
        FormFactory $formFactory
    ) {
        // 1. gerer l'accés 
        $this->denyAccessUnlessGranted('ATELIER_DIT_CREATE');

        // 2. initialisation
        $formFactory = $formFactory->create();

        // 3. creation du formulaire
        $form = $this->createForm(DitFormType::class, $formFactory);

        return $this->render('hf/atelier/dit/creation/form.html.twig', [
            'form' => $form->createView(),
            'breadcrumbs' => $this->breadcrumbBuilder->build('hf_atelier_dit_form_index'),
        ]);
    }
}
