<?php

namespace App\Controller\Api\Hf\Materiel\Casier;

use App\Entity\Admin\AgenceService\Agence;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Hf\Materiel\Casier\Casier;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\Admin\Statut\StatutDemandeRepository;
use App\Constants\Admin\Historisation\TypeDocumentConstants;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/casier/{id}/validate", name="api_casier_validate", methods={"POST"})
     */
    public function validateCasier(Casier $casier, EntityManagerInterface $em, StatutDemandeRepository $statutDemandeRepository): JsonResponse
    {
        try {
            if ($casier->isValid()) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Ce casier est déjà validé.'
                ], 400); // Bad Request
            }

            $casier->setIsValide(true)
                ->setStatutDemande($statutDemandeRepository->findOneBy(['codeApplication' => TypeDocumentConstants::TYPE_DOCUMENT_CAS_CODE, 'description' => 'VALIDER']))
            ;
            $em->flush();

            return new JsonResponse([
                'success' => true,
                'message' => sprintf('Le casier "%s" a été validé avec succès.', $casier->getNom())
            ]);
        } catch (\Exception $e) {
            // Log the exception details here if you have a logger configured
            return new JsonResponse([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la validation du casier.'
            ], 500); // Internal Server Error
        }
    }

    /**
     * @Route("/api/casier-fetch/{id}", name="api_casier_fetch", methods={"GET"})
     */
    public function fetchCasiers(Agence $agence): JsonResponse
    {
        $casiers = $agence->getCasierPhps();
        
        $data = [];
        foreach ($casiers as $casier) {
            $data[] = [
                'value' => $casier->getId(),
                'text' => $casier->getNom(),
            ];
        }

        return new JsonResponse($data);
    }
}
