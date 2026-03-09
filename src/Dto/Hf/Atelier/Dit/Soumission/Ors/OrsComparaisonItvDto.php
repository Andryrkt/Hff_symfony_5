<?php

namespace App\Dto\Hf\Atelier\Dit\Soumission\Ors;

/**
 * Représente une ligne de comparaison Avant/Après pour une intervention d'OR.
 */
class OrsComparaisonItvDto
{
    public int $itv;
    public string $libelleItv = '';
    public ?\DateTime $datePlanning = null;

    // Valeurs AVANT
    public int $nbLigAv = 0;
    public float $mttTotalAv = 0.0;

    // Valeurs APRES
    public int $nbLigAp = 0;
    public float $mttTotalAp = 0.0;

    public string $statut = '';
}
