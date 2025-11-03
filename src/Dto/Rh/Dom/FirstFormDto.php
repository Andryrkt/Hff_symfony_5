<?php

namespace App\Dto\Rh\Dom;


use Symfony\Component\Validator\Constraints as Assert;

class FirstFormDto
{
    #[Assert\NotBlank]
    public $agenceUser;
    #[Assert\NotBlank]
    public $serviceUser;
    #[Assert\NotBlank]
    public $typeMission;
    #[Assert\Choice(['PERMANENT', 'TEMPORAIRE'])]
    public $salarier = 'PERMANENT';
    public $categorie;
    public $matricule;
    public ?string $cin = null;
    public ?string $nom = null;
    public ?string $prenom = null;
}
