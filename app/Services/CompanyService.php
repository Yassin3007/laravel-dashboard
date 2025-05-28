<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompanyService
{
    protected Company $model;

    public function __construct(Company $model)
    {
        $this->model = $model;
    }

    /**
     * Get all companies with pagination
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->latest()->paginate($perPage);
    }

    /**
     * Get all companies without pagination
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return $this->model->all();
    }

    /**
     * Find company by ID
     *
     * @param int $id
     * @return Company|null
     */
    public function findById(int $id): ?Company
    {
        return $this->model->find($id);
    }

    /**
     * Find company by ID or fail
     *
     * @param int $id
     * @return Company
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findByIdOrFail(int $id): Company
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Create a new company
     *
     * @param array $data
     * @return Company
     * @throws \Exception
     */
    public function create(array $data): Company
    {
        try {
            DB::beginTransaction();

            $company = $this->model->create($data);

            DB::commit();

            Log::info('Company created successfully', ['id' => $company->id]);

            return $company;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating Company', ['error' => $e->getMessage(), 'data' => $data]);
            throw $e;
        }
    }

    /**
     * Update an existing company
     *
     * @param Company $company
     * @param array $data
     * @return Company
     * @throws \Exception
     */
    public function update(Company $company, array $data): Company
    {
        try {
            DB::beginTransaction();

            $company->update($data);
            $company->refresh();

            DB::commit();

            Log::info('Company updated successfully', ['id' => $company->id]);

            return $company;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating Company', [
                'id' => $company->id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Delete a company
     *
     * @param Company $company
     * @return bool
     * @throws \Exception
     */
    public function delete(Company $company): bool
    {
        try {
            DB::beginTransaction();

            $deleted = $company->delete();

            DB::commit();

            Log::info('Company deleted successfully', ['id' => $company->id]);

            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting Company', [
                'id' => $company->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Search companies based on criteria
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
     * Bulk delete companies
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

            Log::info('Bulk delete companies completed', [
                'ids' => $ids,
                'deleted_count' => $deleted
            ]);

            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in bulk delete companies', [
                'ids' => $ids,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get companies by specific field
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
     * Count total companies
     *
     * @return int
     */
    public function count(): int
    {
        return $this->model->count();
    }

    /**
     * Check if company exists
     *
     * @param int $id
     * @return bool
     */
    public function exists(int $id): bool
    {
        return $this->model->where('id', $id)->exists();
    }

    /**
     * Get latest companies
     *
     * @param int $limit
     * @return Collection
     */
    public function getLatest(int $limit = 10): Collection
    {
        return $this->model->latest()->limit($limit)->get();
    }

    /**
     * Duplicate a company
     *
     * @param Company $company
     * @return Company
     * @throws \Exception
     */
    public function duplicate(Company $company): Company
    {
        try {
            DB::beginTransaction();

            $data = $company->toArray();
            unset($data['id'], $data['created_at'], $data['updated_at']);

            $newCompany = $this->model->create($data);

            DB::commit();

            Log::info('Company duplicated successfully', [
                'original_id' => $company->id,
                'new_id' => $newCompany->id
            ]);

            return $newCompany;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error duplicating Company', [
                'id' => $company->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
