<?php

namespace App\Controller\Hf\Materiel\Casier\Creation;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/hf/materiel/casier")
 */
class CasierFormController extends AbstractController
{
    /**
     * @Route("/creation", name="hf_materiel_casier_creation")
     */
    public function index() {}
}
