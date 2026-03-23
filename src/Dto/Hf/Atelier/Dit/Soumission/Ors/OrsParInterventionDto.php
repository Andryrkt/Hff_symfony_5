<?php

namespace App\Dto\Hf\Atelier\Dit\Soumission\Ors;

class OrsParInterventionDto
{
    public int $numeroItv = 0;
    public int $nombreLigneItv = 0;
    public float $montantItv = 0;
    public float $montantPiece = 0;
    public float $montantMo = 0;
    public float $montantAchatLocaux = 0;
    public float $montantFraisDivers = 0; // montant autres
    public float $montantLubrifiants = 0;
    public string $libellelItv = '';
    public ?\DateTime $datePlanning = null;
}
