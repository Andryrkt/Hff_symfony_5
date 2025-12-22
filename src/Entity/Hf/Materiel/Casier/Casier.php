<?php

namespace App\Entity\Hf\Materiel\Casier;

use App\Entity\Admin\Statut\StatutDemande;
use App\Entity\Traits\CreatedByTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Contract\Entity\CreatedByInterface;
use App\Entity\Admin\AgenceService\Agence;
use App\Repository\Hf\Materiel\Casier\CasierRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CasierRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Casier implements CreatedByInterface
{
    use TimestampableTrait;
    use CreatedByTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $numero;

    /**
     * @ORM\ManyToOne(targetEntity=Agence::class, inversedBy="casierPhps")
     */
    private $agenceRattacher;

    /**
     * @ORM\ManyToOne(targetEntity=StatutDemande::class, inversedBy="casiers")
     */
    private $statutDemande;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getAgenceRattacher(): ?Agence
    {
        return $this->agenceRattacher;
    }

    public function setAgenceRattacher(?Agence $agenceRattacher): self
    {
        $this->agenceRattacher = $agenceRattacher;

        return $this;
    }

    public function getStatutDemande(): ?StatutDemande
    {
        return $this->statutDemande;
    }

    public function setStatutDemande(?StatutDemande $statutDemande): self
    {
        $this->statutDemande = $statutDemande;

        return $this;
    }
}
