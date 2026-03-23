<?php

namespace App\Controller\Api\Hf\Atelier\Dit;

use stdClass;
use App\Model\Hf\Materiel\Badm\BadmModel;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MaterielApi extends AbstractController
{
    private BadmModel $badmModel;

    public function __construct(BadmModel $badmModel)
    {
        $this->badmModel = $badmModel;
    }

    /**
     * @Route("/ajax/fetch-materiel", name="ajax_fetch_materiel", methods={"GET"})
     */
    public function fetchMateriel(): JsonResponse
    {
        // On récupère tout si pas de paramètres (cas de l'auto-complete)
        // Les param ID/Parc/Serie peuvent être ajoutés via Request $request si besoin plus tard
        $searchDto = new stdClass();
        $searchDto->idMateriel = null;
        $searchDto->numParc = null;
        $searchDto->numSerie = null;

        $rows = $this->badmModel->getInfoMateriel($searchDto);

        if (empty($rows)) {
            return new JsonResponse(['message' => 'Aucun matériel trouvé'], 404);
        }

        $formattedData = [];
        foreach ($rows as $data) {
            $formattedData[] = [
                'constructeur' => $data['constructeur'] ?? null,
                'designation' => $data['designation'] ?? null,
                'km' => $data['km_machine'] ?? null,
                'num_parc' => $data['num_parc'] ?? null,
                'modele' => $data['modele'] ?? null,
                'casier_emetteur' => $data['casier_emetteur'] ?? null,
                'heure' => $data['heure_machine'] ?? null,
                'num_serie' => $data['num_serie'] ?? null,
                'num_matricule' => $data['num_matricule'] ?? null,
            ];
        }

        return new JsonResponse($formattedData);
    }
}
