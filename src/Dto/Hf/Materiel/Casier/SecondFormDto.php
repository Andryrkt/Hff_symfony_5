<?php

namespace App\Dto\Hf\Materiel\Casier;

use App\Entity\Admin\AgenceService\Agence;
use App\Entity\Admin\Statut\StatutDemande;

class SecondFormDto
{
    public string $nom;

    public string $numero;

    public Agence $agence_rattacher;

    public StatutDemande $statutDemande;

    public string $agenceUser;

    public string $serviceUser;

    public int $idMateriel;

    public int $numParc;

    public string $numSerie;

    public string $constructeur = "";

    public string $designation = "";

    public string $modele = "";

    public string $groupe;

    public string $anneeDuModele;

    public string $affectation;

    public string $dateAchat;

    public string $chantier;

    public string $client;

    public string $motif;
}
