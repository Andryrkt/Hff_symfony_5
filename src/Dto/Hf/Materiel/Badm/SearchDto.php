<?php

namespace App\Dto\Hf\Materiel\Badm;

use App\Contract\PaginationDtoInterface;

class SearchDto implements PaginationDtoInterface
{
    // Pagination et tri
    public int $limit = 50;
    public string $sortBy = 'numeroBadm';
    public string $sortOrder = 'DESC';

    public function setLimit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function setSortBy(string $sortBy): self
    {
        $this->sortBy = $sortBy;
        return $this;
    }

    public function setSortOrder(string $sortOrder): self
    {
        $this->sortOrder = $sortOrder;
        return $this;
    }
}
