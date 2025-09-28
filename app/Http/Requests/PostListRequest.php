<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostListRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'limit' => 'nullable|integer|min:1|max:100',
            'offset' => 'nullable|integer|min:0',
            'sort_by' => 'nullable|in:created_at,title',
            'sort_order' => 'nullable|in:asc,desc',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ];
    }
}