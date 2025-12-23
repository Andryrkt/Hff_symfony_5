<?php

namespace App\Entity\Hf\Atelier\Dit;

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
}
