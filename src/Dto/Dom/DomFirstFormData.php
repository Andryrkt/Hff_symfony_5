<?php

namespace App\Dto\Dom;

use App\Entity\Dom\DomCategorie;
use App\Entity\Dom\DomSousTypeDocument;
use App\Entity\Admin\PersonnelUser\User;
use App\Entity\Admin\PersonnelUser\Personnel;
use App\Repository\Dom\DomSousTypeDocumentRepository;

class DomFirstFormData
{
    // Données par défaut (remplies automatiquement)
    public ?string $agenceEmetteur = null;
    public ?string $serviceEmetteur = null;
    public string $salarie = 'PERMANENT';

    // Champs du formulaire
    public ?DomSousTypeDocument $sousTypeDocument = null;
    public ?DomCategorie $categorie = null;
    public ?Personnel $matriculeNom = null;
    public ?string $matricule = null;
    public ?string $nom = null;
    public ?string $prenom = null;
    public ?string $cin = null;

    public function __construct(DomSousTypeDocumentRepository $sousTypeDocumentRepository, ?User $user = null)
    {
        $this->sousTypeDocument = $sousTypeDocumentRepository->findOneBy([
            'codeSousType' => DomSousTypeDocument::CODE_SOUS_TYPE_MISSION,
        ]);
    }
}
