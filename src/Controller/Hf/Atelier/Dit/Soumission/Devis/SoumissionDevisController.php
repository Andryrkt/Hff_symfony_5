<?php

namespace App\Controller\Hf\Atelier\Dit\Soumission\Devis;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/hf/atelier/dit/soumission/devis", name="hf_atelier_dit_soumission_devis_")
 */
class SoumissionDevisController extends AbstractController
{
    /**
     * @Route("/{numDit}", name="index")
     */
    public function index(string $numDit)
    {
        return $this->render('hf/atelier/dit/soumission/devis/index.html.twig', [
            'numDit' => $numDit,
        ]);
    }
}
