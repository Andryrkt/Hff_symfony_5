<?php

namespace App\Controller\Hf\Materiel\Badm\Creation;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/hf/materiel/badm")
 */
class SecondFormController extends AbstractController
{
    /**
     * @Route("/second-form", name="hf_materiel_badm_second_form_index")
     */
    public function index()
    {
        return $this->render('hf/materiel/badm/creation/second_form.html.twig');
    }
}
