<?php

namespace App\Controller\Hf\Rh\Dom\Creation;

use App\Form\Hf\Rh\Dom\SecondFormType;
use App\Repository\Hf\Rh\Dom\DomRepository;
use App\Factory\Hf\Rh\Dom\SecondFormDtoFactory;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/hf/rh/ordre-de-mission")
 */
class DomDuplicationController extends AbstractController
{
    /**
     * @Route("/duplication/{numeroOrdreMission}", name="dom_duplication")
     */
    public function index(
        string $numeroOrdreMission,
        DomRepository $domRepository,
        SecondFormDtoFactory $secondFormDtoFactory
    ) {

        // recuperation des données du numéro d'Ordre de mission
        $dom = $domRepository->findOneBy(['numeroOrdreMission' => $numeroOrdreMission]);

        // hydratation du secondFormDto
        $secondFormDto = $secondFormDtoFactory->createFromDom($dom);

        // Mesure: Création du formulaire Symfony
        $form = $this->createForm(SecondFormType::class, $secondFormDto);

        return $this->render('hf/rh/dom/creation/dom_duplication.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
