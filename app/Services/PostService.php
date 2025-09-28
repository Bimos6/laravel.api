<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class PostService
{
    public function createPost(User $user, array $data): Post
    {
        return Post::create([
            'title' => $data['title'],
            'text' => $data['text'],
            'user_id' => $user->id,
        ]);
    }

    public function getAllPosts(array $filters = []): LengthAwarePaginator
    {
        $query = Post::with('user');

        return $this->applyFilters($query, $filters)->paginate(
            $filters['limit'] ?? 15
        );
    }

    public function getUserPosts(User $user, array $filters = []): LengthAwarePaginator
    {
        $query = Post::where('user_id', $user->id);

        return $this->applyFilters($query, $filters)->paginate(
            $filters['limit'] ?? 15
        );
    }

    private function applyFilters($query, array $filters)
    {
        // Фильтрация по дате (от)
        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        // Фильтрация по дате (до)
        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        // Сортировка
        $sortField = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        if (!in_array($sortField, ['created_at', 'title'])) {
            throw ValidationException::withMessages([
                'sort_by' => ['Invalid sort field. Allowed: created_at, title'],
            ]);
        }

        if (!in_array($sortOrder, ['asc', 'desc'])) {
            throw ValidationException::withMessages([
                'sort_order' => ['Invalid sort order. Allowed: asc, desc'],
            ]);
        }

        return $query->orderBy($sortField, $sortOrder);
    }
}