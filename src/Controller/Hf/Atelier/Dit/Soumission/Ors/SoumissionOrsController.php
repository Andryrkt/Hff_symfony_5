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
     * @Route("/{numDit}", name="index")
     */
    public function index(string $numDit)
    {
        return $this->render('hf/atelier/dit/soumission/ors/index.html.twig', [
            'numDit' => $numDit,
        ]);
    }
}
