<?php

namespace App\Controller\Hf\Rh\Dom\Creation;

use App\Dto\Hf\Rh\Dom\FirstFormDto;
use App\Factory\Hf\Rh\Dom\FirstFormDtoFactory;
use App\Form\Hf\Rh\Dom\FirstFormType;
use App\Service\Navigation\ContextAwareBreadcrumbBuilder;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
        return $this->render('hf/rh/dom/firstForm.html.twig', [
            'form' => $form->createView(),
            'breadcrumbs' => $breadcrumbBuilder->build('dom_first_form'),
        ]);
    }

    private function traitemementForm(FormInterface $form, Request $request)
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->logger->info('Premier formulaire soumis et valide.');
            // 1. recupération des données du formulaire
            $data = $form->getData();

            $this->logger->debug('Données du formulaire', ['data' => $data]);

            // 2. stocage des donner dans le session
            $session = $request->getSession();
            $session->set('dom_first_form_data', $data);

            // 3. Redirection vers le second formulaire
            return $this->redirectToRoute('dom_second_form');
        }
        return null;
    }
}
