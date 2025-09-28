<?php

namespace App\Actions\Posts;

use App\Models\Post;
use App\Models\User;

class CreatePostAction
{
    public function execute(User $user, array $data): Post
    {
        return Post::create([
            'title' => $data['title'],
            'text' => $data['text'],
            'user_id' => $user->id,
        ]);
    }
}