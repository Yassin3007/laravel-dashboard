<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryService
{
    protected Category $model;

    public function __construct(Category $model)
    {
        $this->model = $model;
    }

    /**
     * Get all categories with pagination
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 15 , $with = []): LengthAwarePaginator
    {
        return $this->model->with($with)->latest()->paginate($perPage);
    }

    /**
     * Get all categories without pagination
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return $this->model->all();
    }

    /**
     * Find category by ID
     *
     * @param int $id
     * @return Category|null
     */
    public function findById(int $id): ?Category
    {
        return $this->model->find($id);
    }

    /**
     * Find category by ID or fail
     *
     * @param int $id
     * @return Category
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findByIdOrFail(int $id): Category
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Create a new category
     *
     * @param array $data
     * @return Category
     * @throws \Exception
     */
    public function create(array $data): Category
    {
        try {
            DB::beginTransaction();

            $category = $this->model->create($data);

            DB::commit();

            Log::info('Category created successfully', ['id' => $category->id]);

            return $category;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating Category', ['error' => $e->getMessage(), 'data' => $data]);
            throw $e;
        }
    }

    /**
     * Update an existing category
     *
     * @param Category $category
     * @param array $data
     * @return Category
     * @throws \Exception
     */
    public function update(Category $category, array $data): Category
    {
        try {
            DB::beginTransaction();

            $category->update($data);
            $category->refresh();

            DB::commit();

            Log::info('Category updated successfully', ['id' => $category->id]);

            return $category;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating Category', [
                'id' => $category->id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Delete a category
     *
     * @param Category $category
     * @return bool
     * @throws \Exception
     */
    public function delete(Category $category): bool
    {
        try {
            DB::beginTransaction();

            $deleted = $category->delete();

            DB::commit();

            Log::info('Category deleted successfully', ['id' => $category->id]);

            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting Category', [
                'id' => $category->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Search categories based on criteria
     *
     * @param array $criteria
     * @return LengthAwarePaginator
     */
    public function search(array $criteria): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        // Add search logic based on your model's searchable fields
        // Example implementation:
        if (isset($criteria['search']) && !empty($criteria['search'])) {
            $searchTerm = $criteria['search'];
            $query->where(function ($q) use ($searchTerm) {
                // Add searchable columns here
                // $q->where('name', 'LIKE', "%{$searchTerm}%")
                //   ->orWhere('email', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Add date range filtering
        if (isset($criteria['start_date']) && !empty($criteria['start_date'])) {
            $query->whereDate('created_at', '>=', $criteria['start_date']);
        }

        if (isset($criteria['end_date']) && !empty($criteria['end_date'])) {
            $query->whereDate('created_at', '<=', $criteria['end_date']);
        }

        // Add sorting
        $sortBy = $criteria['sort_by'] ?? 'created_at';
        $sortOrder = $criteria['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $criteria['per_page'] ?? 15;
        return $query->paginate($perPage);
    }

    /**
     * Bulk delete categories
     *
     * @param array $ids
     * @return int
     * @throws \Exception
     */
    public function bulkDelete(array $ids): int
    {
        try {
            DB::beginTransaction();

            $deleted = $this->model->whereIn('id', $ids)->delete();

            DB::commit();

            Log::info('Bulk delete categories completed', [
                'ids' => $ids,
                'deleted_count' => $deleted
            ]);

            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in bulk delete categories', [
                'ids' => $ids,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get categories by specific field
     *
     * @param string $field
     * @param mixed $value
     * @return Collection
     */
    public function getByField(string $field, $value): Collection
    {
        return $this->model->where($field, $value)->get();
    }

    /**
     * Count total categories
     *
     * @return int
     */
    public function count(): int
    {
        return $this->model->count();
    }

    /**
     * Check if category exists
     *
     * @param int $id
     * @return bool
     */
    public function exists(int $id): bool
    {
        return $this->model->where('id', $id)->exists();
    }

    /**
     * Get latest categories
     *
     * @param int $limit
     * @return Collection
     */
    public function getLatest(int $limit = 10): Collection
    {
        return $this->model->latest()->limit($limit)->get();
    }

    /**
     * Duplicate a category
     *
     * @param Category $category
     * @return Category
     * @throws \Exception
     */
    public function duplicate(Category $category): Category
    {
        try {
            DB::beginTransaction();

            $data = $category->toArray();
            unset($data['id'], $data['created_at'], $data['updated_at']);

            $newCategory = $this->model->create($data);

            DB::commit();

            Log::info('Category duplicated successfully', [
                'original_id' => $category->id,
                'new_id' => $newCategory->id
            ]);

            return $newCategory;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error duplicating Category', [
                'id' => $category->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
