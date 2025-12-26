<?php

namespace App\Mapper\Hf\Atelier\Planning;

use App\Dto\Hf\Atelier\Planning\PlanningMaterielDto;

class PlanningMaterielMapper
{
    public function mapArrayToDto(array $data, array $backOrders = []): PlanningMaterielDto
    {
        $dto = new PlanningMaterielDto();

        // 1. Mapping direct (copie simple)
        $this->mapSimpleProperties($dto, $data);

        // 2. Mapping avec logique
        $this->mapComplexProperties($dto, $data, $backOrders);

        // 3. Appeler la méthode spécifique
        $this->addMoisDetail($dto, $data);

        return $dto;
    }

    private function mapSimpleProperties(PlanningMaterielDto $dto, array $data): void
    {
        $dto->codeSuc = $data['codesuc'];
        $dto->libSuc = $data['libsuc'];
        $dto->codeServ = $data['codeserv'];
        $dto->libServ = $data['libserv'];
        $dto->idMat = $data['idmat'];
        $dto->marqueMat = $data['markmat'];
        $dto->typeMat = $data['typemat'];
        $dto->numSerie = $data['numserie'];
        $dto->numParc = $data['numparc'];
        $dto->casier = $data['casier'];
        $dto->annee = $data['annee'];
        $dto->mois = $data['mois'];
        $dto->orIntv = $data['orintv'];
        $dto->qteCdm = $data['qtecdm'];
        $dto->qteLiv = $data['qtliv'];
        $dto->qteAll = $data['qteall'];
    }

    private function mapComplexProperties(PlanningMaterielDto $dto, array $data, array $backOrders): void
    {
        // Logique du back order
        $dto->backOrder = in_array($data['orintv'], $backOrders) ? 'Okey' : '';

        // Récupération du DIT
        $ditInfo = $this->getDitInfo($data['orintv']);
        $dto->numDit = $ditInfo['numeroDit'];
        $dto->migration = $ditInfo['migration'];
    }

    private function addMoisDetail(PlanningMaterielDto $dto, array $data): void
    {
        $ditInfo = $this->getDitInfo($data['orintv']);

        $dto->addMoisDetail(
            $data['mois'],
            $data['annee'],
            $data['orintv'],
            $data['qtecdm'],
            $data['qtliv'],
            $data['qteall'],
            $ditInfo['numeroDit'],
            $ditInfo['migration'],
            $data['commentaire'] ?? null,
            $dto->backOrder
        );
    }

    private function getDitInfo(string $orintv): array
    {
        $dit = $this->ditRepository->findOneBy([
            'numeroOR' => explode('-', $orintv)[0]
        ]);

        return [
            'numeroDit' => $dit?->getNumeroDit(),
            'migration' => $dit?->getNumeroMigration()
        ];
    }
}
