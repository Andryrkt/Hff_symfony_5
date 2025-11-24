<?php

namespace App\Controller\Api\Hf\Rh\Dom;

use App\Entity\Admin\PersonnelUser\Personnel;
use App\Entity\Hf\Rh\Dom\Categorie;
use App\Entity\Hf\Rh\Dom\Indemnite;
use App\Entity\Hf\Rh\Dom\Rmq;
use App\Entity\Hf\Rh\Dom\Site;
use App\Entity\Hf\Rh\Dom\SousTypeDocument;
use App\Repository\Hf\Rh\Dom\DomRepository;
use App\Service\Utils\FormattingService;
use Doctrine\ORM\EntityManagerInterface;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MissionValidationController extends AbstractController
{
    /**
     * @Route("/api/rh/dom/indemnite-forfaitaire", name="api_rh_dom_indemnite_forfaitaire", methods={"GET"})
     */
    public function getMontantIndemniteForfaitaire(Request $request, EntityManagerInterface $em, FormattingService $formattingService): JsonResponse
    {
        $typeMission = $em->getRepository(SousTypeDocument::class)->findOneBy(['codeSousType' => $request->query->get('typeMission')]);
        $categorie = $em->getRepository(Categorie::class)->findOneBy(['description' => $request->query->get('categorie')]);
        $site = $em->getRepository(Site::class)->find($request->query->get('site'));
        $rmq = $em->getRepository(Rmq::class)->find($request->query->get('rmq'));

        $montant = 0;

        if ($typeMission && $categorie && $site && $rmq) {
            $criteria = [
                'sousTypeDocument' => $typeMission,
                'rmq' => $rmq,
                'categorie' => $categorie,
                'site' => $site
            ];

            $indemnite = $em->getRepository(Indemnite::class)->findOneBy($criteria);

                            if ($indemnite) {
                                $montant = $indemnite->getMontant();
                            }        }
        if ($typeMission) {
            if ($typeMission->getCodeSousType() === SousTypeDocument::CODE_TROP_PERCU) {
                $montant = 0;
            } else if ($typeMission->getCodeSousType() === SousTypeDocument::CODE_COMPLEMENT || $typeMission->getCodeSousType() === SousTypeDocument::CODE_MUTATION) {
                $montant  = '';
            }
        }

        $formattedMontant = ($montant === '') ? '' : $formattingService->formatNumber($montant, 0);

        return new JsonResponse(['montant' => $formattedMontant]);
    }
    /**
     * @Route("/api/rh/dom/mode", name="api_rh_dom_mode", methods={"GET"})
     */
    public function getCodeBancaire(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $matricule = $request->query->get('matricule');

        if (!$matricule) {
            return new JsonResponse(['error' => 'Les paramètres matricule est requis.'], 400);
        }

        $personnel = $em->getRepository(Personnel::class)->findOneBy(['matricule' => $matricule]);

        if (!$personnel) {
            return new JsonResponse(['error' => 'Personne non trouvable'], 400);
        }

        $codeBancaire = $personnel->getNumeroCompteBancaire();

        return new JsonResponse(['codeBancaire' => $codeBancaire]);
    }

    /**
     * @Route("/api/validation/mission-overlap", name="api_mission_overlap_check")
     */
    public function checkMissionOverlap(Request $request, DomRepository $missionRepository): JsonResponse
    {
        $matricule = $request->query->get('matricule');
        $startDate = $request->query->get('start_date');
        $endDate = $request->query->get('end_date');

        if (!$matricule || !$startDate || !$endDate) {
            return new JsonResponse(['error' => 'Les paramètres matricule, start_date et end_date sont requis.'], 400);
        }

        try {
            $startDate = new \DateTime($startDate);
            $endDate = new \DateTime($endDate);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Format de date invalide.'], 400);
        }

        // On suppose que votre MissionRepository aura cette méthode
        $overlapExists = $missionRepository->hasOverlappingMission($matricule, $startDate, $endDate);

        return new JsonResponse(['overlap' => $overlapExists]);
    }
}
