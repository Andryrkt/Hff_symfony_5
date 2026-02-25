<?php

namespace App\Controller\Hf\Atelier\Dit\Creation;


use App\Form\Hf\Atelier\Dit\DitFormType;
use App\Service\Hf\Atelier\Dit\PdfService;
use App\Factory\Hf\Atelier\Dit\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Hf\Atelier\Dit\CreationHandler;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/hf/atelier/dit")
 */
class FormController extends AbstractDitFormController
{

    /**
     * @Route("/form", name="hf_atelier_dit_form_index")
     */
    public function index(
        FormFactory $formFactory,
        Request $request,
        PdfService $pdfService,
        CreationHandler $creationHandler
    ) {
        // 1. gerer l'accés 
        $this->denyAccessUnlessGranted('ATELIER_DIT_CREATE');

        // 2. initialisation
        $dto = $formFactory->create();

        // 3. creation du formulaire
        $form = $this->createForm(DitFormType::class, $dto);

        // 4. gerer la soumission du formulaire
        $redirectResponse = $this->traitementFormulaire($request, $form, $pdfService, $creationHandler);
        if ($redirectResponse) {
            return $redirectResponse;
        }

        $this->logger->info('✅ Fin du chargement du formulaire DIT');

        return $this->render('hf/atelier/dit/creation/form.html.twig', [
            'form' => $form->createView(),
            'breadcrumbs' => $this->breadcrumbBuilder->build('hf_atelier_dit_form_index'),
        ]);
    }
}
