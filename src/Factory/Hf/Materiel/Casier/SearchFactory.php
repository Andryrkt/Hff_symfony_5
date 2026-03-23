<?php

namespace App\Factory\Hf\Materiel\Casier;

use App\Dto\Hf\Materiel\Casier\SearchDto;
use App\Repository\Admin\Statut\StatutDemandeRepository;

class SearchFactory
{
    private StatutDemandeRepository $statutDemandeRepository;

    public function __construct(StatutDemandeRepository $statutDemandeRepository)
    {
        $this->statutDemandeRepository = $statutDemandeRepository;
    }

    public function create(): SearchDto
    {
        $searchDto = new SearchDto();
        $searchDto->statut = $this->statutDemandeRepository->findOneBy(['codeApplication' => 'CAS', 'description' => 'ATTENTE VALIDATION']);
        return $searchDto;
    }
}
