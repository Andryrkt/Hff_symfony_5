<?php

namespace App\Controller\Hf\Atelier\Dit\Soumission\Bc;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/hf/atelier/dit/soumission/bc", name="hf_atelier_dit_soumission_bc_")
 */
class SoumissionBcController extends AbstractController
{
    /**
     * @Route("/{numDit}", name="index")
     */
    public function index(string $numDit)
    {
        return $this->render('hf/atelier/dit/soumission/bc/index.html.twig', [
            'numDit' => $numDit,
        ]);
    }
}
