<?php

namespace App\Controller\Hf\Atelier\Dit\Liste;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/hf/atelier/dit/liste")
 */
class ClotureController extends AbstractController
{
    /**
     * @Route("/cloture/{numDit}", name="hf_atelier_dit_liste_cloture")
     */
    public function index($numDit)
    {
        return $this->render('hf/atelier/dit/liste/cloture.html.twig', [
            'numDit' => $numDit,
        ]);
    }
}
