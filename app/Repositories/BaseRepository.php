<?php

namespace App\Repositories;

use App\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;
    protected int $perPage = 10;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(array $filters = []): Collection
    {
        $query = $this->model->query();

        $this->applyFilters($query, $filters);

        return $query->get();
    }

    public function find(int $id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $record = $this->find($id);
        if (!$record) {
            return null;
        }

        $record->update($data);
        return $record->fresh();
    }

    public function delete(int $id): bool
    {
        $record = $this->find($id);
        if (!$record) {
            return false;
        }

        return $record->delete();
    }

    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->query();

        $this->applyFilters($query, $filters);

        $this->perPage = $perPage;

        return $query->orderBy('id', 'desc')->paginate($this->perPage);
    }

    public function search(string $query, array $filters = []): Collection
    {
        $searchQuery = $this->model->query();

        $this->applyFilters($searchQuery, $filters);

        return $searchQuery->get();
    }

    /**
     * Apply filters to the query
     * Override this method in child classes for custom filtering logic
     */
    protected function applyFilters($query, array $filters): void
    {
        foreach ($filters as $field => $value) {
            if ($value !== null && $value !== '') {
                // Check if field is fillable
                if (in_array($field, $this->model->getFillable())) {
                    $query->where($field, 'LIKE', "%{$value}%");
                } elseif (method_exists($this, 'getCustomFilter')) {
                    // Allow for custom filtering logic
                    $this->getCustomFilter($query, $field, $value);
                }
            }
        }
    }

    /**
     * Get the model instance
     */
    protected function getModel(): Model
    {
        return $this->model;
    }
}
