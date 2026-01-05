<?php

namespace App\Controller\Hf\Materiel\Badm\Liste;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/hf/materiel/badm")
 */
class ListeController extends AbstractController
{
    /**
     * @Route("/liste", name="hf_materiel_badm_liste_index")
     */
    public function index()
    {
        return $this->render('hf/materiel/badm/liste/liste.html.twig');
    }
}
