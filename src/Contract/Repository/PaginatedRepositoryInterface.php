<?php

declare(strict_types=1);

namespace App\Contract\Repository;

use App\Contract\Dto\SearchDtoInterface;

interface PaginatedRepositoryInterface
{
    /**
     * @param int $page
     * @param int $limit
     * @param SearchDtoInterface $searchDto
     * @return array{data: array, totalItems: int, currentPage: int, lastPage: int}
     */
    public function findPaginatedAndFiltered(int $page, int $limit, SearchDtoInterface $searchDto): array;
}
