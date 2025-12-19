<?php

namespace App\Controller\Hf\Materiel\Casier\Liste;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/hf/materiel/casier")
 */
class ListeController extends AbstractController
{
    /**
     * @Route("/liste", name="casier_liste_index")
     */
    public function index()
    {
        return $this->render('hf/materiel/casier/liste/liste.html.twig');
    }
}
