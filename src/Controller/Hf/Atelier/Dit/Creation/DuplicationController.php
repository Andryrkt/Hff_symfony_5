<?php

namespace App\Controller\Hf\Atelier\Dit\Creation;

use App\Form\Hf\Atelier\Dit\DitFormType;
use App\Factory\Hf\Atelier\Dit\FormFactory;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Hf\Atelier\Dit\Creation\AbstractDitFormController;


/**
 * @Route("/hf/atelier/dit/creation")
 */
class DuplicationController extends AbstractDitFormController
{
    /**
     * @Route("/duplication/{numDit}", name="hf_atelier_dit_creation_duplication")
     */
    public function index(
        string $numDit,
        FormFactory $formFactory
    ) {
        // 1. gerer l'accés 
        $this->denyAccessUnlessGranted('ATELIER_DIT_CREATE');

        // 2. initialisation (recupération des information DIT)
        $dto = $formFactory->duplicate($numDit);

        // 3. creation du formulaire
        $form = $this->createForm(DitFormType::class, $dto);

        return $this->render('hf/atelier/dit/creation/duplication.html.twig', [
            'form' => $form->createView(),
            'breadcrumbs' => $this->breadcrumbBuilder->build('hf_atelier_dit_creation_duplication'),
        ]);
    }
}
