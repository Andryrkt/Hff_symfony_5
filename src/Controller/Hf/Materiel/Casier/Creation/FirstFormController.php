<?php

namespace App\Controller\Hf\Materiel\Casier\Creation;

use Symfony\Component\Routing\Annotation\Route;
use App\Form\Hf\Materiel\Casier\FirstFormType;
use App\Factory\Hf\Materiel\Casier\FirstFormFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/hf/materiel/casier")
 */
class FirstFormController extends AbstractController
{
    /**
     * @Route("/", name="hf_materiel_casier_first_form_index")
     */
    public function index(FirstFormFactory $casierFirstFormFactory)
    {
        // 1. gerer l'accés 
        $this->denyAccessUnlessGranted('MATERIEL_CASIER_CREATE');

        // 2. initialisation de la CasierFirstFormDto
        $dto = $casierFirstFormFactory->create();

        // 3. creation du formualire
        $form = $this->createForm(FirstFormType::class, $dto);


        // 4. traitement du formualire
        // $response = $this->traitemementForm($form, $request);

        // if ($response instanceof RedirectResponse) {
        //     return $response;
        // }

        // 5. rendu de la vue
        return $this->render('hf/materiel/casier/creation/first_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
