<?php

namespace App\Controller\Hf\Atelier\Dit\Soumission\Ri;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/hf/atelier/dit/soumission/ri", name="hf_atelier_dit_soumission_ri_")
 */
class SoumissionRiController extends AbstractController
{
    /**
     * @Route("/{numDit}", name="index")
     */
    public function index(string $numDit)
    {
        return $this->render('hf/atelier/dit/soumission/ri/index.html.twig', [
            'numDit' => $numDit,
        ]);
    }
}
