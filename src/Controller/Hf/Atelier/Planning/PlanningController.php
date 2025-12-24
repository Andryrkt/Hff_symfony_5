<?php

namespace App\Controller\Hf\Atelier\Planning;

use App\Service\TimelineDataService;
use App\Service\Traits\ArrayHelperTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Model\Hf\Atelier\Planning\PlanningModel;
use App\Repository\Hf\Atelier\Dit\DitRepository;
use App\Dto\Hf\Atelier\Planning\PlanningSearchDto;
use App\Dto\Hf\Atelier\Planning\PlanningMaterielDto;
use App\Repository\Hf\Atelier\Dit\Ors\OrsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PlanningController extends AbstractController
{
    use ArrayHelperTrait;

    private PlanningModel $planningModel;
    private DitRepository $ditRepository;

    public function __construct(
        PlanningModel $planningModel,
        DitRepository $ditRepository
    ) {
        $this->planningModel = $planningModel;
        $this->ditRepository = $ditRepository;
    }

    /**
     * @Route("/hf/atelier/planning", name="hf_atelier_planning")
     */
    public function index(OrsRepository $orsRepository, PlanningSearchDto $planningSearchDto, TimelineDataService $timelineDataService): Response
    {
        // recupération des OR valides
        $allOrsValides = $orsRepository->findAllOrsValide();
        //recupération de tous les OR
        $allOrs = $orsRepository->findAllOrs();
        // @var string $backOrder -  Recupération des interventions en back order 
        $backOrder = $this->planningModel->getBackOrderPlanning($allOrsValides, $allOrs, $planningSearchDto);
        // @var array $informationMaterielPlannifier - Recupération des information de matériel à planifier
        $informationMaterielPlannifier = $this->planningModel->getInformationMaterielPlannifier($planningSearchDto, $allOrsValides, $allOrs, $backOrder);
        dd($informationMaterielPlannifier);
        // creation d'une tableau d'objets PlanningMaterielDto
        $tabObjetPlanningMateriel = $this->creationTableauObjetPlanning($informationMaterielPlannifier, $this->stringEnTableau($backOrder));
        // Fusionner les objets en fonction de l'idMat
        $fusionResult = $this->ajoutMoiDetail($tabObjetPlanningMateriel);

        //TODO: encore à rectifier
        $forDisplay = $timelineDataService->prepareDataForDisplay($fusionResult, $planningSearchDto->getMonths() == null ? 3 : $planningSearchDto->getMonths());

        return $this->render('hf_atelier_planning/index.html.twig');
    }

    private function creationTableauObjetPlanning(array $data, array $back): array
    {
        $objetPlanning = [];
        //Recuperation de idmat et les truc
        foreach ($data as $item) {
            $planningMateriel = new PlanningMaterielDto();

            $ditRepositoryConditionner = $this->ditRepository->findOneBy(['numeroOR' => explode('-', $item['orintv'])[0]]);

            /** @var string|null $numDit */
            $numDit = null;
            /** @var int|null $migration */
            $migration = null;
            if ($ditRepositoryConditionner) {
                $numDit = $ditRepositoryConditionner->getNumeroDit();
                $migration = $ditRepositoryConditionner->getNumeroMigration();
            }


            if (in_array($item['orintv'], $back)) {
                $backOrder = 'Okey';
            } else {
                $backOrder = '';
            }

            //initialisation
            $planningMateriel
                ->codeSuc = $item['codesuc']
                ->libSuc = $item['libsuc']
                ->codeServ = $item['codeserv']
                ->libServ = $item['libserv']
                ->idMat = $item['idmat']
                ->marqueMat = $item['markmat']
                ->typeMat = $item['typemat']
                ->numSerie = $item['numserie']
                ->numParc = $item['numparc']
                ->casier = $item['casier']
                ->annee = $item['annee']
                ->mois = $item['mois']
                ->orIntv = $item['orintv']
                ->qteCdm = $item['qtecdm']
                ->qteLiv = $item['qtliv']
                ->qteAll = $item['qteall']
                ->numDit = $numDit
                ->addMoisDetail($item['mois'], $item['annee'], $item['orintv'], $item['qtecdm'], $item['qtliv'], $item['qteall'], $numDit, $migration, $item['commentaire'], $backOrder);
            $objetPlanning[] = $planningMateriel;
        }
        return $objetPlanning;
    }

    private function ajoutMoiDetail(array $objetPlanning): array
    {
        // Fusionner les objets en fonction de l'idMat
        $fusionResult = [];
        foreach ($objetPlanning as $materiel) {
            $key = $materiel->idMat; // Utiliser idMat comme clé unique
            if (!isset($fusionResult[$key])) {
                $fusionResult[$key] = $materiel; // Si la clé n'existe pas, on l'ajoute
            } else {
                // Si l'élément existe déjà, on fusionne les détails des mois
                foreach ($materiel->moisDetails as $moisDetail) {

                    $fusionResult[$key]->addMoisDetail(
                        $moisDetail['mois'],
                        $moisDetail['annee'],
                        $moisDetail['orIntv'],
                        $moisDetail['qteCdm'],
                        $moisDetail['qteLiv'],
                        $moisDetail['qteAll'],
                        $moisDetail['numDit'],
                        $moisDetail['migration'],
                        $moisDetail['commentaire'],
                        $moisDetail['back']
                    );
                }
            }
        }
        return $fusionResult;
    }
}
