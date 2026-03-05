<?php

namespace App\Mapper\Hf\Atelier\Dit\Soumission\Ors;

use App\Dto\Hf\Atelier\Dit\Soumission\Ors\OrsDto;
use App\Dto\Hf\Atelier\Dit\Soumission\Ors\PieceFaibleAchatDto;
use App\Model\Hf\Atelier\Dit\Soumission\Ors\OrsModel;

class PieceFaibleAchatMapper
{
    private OrsModel $orsModel;

    public function __construct(OrsModel $orsModel)
    {
        $this->orsModel = $orsModel;
    }

    public function mapToDtos(OrsDto $orsDto): array
    {
        $pieceFaibleAchatDtos = [];

        $infoOrs = $this->orsModel->getInfoOrsPourConstructeurMagasin($orsDto->numeroDit, $orsDto->numeroOr);

        foreach ($infoOrs as $infoOr) {
            $pieceFaibleAchatDto = new PieceFaibleAchatDto();

            $afficher = $this->orsModel->getPieceFaibleActiviteAchat($infoOr['constructeur'], $infoOr['reference'], $orsDto->numeroOr);

            if (isset($afficher[0]) && $afficher[0]['retour'] === 'a afficher') {
                $pieceFaibleAchatDto->numeroItv = $infoOr['numero_itv'];
                $pieceFaibleAchatDto->libelleItv = $infoOr['libelle_itv'];
                $pieceFaibleAchatDto->constructeur = $infoOr['constructeur'];
                $pieceFaibleAchatDto->reference = $infoOr['reference'];
                $pieceFaibleAchatDto->designation = $infoOr['designation'];
                $pieceFaibleAchatDto->pmp = $afficher[0]['pmp'];
                $pieceFaibleAchatDto->dateDerniereCde = $afficher[0]['date_derniere_cde'];

                $pieceFaibleAchatDtos[] = $pieceFaibleAchatDto;
            }
        }
        return $pieceFaibleAchatDtos;
    }
}
