<?php

namespace App\Controller\Dom;

use App\Entity\Dom\DomRmq;
use App\Entity\Dom\DomCategorie;
use App\Dto\Dom\DomFirstFormData;
use App\Form\Dom\DomFirstFormType;
use App\Service\Dom\DomWizardManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\Dom\DomIndemniteRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\Dom\DomSousTypeDocumentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DomFirstFormController extends AbstractController
{
    /**
     * @Route("/dom/first", name="dom_first")
     * 
     * @param Request $request
     * @param DomWizardManager $wizardManager
     * @return void
     */
    public function index(Request $request, DomWizardManager $domWizardManager, DomSousTypeDocumentRepository $sousTypeDocumentRepository): Response
    {
        // Récupération des données existantes ou nouveau DTO
        $dto = $domWizardManager->getStep1Data() ?? new DomFirstFormData($sousTypeDocumentRepository, $this->getUser());

        $form = $this->createForm(DomFirstFormType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $domWizardManager->saveStep1Data($dto);

            return $this->redirectToRoute('dom_step2');
        }

        return $this->render('dom/domFirstForm.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/dom/categories", name="dom_categories_fetch")
     */
    public function fetchCategories(Request $request, DomIndemniteRepository $domIndemniteRepository, EntityManagerInterface $em): JsonResponse
    {
        $typeDoc = $request->query->get('typeDoc');
        $agence = $request->query->get('agence');

        if (!$typeDoc || !$agence) {
            return $this->json(['error' => 'Paramètres typeDoc ou agence manquants'], 400);
        }

        // dd($typeDoc, $agence);
        $rmqDescription = str_starts_with($agence, '50')
            ? DomRmq::DESCRIPTION_50
            : DomRmq::DESCRIPTION_STD;

        $rmq = $em->getRepository(DomRmq::class)->findOneBy(['description' => $rmqDescription]);

        if (!$rmq) {
            return $this->json(['error' => 'RMQ introuvable pour ' . $rmqDescription], 404);
        }

        $categories = $domIndemniteRepository->findDistinctCategoriesByCriteria([
            'sousTypeDoc' => $typeDoc,
            'rmq' => $rmq,
        ]);

        return $this->json(array_map(fn(DomCategorie $cat) => [
            'id' => $cat->getId(),
            'description' => $cat->getDescription()
        ], $categories));
    }
}
