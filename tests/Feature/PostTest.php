<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Tests\TestCase;

class PostTest extends TestCase
{
    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test')->plainTextToken;
    }

    public function test_authenticated_user_can_create_post()
    {
        $postData = [
            'title' => 'Test Post ' . uniqid(),
            'text' => 'Test Content'
        ];

        $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/posts', $postData)
            ->assertStatus(201)
            ->assertJson(['title' => $postData['title']]);

        $this->assertDatabaseHas('posts', ['title' => $postData['title']]);
    }

    public function test_unauthenticated_user_cannot_create_post()
    {
        $this->postJson('/api/posts', [
            'title' => 'Test',
            'text' => 'Content'
        ])->assertStatus(401);
    }

    public function test_user_can_get_all_posts()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $post1 = Post::create(['title' => 'Post 1 ' . uniqid(), 'text' => 'Content 1', 'user_id' => $user1->id]);
        $post2 = Post::create(['title' => 'Post 2 ' . uniqid(), 'text' => 'Content 2', 'user_id' => $user2->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/posts')
            ->assertStatus(200);

        $response->assertJsonFragment(['title' => $post1->title])
                 ->assertJsonFragment(['title' => $post2->title]);
    }

    public function test_user_can_get_their_posts()
    {
        $myUniqueTitle = 'My Unique Post ' . uniqid();
        $otherUniqueTitle = 'Other Unique Post ' . uniqid();
        
        Post::create([
            'title' => $myUniqueTitle,
            'text' => 'My Content', 
            'user_id' => $this->user->id
        ]);
        
        $otherUser = User::factory()->create();
        Post::create([
            'title' => $otherUniqueTitle,
            'text' => 'Other Content',
            'user_id' => $otherUser->id
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/my-posts')
            ->assertStatus(200);

        $postsData = $response->json('data');
        
        foreach ($postsData as $post) {
            $this->assertEquals($this->user->id, $post['user_id']);
        }
        
        $response->assertJsonFragment(['title' => $myUniqueTitle]);
    }
}