<?php

namespace App\Controller\Hf\Materiel\Casier\Liste;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Factory\Hf\Materiel\Casier\SearchFactory;
use App\Form\Hf\Materiel\Casier\Liste\SearchType;
use App\Constants\Hf\Materiel\Casier\ButtonsConstants;
use App\Repository\Hf\Materiel\Casier\CasierRepository;
use App\Service\Navigation\ContextAwareBreadcrumbBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/hf/materiel/casier")
 */
class ListeController extends AbstractController
{
    /**
     * @Route("/liste", name="hf_materiel_casier_liste_index")
     */
    public function index(
        CasierRepository $casierRepository,
        Request $request,
        ContextAwareBreadcrumbBuilder $breadcrumbBuilder,
        SearchFactory $searchFactory
    ) {
        // 1. gerer l'accés
        $this->denyAccessUnlessGranted('MATERIEL_CASIER_VIEW');

        // creation du formulaire de recherhce
        $searchDto = $searchFactory->create();
        $form = $this->createForm(SearchType::class, $searchDto);

        //traitement du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchDto = $form->getData();
        }
        // recuperation des données
        $casiers = $casierRepository->getCasiersTemporaire($searchDto);


        return $this->render('hf/materiel/casier/liste/liste.html.twig', [
            'casiers' => $casiers,
            'form' => $form->createView(),
            'buttons' => ButtonsConstants::BUTTONS_ACTIONS,
            'breadcrumbs' => $breadcrumbBuilder->build('hf_materiel_casier_liste_index'),
        ]);
    }
}
