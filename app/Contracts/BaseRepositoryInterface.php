<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseRepositoryInterface
{
    /**
     * Get all records with optional filters
     */
    public function all(array $filters = []): Collection;

    /**
     * Find a record by ID
     */
    public function find(int $id);

    /**
     * Create a new record
     */
    public function create(array $data);

    /**
     * Update an existing record
     */
    public function update(int $id, array $data);

    /**
     * Delete a record
     */
    public function delete(int $id): bool;

    /**
     * Get paginated records with filters
     */
    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator;

    /**
     * Search records
     */
    public function search(string $query, array $filters = []): Collection;
}
