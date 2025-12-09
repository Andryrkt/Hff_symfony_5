<?php

declare(strict_types=1);

namespace App\Controller\Traits;

use App\Contract\PaginationDtoInterface;
use Symfony\Component\HttpFoundation\Request;

trait PaginationAndSortingTrait
{
    /**
     * Handles pagination and sorting parameters from the request.
     * Updates the DTO with limit, sortBy, and sortOrder if present.
     * Returns the current page number.
     *
     * @param Request $request
     * @param PaginationDtoInterface $dto The DTO object to update
     * @return int The current page number
     */
    protected function handlePaginationAndSorting(Request $request, PaginationDtoInterface $dto): int
    {
        // Limit
        $limitFromUrl = $request->query->getInt('limit', 0);
        if ($limitFromUrl > 0) {
            $dto->setLimit($limitFromUrl);
        }

        // Sort By
        $sortByFromUrl = $request->query->get('sortBy');
        if ($sortByFromUrl) {
            $dto->setSortBy($sortByFromUrl);
        }

        // Sort Order
        $sortOrderFromUrl = $request->query->get('sortOrder');
        if ($sortOrderFromUrl) {
            $dto->setSortOrder($sortOrderFromUrl);
        }

        // Page
        return $request->query->getInt('page', 1);
    }
}
