<?php

namespace App\Controller\Hf\Materiel\Badm\Liste;

use Symfony\Component\Routing\Annotation\Route;
use App\Service\Navigation\ContextAwareBreadcrumbBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/hf/materiel/badm")
 */
class ListeController extends AbstractController
{
    /**
     * @Route("/liste", name="hf_materiel_badm_liste_index")
     */
    public function index(ContextAwareBreadcrumbBuilder $breadcrumbBuilder)
    {
        return $this->render('hf/materiel/badm/liste/liste.html.twig', [
            'breadcrumbs' => $breadcrumbBuilder->build('hf_materiel_badm_liste_index'),
        ]);
    }
}
