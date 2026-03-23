<?php

namespace App\Entity\Admin\Statut;

use App\Entity\Hf\Materiel\Badm\Badm;
use App\Entity\Hf\Atelier\Dit\Dit;
use App\Entity\Hf\Materiel\Casier\Casier;
use App\Entity\Hf\Rh\Dom\Dom;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\Admin\Statut\StatutDemandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StatutDemandeRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class StatutDemande
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $codeApplication;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $codeStatut;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=Dom::class, mappedBy="idStatutDemande")
     */
    private $doms;

    /**
     * @ORM\OneToMany(targetEntity=Casier::class, mappedBy="statutDemande")
     */
    private $casiers;

    /**
     * @ORM\OneToMany(targetEntity=Dit::class, mappedBy="statutDemande")
     */
    private $dits;

    /**
     * @ORM\OneToMany(targetEntity=Badm::class, mappedBy="statutDemande")
     */
    private $badms;


    public function __construct()
    {
        $this->doms = new ArrayCollection();
        $this->casiers = new ArrayCollection();
        $this->dits = new ArrayCollection();
        $this->badms = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeApplication(): ?string
    {
        return $this->codeApplication;
    }

    public function setCodeApplication(string $codeApplication): self
    {
        $this->codeApplication = $codeApplication;

        return $this;
    }

    public function getCodeStatut(): ?string
    {
        return $this->codeStatut;
    }

    public function setCodeStatut(string $codeStatut): self
    {
        $this->codeStatut = $codeStatut;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Dom>
     */
    public function getDoms(): Collection
    {
        return $this->doms;
    }

    public function addDom(Dom $dom): self
    {
        if (!$this->doms->contains($dom)) {
            $this->doms[] = $dom;
            $dom->setIdStatutDemande($this);
        }

        return $this;
    }

    public function removeDom(Dom $dom): self
    {
        if ($this->doms->removeElement($dom)) {
            // set the owning side to null (unless already changed)
            if ($dom->getIdStatutDemande() === $this) {
                $dom->setIdStatutDemande(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Casier>
     */
    public function getCasiers(): Collection
    {
        return $this->casiers;
    }

    public function addCasier(Casier $casier): self
    {
        if (!$this->casiers->contains($casier)) {
            $this->casiers[] = $casier;
            $casier->setStatutDemande($this);
        }

        return $this;
    }

    public function removeCasier(Casier $casier): self
    {
        if ($this->casiers->removeElement($casier)) {
            // set the owning side to null (unless already changed)
            if ($casier->getStatutDemande() === $this) {
                $casier->setStatutDemande(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Dit>
     */
    public function getDits(): Collection
    {
        return $this->dits;
    }

    public function addDit(Dit $dit): self
    {
        if (!$this->dits->contains($dit)) {
            $this->dits[] = $dit;
            $dit->setStatutDemande($this);
        }

        return $this;
    }

    public function removeDit(Dit $dit): self
    {
        if ($this->dits->removeElement($dit)) {
            // set the owning side to null (unless already changed)
            if ($dit->getStatutDemande() === $this) {
                $dit->setStatutDemande(null);
            }
        }

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
            $badm->setStatutDemande($this);
        }

        return $this;
    }

    public function removeBadm(Badm $badm): self
    {
        if ($this->badms->removeElement($badm)) {
            // set the owning side to null (unless already changed)
            if ($badm->getStatutDemande() === $this) {
                $badm->setStatutDemande(null);
            }
        }

        return $this;
    }
}
