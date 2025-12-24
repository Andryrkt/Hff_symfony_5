<?php

namespace App\Dto\Hf\Atelier\Planning;

class PlanningMaterielDto
{
    public  $commercial;
    public  $codeSuc;
    public  $libsuc;
    public  $codeServ;
    public  $libServ;
    public  int $idMat;
    public  $marqueMat;
    public  $typeMat;
    public  $numSerie;
    public  $numParc;
    public  $casier;
    public  $annee;
    public  $mois;
    public  $orIntv;
    public  int $qteCdm;
    public  int $qteLiv;
    public  int $qteAll;
    public  $moisDetails = [];
    public  ?string $numDit;
    public  int $migration;
    public  $pos;
    public  $numeroOr;
    public  $commentaire;
    public  $plan;
    public  $back;

    public function addMoisDetail($mois, $annee, $orIntv, $qteCdm, $qteLiv, $qteAll, $numDit, int $migration, $commentaire, $back)
    {
        $this->moisDetails[] = [
            'mois' => $mois,
            'annee' => $annee,
            'orIntv' => $orIntv,
            'qteCdm' => $qteCdm,
            'qteLiv' => $qteLiv,
            'qteAll' => $qteAll,
            'numDit' => $numDit,
            'migration' => $migration,
            'commentaire' => $commentaire,
            'back' => $back
        ];
    }

    public function addMoisDetailMagasin($mois, $annee, $orIntv, $qteCdm, $qteLiv, $qteAll, $commentaire, $back)
    {
        $this->moisDetails[] = [
            'mois' => $mois,
            'annee' => $annee,
            'orIntv' => $orIntv,
            'qteCdm' => $qteCdm,
            'qteLiv' => $qteLiv,
            'qteAll' => $qteAll,
            'commentaire' => $commentaire,
            'back' => $back
        ];
    }
}
