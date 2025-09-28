<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostListRequest;
use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PostController extends Controller
{
    public function __construct(private PostService $postService)
    {
    }

    public function store(PostRequest $request): JsonResponse
    {
        $post = $this->postService->createPost($request->user(), $request->validated());

        return response()->json(new PostResource($post), 201);
    }

    public function index(PostListRequest $request): AnonymousResourceCollection
    {
        $posts = $this->postService->getAllPosts($request->validated());

        return PostResource::collection($posts);
    }

    public function myPosts(PostListRequest $request): AnonymousResourceCollection
    {
        $posts = $this->postService->getUserPosts($request->user(), $request->validated());

        return PostResource::collection($posts);
    }
}