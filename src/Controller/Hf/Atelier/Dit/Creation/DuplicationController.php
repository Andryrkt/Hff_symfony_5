<?php

namespace App\Controller\Hf\Atelier\Dit\Creation;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/hf/atelier/dit/creation")
 */
class DuplicationController extends AbstractController
{
    /**
     * @Route("/duplication/{numDit}", name="hf_atelier_dit_creation_duplication")
     */
    public function index($numDit)
    {
        return $this->render('hf/atelier/dit/creation/duplication.html.twig', [
            'numDit' => $numDit,
        ]);
    }
}
