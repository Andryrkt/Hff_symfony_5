<?php

namespace App\Mapper\Hf\Atelier\Dit\Soumission\Ors;

use App\Dto\Hf\Atelier\Dit\Soumission\Ors\OrsDto;
use App\Dto\Hf\Atelier\Dit\Soumission\Ors\PieceFaibleAchatDto;
use App\Model\Hf\Atelier\Dit\Soumission\Ors\OrsModel;

class PieceFaibleAchatMapper
{
    private OrsModel $orsModel;
    private $parameters;

    public function __construct(OrsModel $orsModel, \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface $parameters)
    {
        $this->orsModel = $orsModel;
        $this->parameters = $parameters;
    }

    public function mapToDtos(OrsDto $orsDto, array $infoSurLesOrs = []): array
    {
        $pieceFaibleAchatDtos = [];

        // Si les données ne sont pas fournies, on les récupère (comportement d'origine)
        // Sinon on les filtre en mémoire pour économiser une requête
        if (empty($infoSurLesOrs)) {
            $infoOrs = $this->orsModel->getInfoOrsPourConstructeurMagasin($orsDto->numeroDit, $orsDto->numeroOr);
        } else {
            $piecesMagasin = explode(',', str_replace("'", "", $this->parameters->get('app.constructeurs.pieces_magasin')));
            $infoOrs = array_filter($infoSurLesOrs, function ($info) use ($piecesMagasin) {
                return in_array($info['constructeur'] ?? '', $piecesMagasin);
            });
        }

        foreach ($infoOrs as $infoOr) {
            $pieceFaibleAchatDto = new PieceFaibleAchatDto();

            $afficher = $this->orsModel->getPieceFaibleActiviteAchat($infoOr['constructeur'], $infoOr['reference'], $orsDto->numeroOr);

            if (isset($afficher[0]) && $afficher[0]['retour'] === 'a afficher') {
                $pieceFaibleAchatDto->numeroItv = (int) ($infoOr['numero_itv'] ?? 0);
                $pieceFaibleAchatDto->libelleItv = (string) ($infoOr['libelle_itv'] ?? '');
                $pieceFaibleAchatDto->constructeur = (string) ($infoOr['constructeur'] ?? '');
                $pieceFaibleAchatDto->reference = (string) ($infoOr['reference'] ?? '');
                $pieceFaibleAchatDto->designation = (string) ($infoOr['designation'] ?? '');
                $pieceFaibleAchatDto->pmp = (float) ($afficher[0]['pmp'] ?? 0);
                $pieceFaibleAchatDto->dateDerniereCde = $afficher[0]['date_derniere_cde'];

                $pieceFaibleAchatDtos[] = $pieceFaibleAchatDto;
            }
        }
        return $pieceFaibleAchatDtos;
    }
}
