<?php

namespace App\Dto\Hf\Materiel\Casier;

use App\Entity\Admin\AgenceService\Agence;
use App\Entity\Admin\Statut\StatutDemande;

class SecondFormDto
{
    public string $agenceUser;

    public string $serviceUser;

    public string $designation = "";

    public int $idMateriel;

    public int $numParc;

    public string $numSerie;

    public string $groupe;

    public string $constructeur = "";

    public string $modele = "";

    public string $anneeDuModele;

    public string $affectation;

    public string $dateAchat;


    public ?\DateTime $dateDemande = null;

    public string $nom; //casier du materiel

    public string $numeroCasier; // numero de casier de creation

    public Agence $agenceRattacher;

    public StatutDemande $statutDemande;

    /**
     * @Assert\NotBlank(message="Le chantier ne peut pas être vide.")
     * @Assert\Length(
     *      min=3,
     *      max=8,
     *      minMessage="Le chantier doit comporter au moins {{ limit }} caractères",
     *      maxMessage="Le chantier ne peut pas dépasser {{ limit }} caractères"
     * )
     */
    public string $chantier;

    /**
     * @Assert\NotBlank(message="Le client ne peut pas être vide.")
     * @Assert\Length(
     *      min=3,
     *      max=8,
     *      minMessage="Le client doit comporter au moins {{ limit }} caractères",
     *      maxMessage="Le client ne peut pas dépasser {{ limit }} caractères"
     * )
     */
    public string $client;

    /**
     * @Assert\NotBlank(message="Le motif ne peut pas être vide.")
     * @Assert\Length(
     *      min=3,
     *      max=100,
     *      minMessage="Le motif doit comporter au moins {{ limit }} caractères",
     *      maxMessage="Le motif ne peut pas dépasser {{ limit }} caractères"
     * )
     */
    public string $motif;
}
