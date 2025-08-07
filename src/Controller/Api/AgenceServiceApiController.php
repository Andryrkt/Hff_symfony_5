<?php

namespace App\Controller\Api;

use App\Entity\Admin\AgenceService\Service;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\Admin\AgenceService\AgenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AgenceServiceApiController extends AbstractController
{
    /**
     * @Route("/api/agences/{agenceId}/services", name="api_agence_services", methods={"GET"})
     */
    public function getAgenceServices(int $agenceId, AgenceRepository $agenceRepository): JsonResponse
    {
        $agence = $agenceRepository->find($agenceId);

        if (!$agence) {
            return new JsonResponse(['error' => 'Agence not found'], Response::HTTP_NOT_FOUND);
        }

        $services = $agence->getServices()->map(function (Service $service) {
            return [
                'id' => $service->getId(),
                'code' => $service->getCode(),
                'name' => $service->getNom(),
            ];
        });

        return new JsonResponse($services->toArray());
    }
}
