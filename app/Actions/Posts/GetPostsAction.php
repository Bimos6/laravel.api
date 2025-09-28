<?php

namespace App\Actions\Posts;

use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;

class GetPostsAction
{
    public function execute(array $filters = []): LengthAwarePaginator
    {
        $query = Post::with('user');

        return $this->applyFilters($query, $filters)->paginate(
            $filters['limit'] ?? 15
        );
    }

    private function applyFilters($query, array $filters)
    {
        // Фильтрация по дате
        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        // Сортировка
        $sortField = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        return $query->orderBy($sortField, $sortOrder);
    }
}