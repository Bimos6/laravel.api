<?php

namespace App\Http\Controllers;

use App\Actions\Posts\CreatePostAction;
use App\Actions\Posts\GetPostsAction;
use App\Http\Requests\PostRequest;
use App\Http\Requests\PostListRequest;
use App\Http\Resources\PostResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PostController extends Controller
{
    public function store(
        PostRequest $request,
        CreatePostAction $createPostAction
    ): JsonResponse {
        $post = $createPostAction->execute($request->user(), $request->validated());

        return response()->json(new PostResource($post), 201);
    }

    public function index(
        PostListRequest $request,
        GetPostsAction $getPostsAction
    ): AnonymousResourceCollection {
        $posts = $getPostsAction->execute($request->validated());

        return PostResource::collection($posts);
    }

    public function myPosts(
        PostListRequest $request,
        GetPostsAction $getPostsAction
    ): AnonymousResourceCollection {
        $filters = $request->validated();
        $filters['user_id'] = $request->user()->id;
        
        $posts = $getPostsAction->execute($filters);

        return PostResource::collection($posts);
    }
}