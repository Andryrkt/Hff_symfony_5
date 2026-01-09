<?php

namespace App\Controller\Hf\Materiel\Badm\Liste;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/hf/materiel/badm")
 */
class DetailController extends AbstractController
{

    /**
     * @Route("/detail/{numeroBadm}", name="hf_materiel_badm_detail_index")
     */
    public function index() {}
}
