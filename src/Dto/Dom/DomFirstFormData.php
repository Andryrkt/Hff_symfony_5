<?php

// src/Dto/Dom/DefaultDomFormData.php
namespace App\Dto\Dom;

use App\Entity\Dom\DomCategorie;
use App\Entity\Dom\DomSousTypeDocument;
use App\Entity\Admin\PersonnelUser\Personnel;

class DomFirstFormData
{
    // Données par défaut (remplies automatiquement)
    public ?string $agenceEmetteur = null;
    public ?string $serviceEmetteur = null;
    public string $salarie = 'PERMANENT';

    // Champs du formulaire (sélectionnables/modifiables)
    public ?DomSousTypeDocument $sousTypeDocument = null;
    public ?DomCategorie $categorie = null;
    public ?Personnel $matriculeNom = null;
    public ?string $matricule = null;
    public ?string $nom = null;
    public ?string $prenom = null;
    public ?string $cin = null;
}
