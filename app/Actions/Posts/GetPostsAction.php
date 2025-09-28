<?php

namespace App\Actions\Posts;

use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;

class GetPostsAction
{
    public function execute(array $filters = []): LengthAwarePaginator
    {
        $query = Post::with('user');

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        $sortField = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        if (in_array($sortField, ['created_at', 'title'])) {
            $query->orderBy($sortField, $sortOrder);
        }

        $limit = $filters['limit'] ?? 15;
        
        return $query->paginate($limit);
    }

    private function applyFilters($query, array $filters)
    {
        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        $sortField = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        return $query->orderBy($sortField, $sortOrder);
    }
}