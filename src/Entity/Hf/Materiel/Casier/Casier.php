<?php

namespace App\Entity\Hf\Materiel\Casier;

use App\Entity\Admin\Statut\StatutDemande;
use App\Entity\Hf\Materiel\Badm\Badm;
use App\Entity\Traits\CreatedByTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Contract\Entity\CreatedByInterface;
use App\Entity\Admin\AgenceService\Agence;
use App\Repository\Hf\Materiel\Casier\CasierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @ORM\Column(type="boolean")
     */
    private $isValide = false;

    /**
     * @ORM\OneToMany(targetEntity=Badm::class, mappedBy="casierEmetteur")
     */
    private $badms;

    public function __construct()
    {
        $this->badms = new ArrayCollection();
    }

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

    public function isValid(): ?bool
    {
        return $this->isValide;
    }

    public function setIsValide(bool $isValide): self
    {
        $this->isValide = $isValide;

        return $this;
    }

    /**
     * @return Collection<int, Badm>
     */
    public function getBadms(): Collection
    {
        return $this->badms;
    }

    public function addBadm(Badm $badm): self
    {
        if (!$this->badms->contains($badm)) {
            $this->badms[] = $badm;
            $badm->setCasierEmetteur($this);
        }

        return $this;
    }

    public function removeBadm(Badm $badm): self
    {
        if ($this->badms->removeElement($badm)) {
            // set the owning side to null (unless already changed)
            if ($badm->getCasierEmetteur() === $this) {
                $badm->setCasierEmetteur(null);
            }
        }

        return $this;
    }
}
