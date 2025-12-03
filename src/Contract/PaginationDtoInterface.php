<?php

declare(strict_types=1);

namespace App\Contract;

interface PaginationDtoInterface
{
    public function setLimit(int $limit): self;
    public function setSortBy(string $sortBy): self;
    public function setSortOrder(string $sortOrder): self;
}
