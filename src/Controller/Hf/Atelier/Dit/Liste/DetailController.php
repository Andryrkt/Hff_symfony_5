<?php

namespace App\Controller\Hf\Atelier\Dit\Liste;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/***
 * @Route("/hf/atelier/dit/liste")
 */
class DetailController extends AbstractController
{
    /**
     * @Route("/detail/{numDit}", name="hf_atelier_dit_liste_detail")
     */
    public function index($numDit)
    {
        return $this->render('hf/atelier/dit/liste/detail.html.twig', [
            'numDit' => $numDit,
        ]);
    }
}
