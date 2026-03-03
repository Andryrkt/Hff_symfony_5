<?php

namespace App\Controller\Hf\Atelier\Dit\Soumission\Ors;

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
    public function index(string $numDit, string $numOr)
    {
        // 1. gerer l'accés 
        $this->denyAccessUnlessGranted('ATELIER_DIT_SOUMISSION_ORS');

        return $this->render('hf/atelier/dit/soumission/ors/index.html.twig', [
            'numDit' => $numDit,
        ]);
    }
}
