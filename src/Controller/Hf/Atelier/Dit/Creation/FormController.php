<?php

namespace App\Controller\Hf\Atelier\Dit\Creation;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/hf/atelier/dit")
 */
class FormController extends AbstractController
{
    /**
     * @Route("/form", name="hf_atelier_dit_form")
     */
    public function index()
    {
        // 1. gerer l'accés 
        $this->denyAccessUnlessGranted('ATELIER_DIT_CREATE');

        return $this->render('hf/atelier/dit/creation/form.html.twig');
    }
}
