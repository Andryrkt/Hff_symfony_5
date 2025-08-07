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

    /**
     * Get the value of agenceEmetteur
     */
    public function getAgenceEmetteur()
    {
        return $this->agenceEmetteur;
    }

    /**
     * Set the value of agenceEmetteur
     *
     * @return  self
     */
    public function setAgenceEmetteur($agenceEmetteur)
    {
        $this->agenceEmetteur = $agenceEmetteur;

        return $this;
    }

    /**
     * Get the value of serviceEmetteur
     */
    public function getServiceEmetteur()
    {
        return $this->serviceEmetteur;
    }

    /**
     * Set the value of serviceEmetteur
     *
     * @return  self
     */
    public function setServiceEmetteur($serviceEmetteur)
    {
        $this->serviceEmetteur = $serviceEmetteur;

        return $this;
    }

    /**
     * Get the value of salarie
     */
    public function getSalarie()
    {
        return $this->salarie;
    }

    /**
     * Set the value of salarie
     *
     * @return  self
     */
    public function setSalarie($salarie)
    {
        $this->salarie = $salarie;

        return $this;
    }

    /**
     * Get the value of sousTypeDocument
     */
    public function getSousTypeDocument()
    {
        return $this->sousTypeDocument;
    }

    /**
     * Set the value of sousTypeDocument
     *
     * @return  self
     */
    public function setSousTypeDocument($sousTypeDocument)
    {
        $this->sousTypeDocument = $sousTypeDocument;

        return $this;
    }

    /**
     * Get the value of categorie
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * Set the value of categorie
     *
     * @return  self
     */
    public function setCategorie($categorie)
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get the value of matriculeNom
     */
    public function getMatriculeNom()
    {
        return $this->matriculeNom;
    }

    /**
     * Set the value of matriculeNom
     *
     * @return  self
     */
    public function setMatriculeNom($matriculeNom)
    {
        $this->matriculeNom = $matriculeNom;

        return $this;
    }

    /**
     * Get the value of matricule
     */
    public function getMatricule()
    {
        return $this->matricule;
    }

    /**
     * Set the value of matricule
     *
     * @return  self
     */
    public function setMatricule($matricule)
    {
        $this->matricule = $matricule;

        return $this;
    }

    /**
     * Get the value of nom
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set the value of nom
     *
     * @return  self
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get the value of prenom
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set the value of prenom
     *
     * @return  self
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get the value of cin
     */
    public function getCin()
    {
        return $this->cin;
    }

    /**
     * Set the value of cin
     *
     * @return  self
     */
    public function setCin($cin)
    {
        $this->cin = $cin;

        return $this;
    }
}
