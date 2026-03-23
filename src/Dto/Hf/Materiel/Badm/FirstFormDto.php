<?php

namespace App\Dto\Hf\Materiel\Badm;

use App\Entity\Hf\Materiel\Badm\TypeMouvement;
use Symfony\Component\Validator\Constraints as Assert;

class FirstFormDto
{
    public string $agenceUser = '';
    public string $serviceUser = '';

    /**
     * @Assert\Length(
     *      max=5,
     *      maxMessage="Le id du matériel ne peut pas dépasser {{ limit }} caractères"
     * )
     */
    public ?int $idMateriel = null;
    public ?string $numParc = null;
    public ?string $numSerie = null;
    public ?TypeMouvement $typeMouvement = null;
}
