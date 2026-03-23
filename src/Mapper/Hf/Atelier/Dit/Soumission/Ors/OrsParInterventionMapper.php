<?php

namespace App\Mapper\Hf\Atelier\Dit\Soumission\Ors;

use App\Dto\Hf\Atelier\Dit\Soumission\Ors\OrsDto;
use App\Dto\Hf\Atelier\Dit\Soumission\Ors\OrsParInterventionDto;
use App\Model\Hf\Atelier\Dit\Soumission\Ors\OrsModel;

class OrsParInterventionMapper
{
    private OrsModel $orsModel;

    public function __construct(OrsModel $orsModel)
    {
        $this->orsModel = $orsModel;
    }

    /**
     * Mappe les données Informix vers un tableau de DTOs d'interventions
     * 
     * @param OrsDto $orsDto
     * @return OrsParInterventionDto[]
     */
    public function mapToDtos(OrsDto $orsDto): array
    {
        $infoSurLesOrs = $this->orsModel->getInfoOrs($orsDto->numeroDit, $orsDto->numeroOr);

        return $this->mapFromRawData($infoSurLesOrs);
    }

    /**
     * @param array $infoSurLesOrs
     * @return OrsParInterventionDto[]
     */
    public function mapFromRawData(array $infoSurLesOrs): array
    {
        $grouped = [];

        foreach ($infoSurLesOrs as $info) {
            $numItv = (int) ($info['numero_itv'] ?? 0);

            if (!isset($grouped[$numItv])) {
                $dto = new OrsParInterventionDto();
                $dto->numeroItv = $numItv;
                $dto->libellelItv = (string) ($info['libelle_itv'] ?? '');
                $dto->datePlanning = new \DateTime($info['date_planning']);
                $dto->nombreLigneItv = 0;
                $grouped[$numItv] = $dto;
            }

            /** @var OrsParInterventionDto $dto */
            $dto = $grouped[$numItv];

            // Si slor_constp est null, c'est une itv sans pièces/MO
            if (!isset($info['constructeur']) || empty($info['constructeur'])) {
                continue;
            }

            $dto->nombreLigneItv++;

            $type = $info['type_ligne'] ?? '';
            $qtePiece = (float)($info['qte_piece'] ?? 0);
            $qteAutre = (float)($info['qte_autre'] ?? 0);
            $prixNet = (float)($info['prix_net'] ?? 0);
            $constr = $info['constructeur'] ?? '';

            $montant = 0;
            if ($type === 'P') {
                // Répartition selon constructeur (Logique historique)
                if ($constr === 'ZST') {
                    $montant = $qtePiece * $prixNet;
                    $dto->montantAchatLocaux += $montant;
                } elseif (strpos($constr, 'Z') === 0) {
                    $montant = $qteAutre * $prixNet;
                    $dto->montantFraisDivers += $montant;
                } elseif ($constr === 'LUB') {
                    $montant = $qtePiece * $prixNet;
                    $dto->montantLubrifiants += $montant;
                } else {
                    $montant = $qtePiece * $prixNet;
                    $dto->montantPiece += $montant;
                }
            } elseif ($type === 'M') {
                $montant = $qteAutre * $prixNet;
                $dto->montantMo += $montant;
            }

            $dto->montantItv += $montant;
        }

        return array_values($grouped);
    }
}
