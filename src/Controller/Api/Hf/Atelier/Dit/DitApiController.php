<?php

namespace App\Controller\Api\Hf\Atelier\Dit;

use App\Entity\Hf\Atelier\Dit\Dit;
use App\Model\Hf\Atelier\Dit\DitModel;
use App\Repository\Hf\Atelier\Dit\DitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/hf/atelier/dit/async")
 */
class DitApiController extends AbstractController
{
    /**
     * @Route("/check-numero-or-batch", name="api_hf_atelier_dit_check_numero_or_batch", methods={"POST"})
     */
    public function checkNumeroOrBatch(
        Request $request,
        DitModel $ditModel,
        DitRepository $ditRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        $content = json_decode($request->getContent(), true);
        $numeroDits = $content['numeroDits'] ?? [];

        if (empty($numeroDits)) {
            return new JsonResponse(['results' => []]);
        }

        $results = $ditModel->getNumeroOrBatch($numeroDits);
        $foundMapping = [];

        foreach ($results as $result) {
            $numDit = $result['numero_dit'];
            $numeroOr = trim($result['numero_or']);

            $foundMapping[$numDit] = $numeroOr;

            // Optionnel : Enregistrer immédiatement en base si non présent
            $dit = $ditRepository->findOneBy(['numeroDit' => $numDit]);
            if ($dit && empty($dit->getNumeroOr())) {
                $dit->setNumeroOr($numeroOr);
            }
        }

        if (!empty($foundMapping)) {
            $em->flush();
        }

        return new JsonResponse([
            'results' => $foundMapping
        ]);
    }
}
