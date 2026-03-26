<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseServiceInterface
{
    /**
     * Get all records with optional filters
     */
    public function getAll(array $filters = []): Collection;

    /**
     * Get paginated records with filters
     */
    public function getPaginated(int $perPage = 10, array $filters = []): LengthAwarePaginator;

    /**
     * Find a record by ID
     */
    public function findById(int $id);

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
     * Search records
     */
    public function search(string $query, array $filters = []): Collection;
}
