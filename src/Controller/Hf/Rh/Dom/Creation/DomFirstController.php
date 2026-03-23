<?php

namespace App\Controller\Hf\Rh\Dom\Creation;

use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Factory\Hf\Rh\Dom\FirstFormDtoFactory;
use App\Form\Hf\Rh\Dom\creation\FirstFormType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Service\Navigation\ContextAwareBreadcrumbBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/rh/ordre-de-mission")
 */
class DomFirstController extends AbstractController
{
    private $firstFormDtoFactory;
    private LoggerInterface $logger;

    public function __construct(FirstFormDtoFactory $firstFormDtoFactory, LoggerInterface $domFirstFormLogger)
    {
        $this->firstFormDtoFactory = $firstFormDtoFactory;
        $this->logger = $domFirstFormLogger;
    }

    /**
     * @Route("/dom-first-form", name="dom_first_form")
     */
    public function firstForm(Request $request, ContextAwareBreadcrumbBuilder $breadcrumbBuilder)
    {
        $this->logger->info('Affichage du premier formulaire de création de DOM.');
        // 1. gerer l'accés 
        $this->denyAccessUnlessGranted('RH_ORDRE_MISSION_CREATE');

        // 2 . initialisation de la FirstFormDto
        $dto = $this->firstFormDtoFactory->create();

        //3. creation du formualire
        $form = $this->createForm(FirstFormType::class, $dto);
        //4. traitement du formualire
        $response = $this->traitemementForm($form, $request);

        if ($response instanceof RedirectResponse) {
            return $response;
        }

        //5. rendu de la vue
        return $this->render('hf/rh/dom/creation/firstForm.html.twig', [
            'form' => $form->createView(),
            'breadcrumbs' => $breadcrumbBuilder->build('dom_first_form'),
        ]);
    }

    private function traitemementForm(FormInterface $form, Request $request)
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->logger->info('Premier formulaire soumis et valide.');

            // 1. Récupération des données du formulaire
            $dto = $form->getData();

            // Manually set IDs from unmapped fields
            $typeMission = $form->get('typeMission')->getData();
            $categorie = $form->get('categorie')->getData();

            $dto->typeMissionId = $typeMission ? $typeMission->getId() : null;
            $dto->categorieId = $categorie ? $categorie->getId() : null;

            $this->logger->debug('Données du formulaire', ['data' => $dto]);

            // 2. Stockage des données dans la session
            $session = $request->getSession();
            $session->set('dom_first_form_data', $dto);

            // 3. Redirection vers le second formulaire
            return $this->redirectToRoute('dom_second_form');
        }
        return null;
    }
}
