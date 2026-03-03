<?php

namespace App\Controller\Hf\Atelier\Dit\Soumission\Ors;

use App\Factory\Hf\Atelier\Dit\Soumission\Ors\OrsFactory;
use App\Form\Hf\Atelier\Dit\Soumission\Ors\OrsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/hf/atelier/dit/soumission/ors", name="hf_atelier_dit_soumission_ors_")
 */
class SoumissionOrsController extends AbstractController
{
    /**
     * @Route("/{numDit}/{numOr}", name="index")
     */
    public function index(
        string $numDit,
        string $numOr,
        OrsFactory $orsFactory
    ) {
        // 1. gerer l'accés 
        $this->denyAccessUnlessGranted('ATELIER_DIT_SOUMISSION_ORS');

        // 2. creation et initialisation du formulaire
        $dto = $orsFactory->create($numDit, $numOr);
        $form = $this->createForm(OrsType::class, $dto);

        return $this->render('hf/atelier/dit/soumission/ors/index.html.twig', [
            'numDit' => $numDit,
            'form' => $form->createView(),
        ]);
    }
}
