<?php

namespace App\Dto\Hf\Materiel\Casier;

use Symfony\Component\Validator\Constraints as Assert;

class FirstFormDto
{
    public string $agenceUser;

    public string $serviceUser;

    /**
     * @Assert\Length(
     *      max=5,
     *      maxMessage="Le id du matériel ne peut pas dépasser {{ limit }} caractères"
     * )
     */
    public ?int $idMateriel = null;

    public ?string $numParc = null;

    public ?string $numSerie = null;
}
