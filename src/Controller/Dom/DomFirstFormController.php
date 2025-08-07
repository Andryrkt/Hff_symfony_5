<?php

namespace App\Controller\Dom;

use App\Entity\Dom\DomRmq;
use Psr\Log\LoggerInterface;
use App\Entity\Dom\DomCategorie;
use App\Dto\Dom\DomFirstFormData;
use App\Form\Dom\DomFirstFormType;
use App\Service\Dom\DomWizardManager;
use App\Entity\Dom\DomSousTypeDocument;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Admin\PersonnelUser\Personnel;
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
     */
    public function index(
        Request $request,
        DomWizardManager $domWizardManager,
        DomSousTypeDocumentRepository $sousTypeDocumentRepository,
        EntityManagerInterface $em,
        LoggerInterface $logger
    ): Response {
        try {
            // Créer le DTO
            $dto = $this->createOrRestoreDto($domWizardManager, $sousTypeDocumentRepository, $em);

            $form = $this->createForm(DomFirstFormType::class, $dto);
            $form->handleRequest($request);

            // CORRECTION: Vérifier d'abord isSubmitted() avant isValid()
            if ($form->isSubmitted()) {
                $logger->info('Form submitted successfully');

                if ($form->isValid()) {
                    $logger->info('Form is valid, saving data');

                    try {
                        // Sauvegarder avec la nouvelle méthode
                        $domWizardManager->saveStep1Data($dto);
                        $logger->info('Data saved successfully, redirecting to step2');

                        $this->addFlash('success', 'Étape 1 sauvegardée avec succès');
                        return $this->redirectToRoute('dom_second');
                    } catch (\Exception $e) {
                        $logger->error('Error saving wizard data: ' . $e->getMessage());
                        $this->addFlash('error', 'Erreur lors de la sauvegarde : ' . $e->getMessage());
                    }
                } else {
                    // Log des erreurs de validation
                    $errors = [];
                    foreach ($form->getErrors(true) as $error) {
                        $errors[] = $error->getMessage();
                        $logger->error('Form validation error: ' . $error->getMessage());
                    }

                    $this->addFlash('error', 'Erreurs de validation : ' . implode(', ', $errors));
                    $logger->error('Form validation failed: ' . implode(', ', $errors));
                }
            }
        } catch (\Exception $e) {
            $logger->error('General error in dom_first: ' . $e->getMessage());
            $this->addFlash('error', 'Erreur générale : ' . $e->getMessage());

            // Fallback en cas d'erreur critique
            $dto = new DomFirstFormData($sousTypeDocumentRepository, $this->getUser());
            $form = $this->createForm(DomFirstFormType::class, $dto);
        }

        return $this->render('dom/domFirstForm.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Crée ou restaure le DTO à partir des données de session
     */
    private function createOrRestoreDto(
        DomWizardManager $domWizardManager,
        DomSousTypeDocumentRepository $sousTypeDocumentRepository,
        EntityManagerInterface $em
    ): DomFirstFormData {

        // Vérifier si on a des données sauvegardées
        $savedData = $domWizardManager->getStep1DataArray();

        if (!$savedData) {
            // Pas de données sauvegardées, créer un nouveau DTO
            return new DomFirstFormData($sousTypeDocumentRepository, $this->getUser());
        }

        // Restaurer le DTO à partir des données sauvegardées
        $dto = new DomFirstFormData($sousTypeDocumentRepository, $this->getUser());

        try {

            if (isset($savedData['sousTypeDocument']) && $savedData['sousTypeDocument']) {
                $sousType = $em->find(DomSousTypeDocument::class, $savedData['sousTypeDocument']);
                if ($sousType) {
                    $dto->setSousTypeDocument($sousType);
                }
            }

            if (isset($savedData['categorie']) && $savedData['categorie']) {
                $categorie = $em->find(DomCategorie::class, $savedData['categorie']);
                if ($categorie) {
                    $dto->setCategorie($categorie);
                }
            }

            if (isset($savedData['matriculeNom']) && $savedData['matriculeNom']) {
                $personnel = $em->find(Personnel::class, $savedData['matriculeNom']);
                if ($personnel) {
                    $dto->setMatriculeNom($personnel);
                }
            }

            // Restaurer les champs simples
            if (isset($savedData['salarie'])) {
                $dto->setSalarie($savedData['salarie']);
            }
            if (isset($savedData['matricule'])) {
                $dto->setMatricule($savedData['matricule']);
            }
            if (isset($savedData['nom'])) {
                $dto->setNom($savedData['nom']);
            }
            if (isset($savedData['prenom'])) {
                $dto->setPrenom($savedData['prenom']);
            }
            if (isset($savedData['cin'])) {
                $dto->setCin($savedData['cin']);
            }
        } catch (\Exception $e) {
            // En cas d'erreur lors de la restauration, retourner un DTO vide
            return new DomFirstFormData($sousTypeDocumentRepository, $this->getUser());
        }

        return $dto;
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

        try {
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
        } catch (\Exception $e) {
            return $this->json(['error' => 'Erreur serveur: ' . $e->getMessage()], 500);
        }
    }
}
