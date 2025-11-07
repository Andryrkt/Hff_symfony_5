<?php

namespace App\Dto\Rh\Dom;

use App\Entity\Rh\Dom\SousTypeDocument;
use Symfony\Component\Validator\Constraints as Assert;
use ArrayAccess;

class FirstFormDto implements ArrayAccess
{
    #[Assert\NotBlank]
    public $agenceUser;
    #[Assert\NotBlank]
    public $serviceUser;
    #[Assert\NotBlank]
    public ?SousTypeDocument $typeMission = null;
    #[Assert\Choice(['PERMANENT', 'TEMPORAIRE'])]
    public string $salarier = 'PERMANENT';
    public $categorie;
    public $matricule;
    public $matriculeNom;
    public ?string $cin = null;
    public ?string $nom = null;
    public ?string $prenom = null;

    // ArrayAccess methods
    public function offsetExists($offset): bool
    {
        return property_exists($this, $offset);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->$offset;
    }

    public function offsetSet($offset, $value): void
    {
        $this->$offset = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->$offset);
    }
}
