<?php

namespace App\Controller\Hf\Atelier\Planning;


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
    public function index(OrsRepository $orsRepository, PlanningSearchDto $planningSearchDto): Response
    {
        // recupération des OR valides sous forme de chaine de caractère
        // $allOrsValidesString = $orsRepository->findAllOrsValideString();
        $allOrsValidesString = "'16413111','16413136','16406497','16406492','16409585','16409591','16409809','16409808','16409807','16409806','16409804','16409800','16409801','16409798','16409797','16409796','16410172','16410302','16410306','16410308','16410307','16410441','16410553','16410722','16410737','16410739','16411270','16411265','16410842','16411386','16411716','16411742','16411732','16411731','16411754','16411748','16411745','16411749','16411750','16411945','16407719','16409137','16409586','16409584','16409970','16410727','16410740','16410200','16411173','16411412','16411792','16411805','16411881','16411879','16411926','16411975','16411983','16412302','16412358','16412391','16412392','16412383','16412405','16412424','16412452','16412476','16412493','16412490','16412545','16412574','16412573','16412577','16412607','16412663','16412665','16412666','16412661','16412667','16412642','16412657','16412658','16412649','16412662','16412647','16412698','16412737','16412739','16412740','16412753','16412755','16412756','16412758','16412760','16412765','16412770','16412810','16412811','16412812','16412842','16412851','16412868','16412871','16412872','16412873','16412942','16412953','16412954','16412955','16412997','16413079','16413080','16413081','16411974','16413258','16413260','16413262','16413264','16413266','16413220','16413389','16413390','16413391','16413388','16413423','16412762','16413155','16413424','16413437','16413257','16413422','16413547','16413548','16412809','16413088','16413609','16412329','16413261','16413811','16413812','16413809','16413808','16413651','16410791','16411365','16411422','16411484','16411487','16411700','16411845','16411853','16411976','16412195','16412272','16412340','16412363','16412423','16412630','16412859','16412874','16412961','16412967','16412998','16413002','16413125','16413272','16413425','16413545','16413568','16413600','16413616','16413625','16413646','16413660','16413664','16413694','16413706','16413827','16409673','51302233','51302245','51302250','51302259','51302276','51302278','51302282','51302292','51302297','51302302','51302311','51302322','51302331','51302341','51302346','51302349','51302350','51302351','51302358','51302360','51302361','51302364'";
        //recupération de tous les OR sous forme de chaine de caractère
        // $allOrsString = $orsRepository->findAllOrsString();
        $allOrsString = "'16413111','16413136','16406497','16406492','16409585','16409591','16409809','16409808','16409807','16409806','16409804','16409800','16409801','16409798','16409797','16409796','16410172','16410302','16410306','16410308','16410307','16410441','16410553','16410722','16410737','16410739','16411270','16411265','16410842','16411386','16411716','16411742','16411732','16411731','16411754','16411748','16411745','16411749','16411750','16411945','16407719','16409137','16409586','16409584','16409970','16410727','16410740','16410200','16411173','16411412','16411792','16411805','16411881','16411879','16411926','16411975','16411983','16412302','16412358','16412391','16412392','16412383','16412405','16412424','16412452','16412476','16412493','16412490','16412545','16412574','16412573','16412577','16412607','16412663','16412665','16412666','16412661','16412667','16412642','16412657','16412658','16412649','16412662','16412647','16412698','16412737','16412739','16412740','16412753','16412755','16412756','16412758','16412760','16412765','16412770','16412810','16412811','16412812','16412842','16412851','16412868','16412871','16412872','16412873','16412942','16412953','16412954','16412955','16412997','16413079','16413080','16413081','16411974','16413258','16413260','16413262','16413264','16413266','16413220','16413389','16413390','16413391','16413388','16413423','16412762','16413155','16413424','16413437','16413257','16413422','16413547','16413548','16412809','16413088','16413609','16412329','16413261','16413811','16413812','16413809','16413808','16413651','16410791','16411365','16411422','16411484','16411487','16411700','16411845','16411853','16411976','16412195','16412272','16412340','16412363','16412423','16412630','16412859','16412874','16412961','16412967','16412998','16413002','16413125','16413272','16413425','16413545','16413568','16413600','16413616','16413625','16413646','16413660','16413664','16413694','16413706','16413827','16409673','51302233','51302245','51302250','51302259','51302276','51302278','51302282','51302292','51302297','51302302','51302311','51302322','51302331','51302341','51302346','51302349','51302350','51302351','51302358','51302360','51302361','51302364'";
        // @var string $backOrder -  Recupération des interventions en back order 
        $backOrder = $this->planningModel->getBackOrderPlanning($allOrsValidesString, $allOrsString, $planningSearchDto);
        // @var array $informationMaterielPlannifier - Recupération des information de matériel à planifier
        $informationMaterielPlannifier = $this->planningModel->getInformationMaterielPlannifier($planningSearchDto, $allOrsValidesString, $allOrsString, $backOrder);
        dd($informationMaterielPlannifier);
        // creation d'une tableau d'objets PlanningMaterielDto
        $tabObjetPlanningMateriel = $this->creationTableauObjetPlanning($informationMaterielPlannifier, $this->stringEnTableau($backOrder));
        // Fusionner les objets en fonction de l'idMat
        $fusionResult = $this->ajoutMoiDetail($tabObjetPlanningMateriel);
        dd($fusionResult);


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
