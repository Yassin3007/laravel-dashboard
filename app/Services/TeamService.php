<?php

namespace App\Services;

use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TeamService
{
    protected Team $model;

    public function __construct(Team $model)
    {
        $this->model = $model;
    }

    /**
     * Get all teams with pagination
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 15 , $with = []): LengthAwarePaginator
    {
        return $this->model->with($with)->latest()->paginate($perPage);
    }

    /**
     * Get all teams without pagination
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return $this->model->all();
    }

    /**
     * Find team by ID
     *
     * @param int $id
     * @return Team|null
     */
    public function findById(int $id): ?Team
    {
        return $this->model->find($id);
    }

    /**
     * Find team by ID or fail
     *
     * @param int $id
     * @return Team
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findByIdOrFail(int $id): Team
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Create a new team
     *
     * @param array $data
     * @return Team
     * @throws \Exception
     */
    public function create(array $data): Team
    {
        try {
            DB::beginTransaction();

            $team = $this->model->create($data);

            DB::commit();

            Log::info('Team created successfully', ['id' => $team->id]);

            return $team;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating Team', ['error' => $e->getMessage(), 'data' => $data]);
            throw $e;
        }
    }

    /**
     * Update an existing team
     *
     * @param Team $team
     * @param array $data
     * @return Team
     * @throws \Exception
     */
    public function update(Team $team, array $data): Team
    {
        try {
            DB::beginTransaction();

            $team->update($data);
            $team->refresh();

            DB::commit();

            Log::info('Team updated successfully', ['id' => $team->id]);

            return $team;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating Team', [
                'id' => $team->id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Delete a team
     *
     * @param Team $team
     * @return bool
     * @throws \Exception
     */
    public function delete(Team $team): bool
    {
        try {
            DB::beginTransaction();

            $deleted = $team->delete();

            DB::commit();

            Log::info('Team deleted successfully', ['id' => $team->id]);

            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting Team', [
                'id' => $team->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Search teams based on criteria
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
     * Bulk delete teams
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

            Log::info('Bulk delete teams completed', [
                'ids' => $ids,
                'deleted_count' => $deleted
            ]);

            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in bulk delete teams', [
                'ids' => $ids,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get teams by specific field
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
     * Count total teams
     *
     * @return int
     */
    public function count(): int
    {
        return $this->model->count();
    }

    /**
     * Check if team exists
     *
     * @param int $id
     * @return bool
     */
    public function exists(int $id): bool
    {
        return $this->model->where('id', $id)->exists();
    }

    /**
     * Get latest teams
     *
     * @param int $limit
     * @return Collection
     */
    public function getLatest(int $limit = 10): Collection
    {
        return $this->model->latest()->limit($limit)->get();
    }

    /**
     * Duplicate a team
     *
     * @param Team $team
     * @return Team
     * @throws \Exception
     */
    public function duplicate(Team $team): Team
    {
        try {
            DB::beginTransaction();

            $data = $team->toArray();
            unset($data['id'], $data['created_at'], $data['updated_at']);

            $newTeam = $this->model->create($data);

            DB::commit();

            Log::info('Team duplicated successfully', [
                'original_id' => $team->id,
                'new_id' => $newTeam->id
            ]);

            return $newTeam;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error duplicating Team', [
                'id' => $team->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
