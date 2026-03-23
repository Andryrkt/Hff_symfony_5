<?php

namespace App\Entity\Hf\Atelier\Dit;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\Hf\Atelier\Dit\CategorieAteAppRepository;

/**
 * @ORM\Entity(repositoryClass=CategorieAteAppRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class CategorieAteApp
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $libelleCategorieAteApp;

    /**
     * @ORM\OneToMany(targetEntity=Dit::class, mappedBy="categorieAteApp")
     */
    private $dits;

    public function __construct()
    {
        $this->dits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelleCategorieAteApp(): ?string
    {
        return $this->libelleCategorieAteApp;
    }

    public function setLibelleCategorieAteApp(string $libelleCategorieAteApp): self
    {
        $this->libelleCategorieAteApp = $libelleCategorieAteApp;

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
            $dit->setCategorieAteApp($this);
        }

        return $this;
    }

    public function removeDit(Dit $dit): self
    {
        if ($this->dits->removeElement($dit)) {
            // set the owning side to null (unless already changed)
            if ($dit->getCategorieAteApp() === $this) {
                $dit->setCategorieAteApp(null);
            }
        }

        return $this;
    }
}
