<?php

namespace App\Dto\Hf\Materiel\Badm;

use App\Entity\Admin\Statut\StatutDemande;
use App\Entity\Hf\Materiel\Badm\TypeMouvement;
use Symfony\Component\Validator\Constraints as Assert;
use App\Constants\Hf\Materiel\Badm\TypeMouvementConstants;

class SecondFormDto
{
    // --------------- Caracteristique du matériel ---------------
    public string $designation = "";
    public int $idMateriel;
    public ?int $numParc;
    public string $numSerie;
    public string $groupe;
    public string $constructeur = "";
    public string $modele = "";
    public string $anneeDuModele;
    public string $affectation;
    public string $dateAchat;
    // --------------- Etat machine -----------------
    public ?int $heureMachine;
    public ?int $kmMachine;
    // ---------------- Agence, service et casier emetteur ----------------
    public ?array $emetteur = null;
    // ---------------- Agence, service et casier destinataire ----------------
    public ?array $destinataire = null;
    /**
     * @Assert\NotBlank(message="Le motif ne peut pas être vide.")
     * @Assert\Length(
     *      min=3,
     *      max=100,
     *      minMessage="Le motif doit comporter au moins {{ limit }} caractères",
     *      maxMessage="Le motif ne peut pas dépasser {{ limit }} caractères"
     * )
     */
    public string $motifMateriel;
    // ---------------- Entrée en parc ----------------
    public ?string $etatAchat;
    public ?\DateTime $dateMiseLocation = null;
    // ---------------- Valeur ----------------
    public ?float $coutAcquisition;
    public ?float $amortissement;
    public ?float $valeurNetComptable;

    // ---------------- cession d'actif ----------------
    public string $nomClient;
    public string $modalitePaiement;
    public float $prixVenteHt;
    // ---------------- Mise au rebut -----------------
    /**
     * @Assert\NotBlank(message="Le motif ne peut pas être vide.")
     * @Assert\Length(
     *      min=3,
     *      max=100,
     *      minMessage="Le motif doit comporter au moins {{ limit }} caractères",
     *      maxMessage="Le motif ne peut pas dépasser {{ limit }} caractères"
     * )
     */
    public string $motifMiseRebut;
    public string $nomImage;
    public string $nomFichier;
    // --------------- mouvement materiel ---------------
    public TypeMouvement $typeMouvement;
    public string $numeroBadm;
    public StatutDemande $statutDemande;
    public \DateTime $dateDemande;


    public function getTypeMouvementCssClass(): string
    {
        return TypeMouvementConstants::getCssClass($this->typeMouvement->getDescription());
    }
}
