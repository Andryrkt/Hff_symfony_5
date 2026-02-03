<?php

namespace App\Controller\Hf\Atelier\Dit\Dw;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/hf/atelier/dit/dw")
 */
class DwInterventionAtelier extends AbstractController
{
    /**
     * @Route("/intervention/{numDit}", name="hf_atelier_dit_dw_intervention")
     */
    public function index(int $numDit): Response
    {
        return $this->render('hf/atelier/dit/dw/index.html.twig', [
            'numDit' => $numDit,
        ]);
    }
}
