<?php

namespace App\Tests\Unit\Repository\Traits;

use App\Repository\Traits\PaginatableRepositoryTrait;
use PHPUnit\Framework\TestCase;

class DummyRepository
{
    use PaginatableRepositoryTrait;

    public function callSortAndLimit($searchDto, array $sortableColumns, string $defaultValue): array
    {
        return $this->sortAndLimit($searchDto, $sortableColumns, $defaultValue);
    }
}

class PaginatableRepositoryTraitTest extends TestCase
{
    private $repository;

    protected function setUp(): void
    {
        $this->repository = new DummyRepository();
    }

    public function testSortAndLimitWithValidData()
    {
        $searchDto = (object) [
            'sortBy' => 'name',
            'sortOrder' => 'ASC',
            'limit' => 20
        ];
        $sortableColumns = ['name' => 'd.name', 'date' => 'd.date'];

        [$limit, $sortBy, $sortOrder] = $this->repository->callSortAndLimit($searchDto, $sortableColumns, 'date');

        $this->assertEquals(20, $limit);
        $this->assertEquals('name', $sortBy);
        $this->assertEquals('ASC', $sortOrder);
    }

    public function testSortAndLimitWithInvalidSortBy()
    {
        $searchDto = (object) [
            'sortBy' => 'invalid_column',
            'sortOrder' => 'DESC',
            'limit' => 50
        ];
        $sortableColumns = ['name' => 'd.name'];

        [$limit, $sortBy, $sortOrder] = $this->repository->callSortAndLimit($searchDto, $sortableColumns, 'name');

        $this->assertEquals(50, $limit);
        $this->assertEquals('name', $sortBy);
        $this->assertEquals('DESC', $sortOrder);
    }

    public function testSortAndLimitWithInvalidSortOrder()
    {
        $searchDto = (object) [
            'sortBy' => 'name',
            'sortOrder' => 'INVALID',
            'limit' => 50
        ];
        $sortableColumns = ['name' => 'd.name'];

        [$limit, $sortBy, $sortOrder] = $this->repository->callSortAndLimit($searchDto, $sortableColumns, 'name');

        $this->assertEquals('DESC', $sortOrder);
    }

    public function testSortAndLimitWithMissingData()
    {
        $searchDto = (object) [];
        $sortableColumns = ['name' => 'd.name'];

        [$limit, $sortBy, $sortOrder] = $this->repository->callSortAndLimit($searchDto, $sortableColumns, 'name');

        $this->assertEquals(50, $limit);
        $this->assertEquals('name', $sortBy);
        $this->assertEquals('DESC', $sortOrder);
    }
}
