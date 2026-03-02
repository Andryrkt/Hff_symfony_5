<?php

namespace App\Factory\Hf\Atelier\Dit\Soumission\Ors;

use App\Dto\Hf\Atelier\Dit\Soumission\Ors\OrsDto;

class OrsFactory
{
    public function create(string $numeroDit): OrsDto
    {
        $dto = new OrsDto();

        $dto->numeroDit = $numeroDit;
        $dto->numeroOr = '';


        return $dto;
    }
}
