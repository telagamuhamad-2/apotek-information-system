<?php

namespace App\Services;

use App\Contracts\BaseRepositoryInterface;
use App\Contracts\BaseServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Exception;

abstract class BaseService implements BaseServiceInterface
{
    protected BaseRepositoryInterface $repository;

    public function __construct(BaseRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all records with optional filters
     */
    public function getAll(array $filters = []): Collection
    {
        try {
            return $this->repository->all($filters);
        } catch (Exception $e) {
            throw new Exception("Failed to retrieve records: " . $e->getMessage());
        }
    }

    /**
     * Get paginated records with filters
     */
    public function getPaginated(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        try {
            return $this->repository->paginate($perPage, $filters);
        } catch (Exception $e) {
            throw new Exception("Failed to retrieve paginated records: " . $e->getMessage());
        }
    }

    /**
     * Find a record by ID
     */
    public function findById(int $id)
    {
        try {
            $record = $this->repository->find($id);
            if (!$record) {
                throw new Exception("Record not found.");
            }
            return $record;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Create a new record
     */
    public function create(array $data)
    {
        try {
            DB::beginTransaction();
            $record = $this->repository->create($data);
            DB::commit();
            return $record;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Failed to create record: " . $e->getMessage());
        }
    }

    /**
     * Update an existing record
     */
    public function update(int $id, array $data)
    {
        try {
            DB::beginTransaction();
            $record = $this->repository->update($id, $data);
            if (!$record) {
                throw new Exception("Record not found.");
            }
            DB::commit();
            return $record;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Failed to update record: " . $e->getMessage());
        }
    }

    /**
     * Delete a record
     */
    public function delete(int $id): bool
    {
        try {
            DB::beginTransaction();
            $result = $this->repository->delete($id);
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Failed to delete record: " . $e->getMessage());
        }
    }

    /**
     * Search records
     */
    public function search(string $query, array $filters = []): Collection
    {
        try {
            return $this->repository->search($query, $filters);
        } catch (Exception $e) {
            throw new Exception("Failed to search records: " . $e->getMessage());
        }
    }
}
