<?php

namespace App\Dto\Hf\Atelier\Dit;

use App\Contract\Dto\PaginationDtoTrait;
use App\Contract\Dto\SearchDtoInterface;
use App\Contract\PaginationDtoInterface;


class SearchDto implements PaginationDtoInterface, SearchDtoInterface
{
    use PaginationDtoTrait;

    public function __construct()
    {
        $this->sortBy = 'numeroDit';
    }
}
