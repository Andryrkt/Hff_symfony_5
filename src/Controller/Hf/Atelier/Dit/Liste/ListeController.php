<?php

namespace App\Controller\Hf\Atelier\Dit\Liste;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/hf/atelier/dit")
 */
class ListeController extends AbstractController
{
    /**
     * @Route("/liste", name="hf_atelier_dit_liste_index")
     */
    public function index()
    {
        return $this->render('hf/atelier/dit/liste/liste.html.twig');
    }
}
