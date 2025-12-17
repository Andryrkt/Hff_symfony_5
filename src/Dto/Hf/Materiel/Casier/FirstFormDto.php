<?php

namespace App\Dto\Hf\Materiel\Casier;


class FirstFormDto
{
    public string $agenceUser;

    public string $serviceUser;

    public ?int $idMateriel = null;

    public ?string $numParc = null;

    public ?string $numSerie = null;
}
