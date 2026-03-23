<?php

declare(strict_types=1);

namespace App\Contract\Dto;

trait PaginationDtoTrait
{
    public int $limit = 50;
    public string $sortBy = 'id';
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
