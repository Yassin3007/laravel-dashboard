<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PostService
{
    protected Post $model;

    public function __construct(Post $model)
    {
        $this->model = $model;
    }

    /**
     * Get all posts with pagination
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->latest()->paginate($perPage);
    }

    /**
     * Get all posts without pagination
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return $this->model->all();
    }

    /**
     * Find post by ID
     *
     * @param int $id
     * @return Post|null
     */
    public function findById(int $id): ?Post
    {
        return $this->model->find($id);
    }

    /**
     * Find post by ID or fail
     *
     * @param int $id
     * @return Post
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findByIdOrFail(int $id): Post
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Create a new post
     *
     * @param array $data
     * @return Post
     * @throws \Exception
     */
    public function create(array $data): Post
    {
        try {
            DB::beginTransaction();

            $post = $this->model->create($data);

            DB::commit();

            Log::info('Post created successfully', ['id' => $post->id]);

            return $post;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating Post', ['error' => $e->getMessage(), 'data' => $data]);
            throw $e;
        }
    }

    /**
     * Update an existing post
     *
     * @param Post $post
     * @param array $data
     * @return Post
     * @throws \Exception
     */
    public function update(Post $post, array $data): Post
    {
        try {
            DB::beginTransaction();

            $post->update($data);
            $post->refresh();

            DB::commit();

            Log::info('Post updated successfully', ['id' => $post->id]);

            return $post;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating Post', [
                'id' => $post->id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Delete a post
     *
     * @param Post $post
     * @return bool
     * @throws \Exception
     */
    public function delete(Post $post): bool
    {
        try {
            DB::beginTransaction();

            $deleted = $post->delete();

            DB::commit();

            Log::info('Post deleted successfully', ['id' => $post->id]);

            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting Post', [
                'id' => $post->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Search posts based on criteria
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
     * Bulk delete posts
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

            Log::info('Bulk delete posts completed', [
                'ids' => $ids,
                'deleted_count' => $deleted
            ]);

            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in bulk delete posts', [
                'ids' => $ids,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get posts by specific field
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
     * Count total posts
     *
     * @return int
     */
    public function count(): int
    {
        return $this->model->count();
    }

    /**
     * Check if post exists
     *
     * @param int $id
     * @return bool
     */
    public function exists(int $id): bool
    {
        return $this->model->where('id', $id)->exists();
    }

    /**
     * Get latest posts
     *
     * @param int $limit
     * @return Collection
     */
    public function getLatest(int $limit = 10): Collection
    {
        return $this->model->latest()->limit($limit)->get();
    }

    /**
     * Duplicate a post
     *
     * @param Post $post
     * @return Post
     * @throws \Exception
     */
    public function duplicate(Post $post): Post
    {
        try {
            DB::beginTransaction();

            $data = $post->toArray();
            unset($data['id'], $data['created_at'], $data['updated_at']);

            $newPost = $this->model->create($data);

            DB::commit();

            Log::info('Post duplicated successfully', [
                'original_id' => $post->id,
                'new_id' => $newPost->id
            ]);

            return $newPost;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error duplicating Post', [
                'id' => $post->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
