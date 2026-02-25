<?php

namespace App\Controller\Hf\Atelier\Dit\Soumission\Facture;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/hf/atelier/dit/soumission/facture", name="hf_atelier_dit_soumission_facture_")
 */
class SoumissionFactureController extends AbstractController
{
    /**
     * @Route("/{numDit}", name="index")
     */
    public function index(string $numDit)
    {
        return $this->render('hf/atelier/dit/soumission/facture/index.html.twig', [
            'numDit' => $numDit,
        ]);
    }
}
