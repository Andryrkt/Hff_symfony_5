<?php

namespace App\Dto\Dom;

use App\Entity\Dom\DomCategorie;
use App\Entity\Dom\DomSousTypeDocument;
use Doctrine\ORM\EntityManagerInterface;

class DomSecondFormData
{
    private ?string $agenceEmetteur = null;
    private ?string $serviceEmetteur = null;
    private string $salarie;
    private ?DomSousTypeDocument $sousTypeDocument = null;
    private ?DomCategorie $categorie = null;
    private ?string $matricule = null;
    private ?string $nom = null;
    private ?string $prenom = null;
    private ?string $cin = null;

    private ?string $agenceDebiteur = null;
    private ?string $serviceDebiteur = null;


    public function populateFromStep1(array $step1Data, EntityManagerInterface $em): self
    {
        if (isset($step1Data['sousTypeDocument']) && $step1Data['sousTypeDocument']) {
            $sousType = $em->find(DomSousTypeDocument::class, $step1Data['sousTypeDocument']);
            if ($sousType) {
                $this->setSousTypeDocument($sousType);
            }
        }

        if (isset($step1Data['categorie']) && $step1Data['categorie']) {
            $categorie = $em->find(DomCategorie::class, $step1Data['categorie']);
            if ($categorie) {
                $this->setCategorie($categorie);
            }
        }
        $this->setSalarie($step1Data['salarie'] ?? 'PERMANENT');
        $this->setMatricule($step1Data['matricule'] ?? null);
        $this->setNom($step1Data['nom'] ?? null);
        $this->setPrenom($step1Data['prenom'] ?? null);
        $this->setCin($step1Data['cin'] ?? null);

        return $this;
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

    /**
     * Get the value of agenceDebiteur
     */
    public function getAgenceDebiteur()
    {
        return $this->agenceDebiteur;
    }

    /**
     * Set the value of agenceDebiteur
     *
     * @return  self
     */
    public function setAgenceDebiteur($agenceDebiteur)
    {
        $this->agenceDebiteur = $agenceDebiteur;

        return $this;
    }

    /**
     * Get the value of serviceDebiteur
     */
    public function getServiceDebiteur()
    {
        return $this->serviceDebiteur;
    }

    /**
     * Set the value of serviceDebiteur
     *
     * @return  self
     */
    public function setServiceDebiteur($serviceDebiteur)
    {
        $this->serviceDebiteur = $serviceDebiteur;

        return $this;
    }
}
